<?php

namespace App\Http\Controllers;

use App\Jobs\ImportarEmpenhosJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpenhosImportController extends Controller
{
    public function uploadCsv(Request $request)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:csv,txt,json',
        ]);

        $file = $request->file('arquivo');

        if ($file->getClientOriginalExtension() === 'json') {
            Storage::put('empenhos/empenhos.json', file_get_contents($file));
        } else {
            Storage::put('empenhos/empenhos_upload.csv', file_get_contents($file));
            ImportarEmpenhosJob::dispatch();
        }

        return back()->with('success', 'Arquivo enviado e importação iniciada!');
    }

    public function iniciarImportacaoAutomatica()
    {
        ImportarEmpenhosJob::dispatch();

        return back()->with('success', 'Importação automática iniciada!');
    }
}
