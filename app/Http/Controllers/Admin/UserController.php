<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Storage; // <--- Importante para deletar fotos antigas

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,financeiro,comercial,tecnico'],
            'profile_photo' => ['nullable', 'image', 'max:2048'], // Validação da foto
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ];

        // Upload da Foto na Criação
        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,financeiro,comercial,tecnico'],
            'profile_photo' => ['nullable', 'image', 'max:2048'], // Validação da foto
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        // --- LÓGICA DE UPLOAD DE FOTO (ADMIN) ---
        if ($request->hasFile('profile_photo')) {
            // 1. Deleta a antiga se existir
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            // 2. Salva a nova
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }
        // ----------------------------------------

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        // Deleta a foto ao remover o usuário para não deixar lixo no servidor
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Usuário excluído.');
    }
}