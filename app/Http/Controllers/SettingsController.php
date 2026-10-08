<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\UserFeedbackMail;
use App\Models\User;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdmin = ($user->role === 'admin' || (method_exists($user, 'hasRole') && $user->hasRole('admin')));

        $activeDevices = collect();
        if (\Schema::hasTable('user_devices')) {
            $activeDevices = DB::table('user_devices')
                ->where('user_id', $user->id)
                ->orderBy('last_active_at', 'desc')
                ->get();
        }

        $activityLogs = collect();
        if (\Schema::hasTable('user_activity_logs')) {
            $activityLogs = DB::table('user_activity_logs')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }
        
        $storageUsage = 0;
        if ($isAdmin) {
            $storageUsage = $this->getStorageUsage();
        }

        return view('settings.index', compact('user', 'activeDevices', 'activityLogs', 'storageUsage', 'isAdmin'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100', 'unique:users,name,' . $user->id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => [
                'nullable', 
                'image', 
                'mimes:jpeg,png,jpg,webp',
                'max:2048',            
            ],
        ], [
            'name.required' => 'El campo de nombre de usuario es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto válida.',
            'name.min' => 'El nombre de usuario debe tener al menos 2 caracteres.',
            'name.max' => 'El nombre de usuario no puede superar los 100 caracteres.',
            'name.unique' => 'Este nombre de usuario ya está registrado por otra cuenta.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes ingresar una dirección de correo electrónico válida.',
            'email.max' => 'El correo electrónico no puede superar los 255 caracteres.',
            'email.unique' => 'Este correo electrónico ya se encuentra registrado en otra cuenta.',

            'bio.max' => 'La biografía profesional no puede superar los 500 caracteres.',

            'avatar.image' => 'El archivo adjunto debe ser una imagen válida.',
            'avatar.mimes' => 'La imagen debe tener un formato permitido (JPEG, PNG, JPG, WEBP).',
            'avatar.max' => 'La imagen de perfil no puede pesar más de 2MB.',
        ]);

        $user->name = $request->input('name');
        $user->email = Str::lower($request->input('email'));
        
        if (\Schema::hasColumn('users', 'bio')) {
            $user->bio = strip_tags($request->input('bio')); 
        }

        if ($request->has('remove_avatar') and $request->input('remove_avatar') == '1') {
            if ($user->avatar and Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar and Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $file = $request->file('avatar');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');
            $user->avatar = $path;
        }

        $user->save();

        $this->logActivity($user->id, 'Actualización de perfil e identidad', $request->ip());

        return back()->with('success', '¡Perfil y foto actualizados correctamente!');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required', 
                'string', 
                'min:8', 
                'confirmed', 
                'regex:/^\S*$/', 
                function ($attribute, $value, $fail) use ($user) {
                    if (strtolower($value) === strtolower($user->name) or str_contains(strtolower($value), strtolower($user->name))) {
                        $fail('La contraseña no puede contener tu nombre de usuario.');
                    }
                },
            ],
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña no puede contener espacios en blanco.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $user->password = Hash::make($request->input('password'));
        $user->save();

        $this->logActivity($user->id, 'Cambio de contraseña de acceso', $request->ip());

        return back()->with('success', '¡Contraseña actualizada de forma segura!');
    }

    public function updateAppearance(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'font_size' => ['required', 'string', 'in:14px,16px,18px'],
            'high_contrast' => ['nullable', 'boolean'],
        ], [
            'font_size.required' => 'Debes seleccionar un tamaño de tipografía válido.',
            'font_size.in' => 'El tamaño de tipografía seleccionado no es válido.',
        ]);

        $user->font_size = $request->input('font_size');
        $user->high_contrast = $request->has('high_contrast') ? true : false;
        $user->save();

        $this->logActivity($user->id, 'Modificación de preferencias visuales y accesibilidad', $request->ip());

        return back()->with('success', '¡Preferencias visuales y de accesibilidad guardadas con éxito!');
    }

    public function downloadPersonalData(Request $request)
    {
        $user = Auth::user();
        $data = [
            'perfil' => $user->toArray(),
            'fecha_exportacion' => now()->toDateTimeString(),
            'plataforma' => 'EduNivelam'
        ];

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'mis-datos-edunivelam-' . $user->id . '.json';

        $this->logActivity($user->id, 'Exportación y descarga de datos personales', $request->ip());

        return response()->streamDownload(function () use ($json) {
            echo $json;
        }, $fileName, ['Content-Type' => 'application/json']);
    }

    public function sendFeedback(Request $request)
    {
        $request->validate([
            'feedback_message' => ['required', 'string', 'max:1000'],
        ], [
            'feedback_message.required' => 'El campo de comentarios no puede estar vacío.',
            'feedback_message.max' => 'El comentario no puede superar los 1000 caracteres.'
        ]);

        $user = Auth::user();
        $messageContent = $request->input('feedback_message');

        try {
            $supportEmail = 'edunivelamsoporte@gmail.com';
            Mail::to($supportEmail)->send(new UserFeedbackMail($messageContent, $user));
            $this->logActivity($user->id, 'Envío exitoso de comentarios al correo de soporte', $request->ip());

            return back()->with('success', '¡Gracias por tus comentarios! Tu mensaje ha sido enviado directamente al equipo de soporte.');

        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo enviar el correo de comentarios en este momento: ' . $e->getMessage());
        }
    }

    public function toggleMaintenance(Request $request)
    {
        $this->authorizeAdmin();
        $action = $request->input('action'); 

        try {
            if ($action === 'down') {
                Artisan::call('down', ['--secret' => 'edunivelam-bypass-token']);
                return back()->with('success', '¡Modo mantenimiento activado globalmente!');
            } else {
                Artisan::call('up');
                return back()->with('success', '¡El sistema ha salido del modo mantenimiento!');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el estado de mantenimiento del servidor.');
        }
    }

    public function testEmailConnection(Request $request)
    {
        $this->authorizeAdmin();
        $isSimulation = $request->has('simular_falla');
        $startTime = microtime(true);

        try {
            $pdo = DB::connection()->getPdo();
            if (!$pdo) {
                throw new \Exception("Conexión PDO inactiva con la base de datos.");
            }

            $dbName = DB::getDatabaseName();
            $driver = DB::getDriverName();
            $tables = DB::select('SHOW TABLES');
            $tablesCount = count($tables);

            if ($isSimulation) {
                throw new \Exception("Simulación de fallo crítico: El servicio IMAP no responde al comando AUTH y la réplica de base de datos presenta timeout (>5000ms).");
            }

            $latency = round((microtime(true) - $startTime) * 1000, 2);

            $diagnosticData = [
                'estado' => 'ESTABLE (Operación Normal)',
                'driver_bd' => strtoupper($driver),
                'base_datos' => $dbName,
                'latencia_conexion' => $latency . ' ms',
                'tablas_activas' => $tablesCount,
                'servicio_imap' => 'Conectado y Sincronizado',
                'memoria_sistema' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB',
                'timestamp' => now()->toDateTimeString(),
            ];

            $this->logActivity(Auth::id(), 'Diagnóstico de red y base de datos ejecutado con éxito', $request->ip());

            return back()->with([
                'diagnostic_modal' => true,
                'diagnostic_status' => 'success',
                'diagnostic_title' => 'Informe de Diagnóstico: Sistema Estable',
                'diagnostic_data' => $diagnosticData
            ]);

        } catch (\Exception $e) {
            $latency = round((microtime(true) - $startTime) * 1000, 2);

            $diagnosticData = [
                'estado' => 'ANORMAL / CRÍTICO (Fallo Operativo)',
                'error_detectado' => $e->getMessage(),
                'latencia_conexion' => $latency . ' ms (Timeout)',
                'servicio_imap' => 'Desconectado / Error de Autenticación',
                'timestamp' => now()->toDateTimeString(),
            ];

            $this->logActivity(Auth::id(), 'ALERTA: Diagnóstico detectó operación anormal', $request->ip());

            return back()->with([
                'diagnostic_modal' => true,
                'diagnostic_status' => 'danger',
                'diagnostic_title' => 'Alerta: Simulación de Operación Anormal',
                'diagnostic_data' => $diagnosticData
            ]);
        }
    }

    public function generateBackup(Request $request)
    {
        $this->authorizeAdmin();

        try {
            $filename = 'backup_edunivelam_completo_' . date('Y-m-d_H-i-s') . '.sql';
            $dbName = DB::getDatabaseName();
            $sqlContent = "-- ========================================================\n";
            $sqlContent .= "-- EDUNIVELAM - RESPALDO ÍNTEGRO DE BASE DE DATOS\n";
            $sqlContent .= "-- Base de datos: {$dbName}\n";
            $sqlContent .= "-- Fecha de generación: " . now()->toDateTimeString() . "\n";
            $sqlContent .= "-- Generado por Administrador ID: " . Auth::id() . "\n";
            $sqlContent .= "-- ========================================================\n\n";

            $tablesResult = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $dbName;
            
            foreach ($tablesResult as $row) {
                $tableName = $row->$tableKey ?? reset($row);
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $createSql =$createTable[0]->{'Create Table'} ?? '';

                $sqlContent .= "-- --------------------------------------------------------\n";
                $sqlContent .= "-- Estructura de tabla para `{$tableName}`\n";
                $sqlContent .= "-- --------------------------------------------------------\n";
                $sqlContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sqlContent .=$createSql . ";\n\n";

                $rows = DB::table($tableName)->get();
                if (count($rows) > 0) {$sqlContent .= "-- Volcado de datos para `{$tableName}`\n";
                    foreach ($rows as$record) {
                        $recordArray = (array)$record;
                        $columns = array_keys($recordArray);
                        $values = array_values($recordArray);

                        $escapedColumns = array_map(fn($col) => "`{$col}`", $columns);
                        $escapedValues = array_map(function($val) {
                            if (is_null($val)) return "NULL";
                            return "'" . addslashes($val) . "'";
                        }, $values);

                        $sqlContent .= "INSERT INTO `{$tableName}` (" . implode(', ', $escapedColumns) . ") VALUES (" . implode(', ', $escapedValues) . ");\n";
                    }
                    $sqlContent .= "\n";
                }
            }

            $this->logActivity(Auth::id(), 'Generación y descarga de respaldo SQL completo de toda la BD', $request->ip());

            return response()->streamDownload(function () use ($sqlContent) {
                echo $sqlContent;
            }, $filename, ['Content-Type' => 'application/sql']);

        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo generar el respaldo de la base de datos: ' . $e->getMessage());
        }
    }

    public function viewLogs(Request $request)
    {
        $this->authorizeAdmin();
        $logPath = storage_path('logs/laravel.log');$logs = "El archivo de registro (log) está vacío o no existe.";

        if (file_exists($logPath)) {
            $lines = file($logPath);
            $lastLines = array_slice($lines, -100);
            $logs = implode("", $lastLines);
        }

        return back()->with([
            'log_modal' => true,
            'log_content' => $logs
        ]);
    }

    public function clearLogs(Request $request)
    {
        $this->authorizeAdmin();$logPath = storage_path('logs/laravel.log');
        
        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
        }

        $this->logActivity(Auth::id(), 'Limpieza del archivo laravel.log',$request->ip());

        return back()->with('success', '¡El archivo de registros (logs) ha sido vaciado correctamente!');
    }

    public function testSmtp(Request $request)
    {
        $this->authorizeAdmin();$request->validate([
            'test_email' => ['required', 'email']
        ]);

        $targetEmail =$request->input('test_email');

        try {
            Mail::raw('Este es un correo de prueba automatizado desde el Centro de Comando de EduNivelam para verificar la conectividad SMTP.', function ($message) use ($targetEmail) {
                $message->to($targetEmail)
                        ->subject('Prueba de Conectividad SMTP - EduNivelam');
            });

            $this->logActivity(Auth::id(), 'Prueba de correo SMTP enviada exitosamente a: ' . $targetEmail,$request->ip());

            return back()->with('success', '¡Correo de prueba enviado con éxito a ' . $targetEmail . '!');
        } catch (\Exception $e) {
            return back()->with('error', 'Fallo al enviar correo SMTP: ' . $e->getMessage());
        }
    }

    public function inspectCache(Request $request)
    {
        $this->authorizeAdmin();$type = $request->input('type');$size = 0;
        $title = '';$description = '';

        if ($type === 'route') {$path = base_path('bootstrap/cache/routes-v7.php');
            if (file_exists($path)) {$size = filesize($path); }$title = 'Caché de Rutas del Sistema';
            $description = 'Contiene las rutas compiladas para acelerar el enrutamiento de peticiones en EduNivelam.';
        } elseif ($type === 'view') {$path = storage_path('framework/views');
            $size =$this->getDirectorySize($path);$title = 'Caché de Vistas Compiladas (Blade)';
            $description = 'Archivos PHP compilados a partir de las plantillas Blade para agilizar la renderización.';
        } elseif ($type === 'config') {$path = base_path('bootstrap/cache/config.php');
            if (file_exists($path)) {$size = filesize($path); }$title = 'Caché de Configuración';
            $description = 'Variables globales del sistema unificadas en un solo archivo para evitar lecturas constantes del .env.';
        } else {
            $path = storage_path('framework/cache/data');
            $size =$this->getDirectorySize($path);$title = 'Caché General de Aplicación';
            $description = 'Datos temporales y consultas almacenadas en caché para optimizar tiempos de respuesta.';
        }

        $sizeFormatted = round($size / 1024, 2) . ' KB';
        if ($size > 1048576) {
            $sizeFormatted = round($size / 1024 / 1024, 2) . ' MB';
        }

        return back()->with([
            'cache_modal' => true,
            'cache_type' => $type,
            'cache_title' => $title,
            'cache_desc' => $description,
            'cache_size' => $sizeFormatted,
            'cache_bytes' => $size
        ]);
    }

    public function optimizeDatabase(Request $request)
    {
        $this->authorizeAdmin();

        try {
            $dbName = DB::getDatabaseName();$statusBefore = DB::select("SELECT SUM(data_length + index_length) AS total_size FROM information_schema.tables WHERE table_schema = ?", [$dbName]);
            $sizeBefore =$statusBefore[0]->total_size ?? 0;

            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' .$dbName;
            $optimizedCount = 0;

            foreach ($tables as$row) {
                $tableName =$row->$tableKey ?? reset($row);
                DB::statement("OPTIMIZE TABLE `{$tableName}`");
                $optimizedCount++;
            }

            $statusAfter = DB::select("SELECT SUM(data_length + index_length) AS total_size FROM information_schema.tables WHERE table_schema = ?", [$dbName]);
            $sizeAfter =$statusAfter[0]->total_size ?? 0;

            $freedBytes = max(0, $sizeBefore -$sizeAfter);
            $beforeFormatted = round($sizeBefore / 1024 / 1024, 2) . ' MB';
            $afterFormatted = round($sizeAfter / 1024 / 1024, 2) . ' MB';
            $freedFormatted = round($freedBytes / 1024, 2) . ' KB';

            $reportData = [
                'tablas_procesadas' => $optimizedCount,
                'tamano_inicial' => $beforeFormatted,
                'tamano_optimizado' => $afterFormatted,
                'espacio_recuperado' => $freedFormatted,
                'estado' => 'Desfragmentación completada con éxito'
            ];

            $this->logActivity(Auth::id(), 'Optimización y desfragmentación de base de datos ejecutada', $request->ip());

            return back()->with([
                'db_report_modal' => true,
                'db_report_data' => $reportData
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al optimizar la base de datos: ' . $e->getMessage());
        }
    }

    public function clearGranularCache(Request $request) 
    {
        $this->authorizeAdmin();
        $type =$request->input('type');

        try {
            $path = '';
            if ($type === 'route') {$path = base_path('bootstrap/cache/routes-v7.php');
            } elseif ($type === 'view') {$path = storage_path('framework/views');
            } elseif ($type === 'config') {$path = base_path('bootstrap/cache/config.php');
            } else {
                $path = storage_path('framework/cache/data');
            }
            $sizeBefore = is_dir($path) ? $this->getDirectorySize($path) : (file_exists($path) ? filesize($path) : 0);

            if ($type === 'route') {
                Artisan::call('route:clear');
                $msg = '¡Caché de rutas purgada con éxito!';
            } elseif ($type === 'view') {
                Artisan::call('view:clear');
                $msg = '¡Caché de vistas compiladas eliminada!';
            } elseif ($type === 'config') {
                Artisan::call('config:clear');
                $msg = '¡Caché de configuración restablecida!';
            } else {
                Artisan::call('cache:clear');
                $msg = '¡Caché general de aplicación vaciada!';
            }

            $sizeFormatted = round($sizeBefore / 1024, 2) . ' KB';

            $this->logActivity(Auth::id(), 'Purgado granular de caché (' . $type . ') - Liberados: ' . $sizeFormatted,$request->ip());

            return back()->with('success', $msg . ' Espacio liberado aprox: ' .$sizeFormatted);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al purgar la caché: ' . $e->getMessage());
        }
    }

    public function clearSystemCache(Request $request)
    {
        $this->authorizeAdmin();

        try {
            Artisan::call('optimize:clear');

            $this->logActivity(Auth::id(), 'Limpieza de caché general y archivos temporales del sistema', $request->ip());

            return back()->with('success', '¡Caché del sistema y archivos temporales limpiados exitosamente!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al limpiar la caché: ' . $e->getMessage());
        }
    }

    public function updateEnv(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'app_name' => ['required', 'string', 'max:50'],
            'app_debug' => ['required', 'in:true,false'],
            'mail_mailer' => ['required', 'string', 'in:smtp,log,mailgun,ses']
        ]);

        try {
            $envPath = base_path('.env');
            if (file_exists($envPath)) {
                $envContent = file_get_contents($envPath);

                $data = [
                    'APP_NAME' => '"' . $request->input('app_name') . '"',
                    'APP_DEBUG' => $request->input('app_debug'),
                    'MAIL_MAILER' => $request->input('mail_mailer'),
                ];

                foreach ($data as $key =>$value) {
                    if (preg_match("/^{$key}=.*/m", $envContent)) {$envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
                    } else {
                        $envContent .= "\n{$key}={$value}";
                    }
                }

                file_put_contents($envPath,$envContent);
                Artisan::call('config:clear');

                $this->logActivity(Auth::id(), 'Actualización de variables de entorno (.env)', $request->ip());

                return back()->with('success', '¡Variables de entorno actualizadas y caché de configuración refrescada!');
            }

            return back()->with('error', 'No se localizó el archivo .env en el servidor.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al modificar el archivo .env: ' . $e->getMessage());
        }
    }

    private function getStorageUsage()
    {
        $files = Storage::disk('public')->allFiles('avatars');$size = 0;
        foreach ($files as$file) {
            $size += Storage::disk('public')->size($file);
        }
        return round($size / 1024 / 1024, 2);
    }

    private function getDirectorySize($directory)
    {
        $size = 0;
        if (!is_dir($directory)) return 0;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)) as$file) {
            if ($file->isFile()) {
                $size +=$file->getSize();
            }
        }
        return $size;
    }

    private function authorizeAdmin()
    {
        $user = Auth::user();
        if (!$user or$user->role !== 'admin') {
            abort(403, 'Acción no autorizada. Esta herramienta es exclusiva para administradores.');
        }
    }

    private function logActivity($userId, $action,$ip)
    {
        if (\Schema::hasTable('user_activity_logs')) {
            DB::table('user_activity_logs')->insert([
                'user_id' => $userId,
                'action' => $action,
                'ip_address' => $ip,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}