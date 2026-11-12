<?php

namespace App\Http\Controllers\API;

use App\Exports\StudentExport;
use App\Exports\StudentTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\StudentImport;
use App\Models\SchoolClass;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class StudentImportExportController extends Controller
{
    /**
     * Download sample Excel template for student bulk import
     */
    public function downloadTemplate()
    {
        return Excel::download(new StudentTemplateExport, 'student_import_template.xlsx');
    }

    /**
     * Export students to Excel (.xlsx)
     */
    public function exportExcel(Request $request)
    {
        $fileName = 'students_export_' . date('Y_m_d_His') . '.xlsx';
        return Excel::download(new StudentExport($request->all()), $fileName);
    }

    /**
     * Bulk import students from Excel / CSV file
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
            'default_class_id' => 'nullable|exists:classes,id',
            'default_section_id' => 'nullable|exists:sections,id',
            'create_portal_accounts' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $tenantManager = app(TenantManager::class);

        // Resolve active school ID
        $schoolId = $tenantManager->getTenantId() ?: $user->school_id;

        if (!$schoolId && $request->filled('default_class_id')) {
            $class = SchoolClass::withoutGlobalScopes()->find($request->default_class_id);
            if ($class && $class->school_id) {
                $schoolId = $class->school_id;
            }
        }

        if (!$schoolId) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a school before importing students.',
            ], 422);
        }

        $defaultClassId = $request->filled('default_class_id') ? (int)$request->default_class_id : null;
        $defaultSectionId = $request->filled('default_section_id') ? (int)$request->default_section_id : null;
        $createPortalAccounts = $request->boolean('create_portal_accounts', true);

        $import = new StudentImport($schoolId, $defaultClassId, $defaultSectionId, $createPortalAccounts);

        try {
            Excel::import($import, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => "Bulk import completed. {$import->importedCount} students imported successfully.",
                'imported_count' => $import->importedCount,
                'skipped_count' => $import->skippedCount,
                'errors' => $import->errors,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process spreadsheet: ' . $e->getMessage(),
            ], 500);
        }
    }
}
