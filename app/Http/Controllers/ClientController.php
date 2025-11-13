<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importamos o Auth para pegar o usuário logado

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Lógica de Permissão:
        // Admin/Financeiro veem todos. Comercial vê apenas os seus.
        if (in_array($user->role, ['admin', 'financeiro'])) {
            $clients = Client::with('creator')->get(); // 'creator' é o relacionamento com User
        } else {
            // Se for 'comercial', pega apenas os que ele criou
            $clients = Client::where('created_by_user_id', $user->id)->get();
        }

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:255', 'unique:'.Client::class], // CNPJ/CPF é opcional, mas único
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'contact_name' => ['nullable', 'string', 'max:255'],
        ]);

        // Adiciona automaticamente o ID do usuário logado como o "criador"
        $data['created_by_user_id'] = Auth::id();

        Client::create($data);

        return redirect()->route('clients.index')->with('success', 'Cliente criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        // Não vamos usar a tela 'show'
        return redirect()->route('clients.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        // Verifica se o Comercial é o dono do cliente
        $this->authorizeOwner($client);
        
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        // Verifica se o Comercial é o dono do cliente
        $this->authorizeOwner($client);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:255', 'unique:'.Client::class.',document,'.$client->id],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'contact_name' => ['nullable', 'string', 'max:255'],
        ]);

        $client->update($data);

        return redirect()->route('clients.index')->with('success', 'Cliente atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        // Verifica se o Comercial é o dono do cliente
        $this->authorizeOwner($client);
        
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Cliente excluído com sucesso.');
    }

    /**
     * Método auxiliar para checar permissão
     */
    private function authorizeOwner(Client $client)
    {
        $user = Auth::user();
        // Se o usuário for 'comercial' E o ID do criador do cliente for DIFERENTE do ID dele
        if ($user->role === 'comercial' && $client->created_by_user_id !== $user->id) {
            // Aborta a operação com erro 403 (Não Autorizado)
            abort(403, 'Acesso não autorizado.');
        }
    }
}