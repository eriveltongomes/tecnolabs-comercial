<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,financeiro,comercial,tecnico'],
            'decea_profile_id' => ['nullable', 'string', 'max:50'], // NOVO
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // O Mutator do Model faz o Hash
            'role' => $request->role,
            'decea_profile_id' => $request->decea_profile_id, // NOVO
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'role' => ['required', 'in:admin,financeiro,comercial,tecnico'],
            'decea_profile_id' => ['nullable', 'string', 'max:50'], // NOVO
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = $request->only('name', 'email', 'role', 'decea_profile_id');

        if ($request->filled('password')) {
            // Aqui precisamos fazer o hash manual ou confiar no mutator se setar a propriedade
            $user->password = $request->password; 
        }
        
        // Update seguro
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->decea_profile_id = $data['decea_profile_id'];
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) return redirect()->route('admin.users.index')->with('error', 'Erro.');
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Usuário excluído.');
    }
}