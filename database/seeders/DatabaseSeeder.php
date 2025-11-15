<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// Importamos todos os Models que vamos usar
use App\Models\User;
use App\Models\Settings\Channel;
use App\Models\Settings\RevenueTier;
use App\Models\Settings\CommissionRule;
use App\Models\Settings\Tax;
use App\Models\Settings\Equipment;
use App\Models\Settings\Course;
use App\Models\Settings\FixedCost;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. CRIA O USUÁRIO ADMIN
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@tecnolabs.com',
            'password' => '12345678', // A senha será '12345678'
            'role' => 'admin'
        ]);

        // 2. CRIA CONFIGURAÇÕES ESSENCIAIS (Baseado nos seus prints)
        $taxISS = Tax::create(['name' => 'ISS (Padrão)', 'percentage' => 6.00, 'is_default' => true]);
        
        $equip1 = Equipment::create(['name' => 'Drone Mavic 3', 'invested_value' => 23000, 'lifespan_hours' => 5000]);
        $course1 = Course::create(['name' => 'Curso de Pilotagem', 'invested_value' => 1000, 'lifespan_hours' => 1000]);
        
        $cost1 = FixedCost::create(['name' => 'Aluguel', 'monthly_value' => 2000]);
        $cost2 = FixedCost::create(['name' => 'Internet', 'monthly_value' => 150]);

        // 3. CRIA A MATRIZ DE COMISSÃO
        
        // Canais
        $channelSys = Channel::create(['name' => 'Sistema/Telefone']);
        $channelRua = Channel::create(['name' => 'Prospecção de Rua']);

        // Metas (Faixas)
        $meta1 = RevenueTier::create(['name' => 'Meta 1', 'min_value' => 0.00, 'max_value' => 15000.00]);
        $meta2 = RevenueTier::create(['name' => 'Meta 2', 'min_value' => 15000.01, 'max_value' => 30000.00]);
        $meta3 = RevenueTier::create(['name' => 'Meta 3', 'min_value' => 30000.01, 'max_value' => 50000.00]);
        $meta4 = RevenueTier::create(['name' => 'Meta 4 (Acima)', 'min_value' => 50000.01, 'max_value' => 9999999.00]); // Faixa "infinita"

        // Regras (Sistema/Telefone)
        CommissionRule::create(['channel_id' => $channelSys->id, 'revenue_tier_id' => $meta1->id, 'percentage' => 6.00]);
        CommissionRule::create(['channel_id' => $channelSys->id, 'revenue_tier_id' => $meta2->id, 'percentage' => 8.00]);
        CommissionRule::create(['channel_id' => $channelSys->id, 'revenue_tier_id' => $meta3->id, 'percentage' => 10.00]);
        CommissionRule::create(['channel_id' => $channelSys->id, 'revenue_tier_id' => $meta4->id, 'percentage' => 12.00]);

        // Regras (Rua)
        CommissionRule::create(['channel_id' => $channelRua->id, 'revenue_tier_id' => $meta1->id, 'percentage' => 10.00]);
        CommissionRule::create(['channel_id' => $channelRua->id, 'revenue_tier_id' => $meta2->id, 'percentage' => 12.00]);
        CommissionRule::create(['channel_id' => $channelRua->id, 'revenue_tier_id' => $meta3->id, 'percentage' => 15.00]);
        CommissionRule::create(['channel_id' => $channelRua->id, 'revenue_tier_id' => $meta4->id, 'percentage' => 18.00]);
    }
}