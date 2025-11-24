<?php

namespace App\Http\Controllers;

use App\Services\TemplateReportService;
use Illuminate\Http\Request;

class TemplateReportsController extends Controller
{
    public function generateDocx(Request $request, TemplateReportService $service)
    {
        $validated = $request->validate([
            'template' => ['required', 'string'],
            'variables' => ['required', 'array'],
            'output' => ['nullable', 'string'],
        ]);

        try {
            $path = $service->generateDocx($validated['template'], $validated['variables'], $validated['output'] ?? null);

            return response()->download($path)->deleteFileAfterSend(false);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function generateXlsx(Request $request, TemplateReportService $service)
    {
        $validated = $request->validate([
            'template' => ['required', 'string'],
            'variables' => ['required', 'array'],
            'output' => ['nullable', 'string'],
        ]);

        try {
            $path = $service->generateXlsx($validated['template'], $validated['variables'], $validated['output'] ?? null);

            return response()->download($path)->deleteFileAfterSend(false);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
