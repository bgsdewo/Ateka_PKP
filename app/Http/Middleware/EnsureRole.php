<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userRole = strtolower($user->role ?? '');

        // Support multiple roles passed as comma-separated or parameters
        $allowedRoles = [];
        foreach ($roles as $roleGroup) {
            foreach (explode(',', $roleGroup) as $r) {
                $trimmed = strtolower(trim($r));
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        // 'admin' is treated as a superuser alias of 'kasubag_tu'
        if (in_array('kasubag_tu', $allowedRoles) && !in_array('admin', $allowedRoles)) {
            $allowedRoles[] = 'admin';
        }

        if (!in_array($userRole, $allowedRoles)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Anda tidak memiliki wewenang untuk aksi ini.'
                ], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak: Akun Anda tidak memiliki hak akses untuk membuka halaman tersebut.');
        }

        return $next($request);
    }
}
