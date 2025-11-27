<?php
namespace App\Http\Controllers;

use App\Models\AgenteTelemetria;
use Illuminate\Http\Request;

class TelemetriaDashboardController extends Controller
{
    public function index()
    {
        $ativos = AgenteTelemetria::where('created_at','>=',now()->subMinutes(10))->count();
        $internetOk = AgenteTelemetria::where('internet_status','ok')->count();
        $atrasados = AgenteTelemetria::where('created_at','<',now()->subMinutes(20))->count();
        $comErro = AgenteTelemetria::whereNotNull('last_error')->count();

        $telemetrias = AgenteTelemetria::with('unidade')->latest()->paginate(30);

        return view('telemetria.dashboard', compact(
            'ativos','internetOk','atrasados','comErro','telemetrias'
        ));
    }
}
