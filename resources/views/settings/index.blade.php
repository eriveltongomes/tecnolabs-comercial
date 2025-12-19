<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            ⚙️ Painel de Configurações
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-10">
                <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2 border-b border-gray-200 pb-2">
                    <span class="bg-indigo-100 text-indigo-600 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    Estratégia Comercial
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <a href="{{ route('settings.monthly-goals.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-indigo-200 transition-all duration-300">
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1 group-hover:text-indigo-600">Metas da Empresa</h4>
                        <p class="text-sm text-gray-500">Defina o faturamento global mensal que a equipe deve perseguir.</p>
                    </a>

                    <a href="{{ route('settings.revenue-tiers.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-indigo-200 transition-all duration-300">
                        <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1 group-hover:text-purple-600">Faixas de Faturamento</h4>
                        <p class="text-sm text-gray-500">Configure os níveis (Bronze, Prata, Ouro) e tetos para comissão.</p>
                    </a>

                    <a href="{{ route('settings.channels.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-indigo-200 transition-all duration-300">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1 group-hover:text-blue-600">Canais de Venda</h4>
                        <p class="text-sm text-gray-500">Gerencie as origens dos leads (Instagram, Google, Indicação).</p>
                    </a>

                    <a href="{{ route('settings.commission-rules.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-indigo-200 transition-all duration-300">
                        <div class="w-12 h-12 bg-green-50 text-green-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-green-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1 group-hover:text-green-600">Regras de Comissão</h4>
                        <p class="text-sm text-gray-500">Defina a % exata que o vendedor ganha por combinação de Canal/Meta.</p>
                    </a>

                </div>
            </div>

            <div class="mb-10">
                <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2 border-b border-gray-200 pb-2">
                    <span class="bg-yellow-100 text-yellow-600 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </span>
                    Base de Cálculo (Precificação)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <a href="{{ route('settings.equipment.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-yellow-200 transition-all duration-300">
                        <div class="w-12 h-12 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-gray-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1 group-hover:text-gray-700">Equipamentos</h4>
                        <p class="text-sm text-gray-500">Drones, câmeras e hardware. Define o custo de depreciação/uso.</p>
                    </a>

                    <a href="{{ route('settings.fixed-costs.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-yellow-200 transition-all duration-300">
                        <div class="w-12 h-12 bg-red-50 text-red-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-red-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1 group-hover:text-red-600">Custos Fixos</h4>
                        <p class="text-sm text-gray-500">Aluguel, softwares e salários base. Impacta a margem mínima.</p>
                    </a>

                    <a href="{{ route('settings.taxes.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-yellow-200 transition-all duration-300">
                        <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1 group-hover:text-orange-600">Impostos</h4>
                        <p class="text-sm text-gray-500">Alíquotas de nota fiscal (ISS, Simples, etc) para cálculo automático.</p>
                    </a>

                    <a href="{{ route('settings.courses.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-yellow-200 transition-all duration-300">
                        <div class="w-12 h-12 bg-cyan-50 text-cyan-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-cyan-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"></path><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1 group-hover:text-cyan-600">Cursos & Treinos</h4>
                        <p class="text-sm text-gray-500">Investimento em capacitação para composição do custo hora.</p>
                    </a>

                </div>
            </div>

            <div>
                <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2 border-b border-gray-200 pb-2">
                    <span class="bg-gray-200 text-gray-600 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </span>
                    Operacional & Sistema
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <a href="{{ route('checklist-models.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-300 transition-all duration-300">
                        <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-teal-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1 group-hover:text-teal-600">Modelos de Checklist</h4>
                        <p class="text-sm text-gray-500">Crie os padrões de execução para os técnicos em campo.</p>
                    </a>

                    <a href="{{ route('admin.users.index') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-300 transition-all duration-300">
                        <div class="w-12 h-12 bg-pink-50 text-pink-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-pink-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1 group-hover:text-pink-600">Usuários & Acessos</h4>
                        <p class="text-sm text-gray-500">Cadastre a equipe e defina permissões (Admin, Comercial, Técnico).</p>
                    </a>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>