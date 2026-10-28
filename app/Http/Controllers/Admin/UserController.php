<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private const ROLE_LABELS = [
        'admin'   => 'Administrador',
        'teacher' => 'Docente',
        'student' => 'Estudiante',
        'user'    => 'Usuario',
    ];

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');
        if (! in_array($tab, ['all', 'student', 'teacher', 'admin'], true)) {
            $tab = 'all';
        }

        $query = User::query();
        if ($tab !== 'all') {
            $query->where('role', $tab);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        $roleCounts = User::select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $countAll   = (int) $roleCounts->sum();
        $boardUsers = User::orderBy('name')->get(['id', 'name', 'email', 'role']);

        $all    = User::get(['id', 'role', 'is_active', 'created_at']);
        $stats  = $this->buildStats($all);
        $charts = $this->buildCharts($all);
        $roleLabels = self::ROLE_LABELS;

        return view('admin.users.index', compact(
            'users', 'tab', 'countAll', 'roleCounts', 'boardUsers', 'stats', 'charts', 'roleLabels'
        ));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users')
                ->with('error', 'Por seguridad, no puedes modificar el estado de tu propia cuenta activa.');
        }

        $user->is_active = !($user->is_active ?? true);
        $user->save();

        $statusMessage = $user->is_active ? 'activada' : 'desactivada';

        return redirect()->route('admin.users')
            ->with('success', "La cuenta del usuario ha sido {$statusMessage} con éxito.");
    }

    public function forceDelete($id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users')
                ->with('error', 'Por seguridad, no puedes eliminar tu propia cuenta activa.');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'La cuenta del usuario ha sido eliminada permanentemente.');
    }

    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role' => ['required', 'string', Rule::in(User::ROLES)],
        ]);

        if (Auth::id() === $user->id && $request->role !== 'admin') {
            return redirect()->route('admin.users')
                ->with('error', 'Por seguridad, no puedes quitarte el rol de administrador a ti mismo.');
        }

        $user->role = $request->role;
        $user->save();

        return redirect()->route('admin.users')
            ->with('success', "El rol de {$user->name} ha sido actualizado con éxito.");
    }

    private function buildStats(Collection $all): array
    {
        $thisStart = now()->startOfMonth();
        $prevStart = $thisStart->copy()->subMonthNoOverflow();

        $newThis = $all->filter(fn ($u) => $u->created_at && $u->created_at->gte($thisStart))->count();
        $newPrev = $all->filter(fn ($u) => $u->created_at
            && $u->created_at->gte($prevStart) && $u->created_at->lt($thisStart))->count();

        $active = $all->filter(fn ($u) => (bool) ($u->is_active ?? true))->count();

        return [
            'total'    => $all->count(),
            'newMonth' => $newThis,
            'growth'   => $newPrev > 0 ? (int) round((($newThis - $newPrev) / $newPrev) * 100) : null,
            'active'   => $active,
            'inactive' => $all->count() - $active,
        ];
    }

    private function buildCharts(Collection $all): array
    {
        $now = now();
        $days  = collect(range(29, 0))->map(fn ($i) => $now->copy()->subDays($i)->startOfDay());
        $byDay = $all->groupBy(fn ($u) => $u->created_at ? $u->created_at->format('Y-m-d') : '');
        $months  = collect(range(11, 0))->map(fn ($i) => $now->copy()->startOfMonth()->subMonthsNoOverflow($i));
        $byMonth = $all->groupBy(fn ($u) => $u->created_at ? $u->created_at->format('Y-m') : '');

        $roleKeys = ['student', 'teacher', 'admin'];

        $roleMonthly = [];
        foreach ($roleKeys as $role) {
            $roleMonthly[] = [
                'role'  => $role,
                'label' => self::ROLE_LABELS[$role],
                'data'  => $months->map(fn ($m) => $byMonth
                    ->get($m->format('Y-m'), collect())->where('role', $role)->count())->all(),
            ];
        }

        $roleDist = [];
        foreach (['admin', 'teacher', 'student', 'user'] as $role) {
            $roleDist[] = [
                'role'  => $role,
                'label' => self::ROLE_LABELS[$role],
                'total' => $all->where('role', $role)->count(),
            ];
        }

        $active = $all->filter(fn ($u) => (bool) ($u->is_active ?? true))->count();

        return [
            'daily' => [
                'labels' => $days->map(fn ($d) => $d->format('d/m'))->all(),
                'data'   => $days->map(fn ($d) => $byDay->get($d->format('Y-m-d'), collect())->count())->all(),
            ],
            'monthly' => [
                'labels' => $months->map(fn ($m) => $m->translatedFormat('M Y'))->all(),
                'data'   => $months->map(fn ($m) => $byMonth->get($m->format('Y-m'), collect())->count())->all(),
            ],
            'roleMonthly' => $roleMonthly,
            'roles'       => $roleDist,
            'status'      => ['active' => $active, 'inactive' => $all->count() - $active],
        ];
    }
}