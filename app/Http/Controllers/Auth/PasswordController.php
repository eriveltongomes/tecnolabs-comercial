<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // --- A CORREÇÃO ESTÁ AQUI ---
        // ANTES (ERRADO):
        // $request->user()->update([
        //     'password' => Hash::make($validated['password']),
        // ]);
        
        // AGORA (CORRETO):
        // Nós apenas passamos a senha pura. O Model (User.php) 
        // vai interceptar e fazer a criptografia automaticamente.
        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return back()->with('status', 'password-updated');
    }
}