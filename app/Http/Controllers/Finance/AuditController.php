<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index()
    {
        // Busca os últimos logs com paginação
        $activities = Activity::with(['causer', 'subject'])
                        ->latest()
                        ->paginate(20);

        return view('finance.audit.index', compact('activities'));
    }
}