<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\ReceivedEmail;
use Carbon\Carbon;

class ReceiveEmails extends Command
{
    protected $signature = 'mail:receive';
    protected $description = 'Consulta el buzón IMAP, procesa los mensajes nuevos, limpia los de más de una semana y los guarda en la base de datos';

    public function handle(): int
    {
        $this->info('Iniciando limpieza y sincronización de buzón IMAP...');

        try {
            $deletedCount = ReceivedEmail::where('received_at', '<', Carbon::now()->subDays(7))->delete();
            if ($deletedCount > 0) {
                $this->line("Se eliminaron {$deletedCount} correos antiguos de más de 7 días de la base de datos.");
            }

            $client = Client::account('default');
            $client->connect();
            
            $folder = $client->getFolder('INBOX');
            $messages = $folder->messages()->unseen()->limit(20)->get();

            foreach ($messages as $message) {
                $receivedAt = Carbon::parse($message->getDate());

                if ($receivedAt->lt(Carbon::now()->subDays(7))) {
                    $message->setFlag(['Seen']);
                    continue;
                }

                $messageId = $message->getMessageId();
                $from = $message->getFrom()[0];
                
                $fromEmail = $from->mail ?? 'desconocido@domain.com';
                $fromName = $from->personal ?? null;
                $subject = $message->getSubject();
                $body = $message->getHTMLBody() ?: $message->getTextBody();

                ReceivedEmail::firstOrCreate(
                    ['message_id' => $messageId],
                    [
                        'from_email' => $fromEmail,
                        'from_name' => $fromName,
                        'subject' => $subject,
                        'body' => $body,
                        'received_at' => $receivedAt,
                        'is_read' => false,
                    ]
                );

                $message->setFlag(['Seen']);
                $this->line("Correo procesado y guardado: {$subject}");
            }

            $this->info('¡Revisión y sincronización de buzón completada con éxito!');
        } catch (\Exception $e) {
            $this->error('Error al conectar con IMAP: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}