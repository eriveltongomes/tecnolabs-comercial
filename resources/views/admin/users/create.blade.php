<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Criar Novo Usuário') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Exibe erros de validação -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                        </div>

                        <!-- Role (Perfil) -->
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Perfil (Role)')" />
                            <select name="role" id="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Selecione um perfil</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="financeiro" {{ old('role') == 'financeiro' ? 'selected' : '' }}>Financeiro</option>
                                <option value="comercial" {{ old('role') == 'comercial' ? 'selected' : '' }}>Comercial</option>
                                <option value="tecnico" {{ old('role') == 'tecnico' ? 'selected' : '' }}>Técnico</option>
                            </select>
                        </div>

                        <!-- SARPAS (ID) -->
                        <div class="mt-4">
                        <x-input-label for="decea_profile_id" :value="__('ID DECEA / SARPAS (Para Pilotos)')" />
                        <x-text-input id="decea_profile_id" class="block mt-1 w-full" type="text" name="decea_profile_id" :value="old('decea_profile_id')" placeholder="Ex: FZBH" />
                         </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full"
                                            type="password"
                                            name="password"
                                            required autocomplete="new-password" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                            type="password"
                                            name="password_confirmation" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancelar') }}
                            </a>
                            
                            <x-primary-button>
                                {{ __('Criar Usuário') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>