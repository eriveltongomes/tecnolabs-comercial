<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  ...$roles (array de perfis permitidos)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Verifica se o usuário está logado E se o 'role' dele está na lista de $roles permitidos
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            
            // Se não tiver permissão, redireciona para o dashboard com um erro
            return redirect('dashboard')->with('error', 'Acesso não autorizado.');
        }

        // Se tiver permissão, continua
        return $next($request);
    }
}