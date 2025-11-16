<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center space-x-2">
            
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            
            <span>
                {{ __('Cadastrar Cliente') }}
            </span>
        </h2>
    </x-slot>

    <div class="py-12" x-data="clientForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('clients.store') }}">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Nome / Razão Social')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="document" :value="__('CNPJ / CPF (Somente números)')" />
                            <x-text-input id="document" x-model="document" @input="maskDocument" class="block mt-1 w-full" type="text" name="document" :value="old('document')" placeholder="000.000.000-00 ou 00.000.000/0000-00" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="contact_name" :value="__('Nome do Contato (Opcional)')" />
                            <x-text-input id="contact_name" class="block mt-1 w-full" type="text" name="contact_name" :value="old('contact_name')" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email (Opcional)')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="phone" :value="__('Telefone (Opcional)')" />
                            <x-text-input id="phone" x-model="phone" @input="maskPhone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" placeholder="(00) 00000-0000" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('clients.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancelar') }}
                            </a>
                            
                            <x-primary-button>
                                {{ __('Salvar Cliente') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function clientForm() {
            return {
                document: '{{ old('document') }}',
                phone: '{{ old('phone') }}',

                maskDocument() {
                    // Remove tudo que não é dígito
                    let v = this.document.replace(/\D/g, '');
                    
                    // Limita ao tamanho máximo de um CNPJ (14 dígitos)
                    if (v.length > 14) v = v.substring(0, 14);

                    // Aplica a máscara dependendo do tamanho
                    if (v.length <= 11) {
                        // CPF: 000.000.000-00
                        v = v.replace(/(\d{3})(\d)/, '$1.$2');
                        v = v.replace(/(\d{3})(\d)/, '$1.$2');
                        v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                    } else {
                        // CNPJ: 00.000.000/0000-00
                        v = v.replace(/^(\d{2})(\d)/, '$1.$2');
                        v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                        v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
                        v = v.replace(/(\d{4})(\d)/, '$1-$2');
                    }
                    
                    this.document = v;
                },

                maskPhone() {
                    // Remove não dígitos
                    let v = this.phone.replace(/\D/g, '');
                    
                    // Limita a 11 dígitos
                    if (v.length > 11) v = v.substring(0, 11);

                    // Formato (99) 99999-9999
                    v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
                    v = v.replace(/(\d)(\d{4})$/, '$1-$2');
                    
                    this.phone = v;
                }
            }
        }
    </script>
</x-app-layout>