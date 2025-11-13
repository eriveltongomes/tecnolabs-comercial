<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\Settings\EquipmentController;
use App\Http\Controllers\Settings\CourseController;
use App\Http\Controllers\Settings\TaxController;
use App\Http\Controllers\Settings\FixedCostController;
use App\Http\Controllers\Settings\ChannelController;
use App\Http\Controllers\Settings\RevenueTierController;
use App\Http\Controllers\Settings\CommissionRuleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () { return redirect()->route('login'); });

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    //Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // COMERCIAL (Visível para todos os envolvidos no processo)
    Route::middleware('role:comercial,admin,financeiro')->group(function () {
        Route::resource('clients', ClientController::class);
    });
    Route::get('/my-results', [StatsController::class, 'index'])->name('stats.index');

    // CONFIGURAÇÕES (AGORA RESTRITO APENAS AO ADMIN)
    Route::middleware('role:admin')->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', function() { return view('settings.index'); })->name('index'); 
        Route::resource('equipment', EquipmentController::class);
        Route::resource('courses', CourseController::class);
        Route::resource('taxes', TaxController::class);
        Route::resource('fixed-costs', FixedCostController::class, ['parameters' => ['fixed-costs' => 'fixedCost']]);
        Route::resource('channels', ChannelController::class);
        Route::resource('revenue-tiers', RevenueTierController::class, ['parameters' => ['revenue-tiers' => 'revenueTier']]);
        Route::resource('commission-rules', CommissionRuleController::class, ['parameters' => ['commission-rules' => 'commissionRule']]);
        Route::resource('users', UserController::class);
    });
    
    // Atalho de usuários para admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
    });

    // PROPOSTAS
    Route::resource('proposals', ProposalController::class);
    Route::post('/proposals/calculate', [ProposalController::class, 'calculate'])->name('proposals.calculate');
    Route::get('/proposals/{proposal}/pdf', [ProposalController::class, 'generatePdf'])->name('proposals.pdf');
    Route::get('/proposals/{proposal}/docx', [ProposalController::class, 'generateDocx'])->name('proposals.docx');
    
    Route::patch('/proposals/{proposal}/approve', [ProposalController::class, 'approve'])->name('proposals.approve');
    Route::patch('/proposals/{proposal}/reject', [ProposalController::class, 'reject'])->name('proposals.reject');
    Route::patch('/proposals/{proposal}/send-analysis', [ProposalController::class, 'sendToAnalysis'])->name('proposals.sendToAnalysis');
    Route::patch('/proposals/{proposal}/cancel', [ProposalController::class, 'cancel'])->name('proposals.cancel');
    Route::patch('/proposals/{proposal}/refuse', [ProposalController::class, 'refuse'])->name('proposals.refuse');
});

require __DIR__.'/auth.php';