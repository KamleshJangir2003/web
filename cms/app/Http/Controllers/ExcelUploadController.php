<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Lead;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExcelUploadController extends Controller
{
    public function uploadExcel(Request $request)
    {
        try {
            // Increase memory and time limits for server
            ini_set('memory_limit', '512M');
            ini_set('max_execution_time', 300);
            
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
                'role' => 'required|string'
            ]);

            $file = $request->file('excel_file');
            $selectedRole = $request->input('role');
            
            Log::info('Excel upload started', [
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'role' => $selectedRole
            ]);
            
            // Check if PhpSpreadsheet is available
            if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
                throw new \Exception('PhpSpreadsheet library not found. Please run: composer install');
            }
            
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Skip header row
            array_shift($rows);
            
            $count = 0;
            $errors = [];
            
            foreach ($rows as $index => $row) {
                try {
                    if (empty($row[0])) continue; // Skip empty rows
                    
                    $number = trim($row[0] ?? '');
                    $name = trim($row[1] ?? '');
                    $role = trim($row[2] ?? $selectedRole); // Use Excel role or selected role
                    
                    // Skip empty data
                    if (empty($number) || empty($name)) continue;
                    
                    // Check if role is python_intern or php_intern - send to Intern table
                    if (in_array($role, ['python_intern', 'php_intern'])) {
                        \App\Models\Intern::create([
                            'number' => $number,
                            'name' => $name,
                            'role' => $role,
                            'condition_status' => null
                        ]);
                    } else {
                        // All other roles including hr_intern - send to Lead table
                        Lead::create([
                            'number' => $number,
                            'name' => $name,
                            'role' => $role,
                            'condition_status' => 'Not Interested'
                        ]);
                    }
                    
                    $count++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                    Log::error('Excel row import error', [
                        'row' => $index + 2,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            $message = "Successfully imported {$count} leads.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " rows had errors.";
            }
            
            Log::info('Excel upload completed', [
                'imported_count' => $count,
                'error_count' => count($errors)
            ]);
            
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => $message,
                'errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            Log::error('Excel upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}