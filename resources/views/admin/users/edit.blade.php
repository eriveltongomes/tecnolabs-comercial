<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Usuário') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <label for="name" class="block font-medium text-sm text-gray-700">Nome</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="email" class="block font-medium text-sm text-gray-700">E-mail</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="role" class="block font-medium text-sm text-gray-700">Função (Nível de Acesso)</label>
                                <select name="role" id="role" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin (Acesso Total)</option>
                                    <option value="financeiro" {{ $user->role == 'financeiro' ? 'selected' : '' }}>Financeiro (Propostas e Relatórios)</option>
                                    <option value="comercial" {{ $user->role == 'comercial' ? 'selected' : '' }}>Comercial (Vendas e Clientes)</option>
                                    <option value="tecnico" {{ $user->role == 'tecnico' ? 'selected' : '' }}>Técnico (Piloto - Apenas App)</option>
                                </select>
                                @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700 mb-2">Foto de Perfil (Avatar)</label>
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0">
                                        @if($user->profile_photo)
                                            <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Foto" class="h-16 w-16 rounded-full object-cover border border-gray-300 shadow-sm">
                                        @else
                                            <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 font-bold border border-gray-300">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <input type="file" name="profile_photo" class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-indigo-50 file:text-indigo-700
                                        hover:file:bg-indigo-100"
                                        accept="image/*">
                                </div>
                                @error('profile_photo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2 border-t pt-4 mt-2">
                                <h3 class="text-md font-medium text-gray-900 mb-4">Alterar Senha (Opcional)</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="password" class="block font-medium text-sm text-gray-700">Nova Senha</label>
                                        <input type="password" name="password" id="password" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" placeholder="Deixe em branco para manter a atual">
                                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirmar Senha</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-8">
                            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>