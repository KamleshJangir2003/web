<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeBankDetail;
use App\Models\EmployeeDocument;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Models\EmailLog;

class EmployeeDocumentController extends Controller
{
    const REQUIRED_DOCUMENTS = [
        'aadhar_card',
        'pan_card',
        'photo',
    ];

    const OPTIONAL_GROUPS = [
        // Any one from 10th/12th
        ['marksheet_10th', 'marksheet_12th'],
        // Any one from diploma/graduation/pg
        ['diploma', 'graduation', 'post_graduation'],
        // Any one from passbook/cheque
        ['passbook', 'cheque'],
    ];

    const OPTIONAL_DOCUMENTS = [
        'bank_statement',
        'experience_letter',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /* =====================================================
       EMPLOYEE DOCUMENTS INDEX (own documents)
    ===================================================== */
    public function index()
    {
        $user = Auth::user();
        $documents = EmployeeDocument::where('user_id', $user->id)->get();
        $bankDetail = EmployeeBankDetail::where('user_id', $user->id)->first();

        $totalRequired  = count(self::REQUIRED_DOCUMENTS) + count(self::OPTIONAL_GROUPS);
        $uploadedCount  = $documents->unique('document_type')->count();
        $verifiedCount  = $documents->where('status', 'verified')->unique('document_type')->count();
        $submittedCount = $documents->where('status', 'submitted')->unique('document_type')->count();
        $pendingCount   = $documents->whereIn('status', ['pending', 'uploaded'])->unique('document_type')->count();
        $isAdminView    = false;

        return response()
            ->view('auth.admin.employees.em_document', compact(
                'user',
                'documents',
                'bankDetail',
                'totalRequired',
                'uploadedCount',
                'verifiedCount',
                'submittedCount',
                'pendingCount',
                'isAdminView'
            ))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /* =====================================================
       ADMIN DOCUMENTS INDEX – LIST EMPLOYEES
    ===================================================== */
    public function adminDocumentsIndex()
    {
        $employees = Employee::where('user_type', 'employee')
            ->where('is_approved', true)
            ->where('action_status', 'selected')
            ->where('hired_status', 'not_hired')
            ->with(['documents' => function($query) {
                $query->select('user_id', 'document_type', 'status');
            }])
            ->orderBy('first_name')
            ->get()
            ->map(function($employee) {
                $documents = $employee->documents;
                
                // Calculate required documents based on actual logic
                $requiredCount = count(self::REQUIRED_DOCUMENTS);
                
                // Add one from each optional group
                foreach (self::OPTIONAL_GROUPS as $group) {
                    $hasAnyFromGroup = $documents->whereIn('document_type', $group)
                        ->whereIn('status', ['uploaded', 'submitted', 'verified'])
                        ->count() > 0;
                    if ($hasAnyFromGroup) {
                        $requiredCount++;
                    }
                }
                
                $uploadedCount = $documents->whereIn('status', ['uploaded', 'submitted', 'verified'])->unique('document_type')->count();
                $verifiedCount = $documents->where('status', 'verified')->unique('document_type')->count();
                $submittedCount = $documents->where('status', 'submitted')->unique('document_type')->count();
                $pendingCount = $documents->whereIn('status', ['pending', 'uploaded'])->unique('document_type')->count();
                
                // Check if all required documents are uploaded
                $allRequiredUploaded = true;
                
                // Check required documents
                foreach (self::REQUIRED_DOCUMENTS as $docType) {
                    $hasDoc = $documents->where('document_type', $docType)
                        ->whereIn('status', ['uploaded', 'submitted', 'verified'])
                        ->count() > 0;
                    if (!$hasDoc) {
                        $allRequiredUploaded = false;
                        break;
                    }
                }
                
                // Check optional groups (at least one from each group)
                if ($allRequiredUploaded) {
                    foreach (self::OPTIONAL_GROUPS as $group) {
                        $hasAnyFromGroup = $documents->whereIn('document_type', $group)
                            ->whereIn('status', ['uploaded', 'submitted', 'verified'])
                            ->count() > 0;
                        if (!$hasAnyFromGroup) {
                            $allRequiredUploaded = false;
                            break;
                        }
                    }
                }
                
                // Calculate actual uploaded count (3 required + 3 from groups)
                $actualUploadedCount = 0;
                foreach (self::REQUIRED_DOCUMENTS as $docType) {
                    if ($documents->where('document_type', $docType)->whereIn('status', ['uploaded', 'submitted', 'verified'])->count() > 0) {
                        $actualUploadedCount++;
                    }
                }
                foreach (self::OPTIONAL_GROUPS as $group) {
                    if ($documents->whereIn('document_type', $group)->whereIn('status', ['uploaded', 'submitted', 'verified'])->count() > 0) {
                        $actualUploadedCount++;
                    }
                }
                
                $employee->document_stats = [
                    'total_required' => 6,
                    'uploaded' => $actualUploadedCount,
                    'verified' => $verifiedCount,
                    'submitted' => $submittedCount,
                    'pending' => $pendingCount,
                    'missing' => 6 - $actualUploadedCount,
                    'status' => $actualUploadedCount == 0 ? 'not_started' : 
                               ($allRequiredUploaded && $verifiedCount >= 6 ? 'completed' : 
                               ($submittedCount > 0 ? 'submitted' : 
                               ($actualUploadedCount > 0 ? 'in_progress' : 'pending')))
                ];
                
                return $employee;
            });

        return response()
            ->view('auth.admin.employees.documents_index', compact('employees'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /* =====================================================
       ADMIN VIEW – EMPLOYEE DOCUMENTS ✅ FIXED
    ===================================================== */
    public function adminView($userId)
    {
        $employee = Employee::findOrFail($userId);

        $documents = EmployeeDocument::where('user_id', $userId)->get();
        $bankDetail = EmployeeBankDetail::where('user_id', $userId)->first();

        $totalRequired  = count(self::REQUIRED_DOCUMENTS) + count(self::OPTIONAL_GROUPS);
        $uploadedCount = $documents->unique('document_type')->count();
        $verifiedCount = $documents->where('status', 'verified')->unique('document_type')->count();
        $submittedCount = $documents->where('status', 'submitted')->unique('document_type')->count();
        $pendingCount  = $documents->where('status', 'pending')->unique('document_type')->count();

        $isAdminView = true;

        return response()
            ->view('auth.admin.employees.em_document', compact(
                'employee',
                'documents',
                'bankDetail',
                'totalRequired',
                'uploadedCount',
                'verifiedCount',
                'submittedCount',
                'pendingCount',
                'isAdminView'
            ))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /* =====================================================
       ADMIN UPLOAD DOCUMENT (for employee)
    ===================================================== */
    public function adminUploadDocument(Request $request, $userId)
    {
        $request->validate([
            'document_type' => 'required|in:aadhar_card,pan_card,photo,marksheet_10th,marksheet_12th,graduation,diploma,post_graduation,passbook,cheque,pf_esi,bank_statement,experience_letter',
            'document'      => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $employee = Employee::findOrFail($userId);

        if ($request->document_type !== 'salary_slips') {
            $existing = EmployeeDocument::where('user_id', $userId)
                ->where('document_type', $request->document_type)
                ->first();

            if ($existing && $existing->status === 'verified') {
                return back()->with('error', 'Verified document cannot be replaced');
            }

            if ($existing) {
                // Delete old file asynchronously
                if (Storage::disk('public')->exists($existing->file_path)) {
                    Storage::disk('public')->delete($existing->file_path);
                }
                $existing->delete();
            }
        }

        $file = $request->file('document');
        // Use hash for unique filename instead of timestamp
        $filename = md5($userId . $request->document_type . time()) . '.' . $file->getClientOriginalExtension();
        $path = "documents/{$userId}/" . $filename;

        // Store file directly without reading into memory
        Storage::disk('public')->putFileAs("documents/{$userId}", $file, $filename);

        EmployeeDocument::create([
            'user_id'        => $userId,
            'document_type'  => $request->document_type,
            'document_name'  => $this->getDocumentDisplayName($request->document_type),
            'file_path'      => $path,
            'file_extension' => $file->getClientOriginalExtension(),
            'file_size'      => $file->getSize(),
            'status'         => 'uploaded',
            'uploaded_at'    => now(),
        ]);

        // Clear cache
        Cache::forget('dashboard_stats');

        return redirect()->route('admin.employees.document', ['userId' => $userId])
            ->with('success', 'Document uploaded successfully');
    }

    /* =====================================================
       ADMIN SAVE BANK DETAILS (for employee)
    ===================================================== */
    public function adminSaveBankDetails(Request $request, $userId)
    {
        $request->validate([
            'bank_name'       => 'required',
            'account_number'  => 'required',
            'ifsc_code'       => 'required',
            'account_type'    => 'required|in:savings,current',
        ]);

        EmployeeBankDetail::updateOrCreate(
            ['user_id' => $userId],
            $request->only('bank_name', 'account_number', 'ifsc_code', 'account_type')
        );

        return redirect()->route('admin.employees.document', ['userId' => $userId])
            ->with('success', 'Bank details saved successfully');
    }

    /* =====================================================
       ADMIN SUBMIT FOR VERIFICATION
    ===================================================== */
    public function adminSubmitForVerification($userId)
    {
        $uploadedTypes = EmployeeDocument::where('user_id', $userId)
            ->pluck('document_type')
            ->unique()
            ->toArray();

        $missing = array_diff(self::REQUIRED_DOCUMENTS, $uploadedTypes);

        if (!empty($missing)) {
            return back()->with('error', 'Missing documents: ' . implode(', ', array_map(fn($m) => ucwords(str_replace('_', ' ', $m)), $missing)));
        }

        // Send offer letter via email (without changing document status)
        return $this->sendOfferLetterEmail($userId);
    }

    /* =====================================================
       SEND OFFER LETTER EMAIL
    ===================================================== */
    public function sendOfferLetterEmail($userId)
    {
        $employee = Employee::findOrFail($userId);
        $employee->refresh(); // Force fresh data from database
        $bankDetail = EmployeeBankDetail::where('user_id', $userId)->first();
        
        // Check if all required documents are uploaded
        $uploadedTypes = EmployeeDocument::where('user_id', $userId)
            ->whereIn('status', ['uploaded', 'submitted', 'verified'])
            ->pluck('document_type')
            ->unique()
            ->toArray();

        $missing = array_diff(self::REQUIRED_DOCUMENTS, $uploadedTypes);

        if (!empty($missing)) {
            return back()->with('error', 'Cannot send offer letter. Please upload all required documents first.');
        }

        try {
            // Generate email HTML content
            $emailContent = view('emails.offer-letter', compact('employee', 'bankDetail'))->render();
            
            \Mail::to($employee->email)->send(new \App\Mail\OfferLetterMail($employee, $bankDetail));
            
            // Save email log
            \App\Models\EmailLog::create([
                'to_email' => $employee->email,
                'subject' => 'Offer Letter - ' . $employee->full_name,
                'content' => $emailContent,
                'sent_at' => now(),
                'status' => 'sent'
            ]);
            
            return redirect()->route('admin.employees.document', ['userId' => $userId])
                ->with('success', 'Offer letter sent successfully to ' . $employee->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /* =====================================================
       GENERATE OFFER LETTER
    ===================================================== */
    public function generateOfferLetter($userId)
    {
        $employee = Employee::findOrFail($userId);
        $employee->refresh(); // Force fresh data from database
        $bankDetail = EmployeeBankDetail::where('user_id', $userId)->first();
        
        // Add cache busting headers
        return response()
            ->view('auth.admin.employees.offer-letter', compact('employee', 'bankDetail'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /* =====================================================
       EMPLOYEE UPLOAD DOCUMENT
    ===================================================== */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:aadhar_card,pan_card,photo,marksheet_10th,marksheet_12th,graduation,diploma,post_graduation,passbook,cheque,pf_esi,bank_statement,experience_letter',
            'document'      => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $user = Auth::user();

        if ($request->document_type !== 'salary_slips') {
            $existing = EmployeeDocument::where('user_id', $user->id)
                ->where('document_type', $request->document_type)
                ->first();

            if ($existing && $existing->status === 'verified') {
                return back()->with('error', 'Verified document cannot be replaced');
            }

            if ($existing) {
                if (Storage::disk('public')->exists($existing->file_path)) {
                    Storage::disk('public')->delete($existing->file_path);
                }
                $existing->delete();
            }
        }

        $file = $request->file('document');
        $filename = md5($user->id . $request->document_type . time()) . '.' . $file->getClientOriginalExtension();
        $path = "documents/{$user->id}/" . $filename;

        // Store file directly
        Storage::disk('public')->putFileAs("documents/{$user->id}", $file, $filename);

        EmployeeDocument::create([
            'user_id'        => $user->id,
            'document_type'  => $request->document_type,
            'document_name'  => $this->getDocumentDisplayName($request->document_type),
            'file_path'      => $path,
            'file_extension' => $file->getClientOriginalExtension(),
            'file_size'      => $file->getSize(),
            'status'         => 'uploaded',
            'uploaded_at'    => now(),
        ]);

        return back()->with('success', 'Document uploaded successfully');
    }

    /* =====================================================
       SAVE BANK DETAILS
    ===================================================== */
    public function saveBankDetails(Request $request)
    {
        $request->validate([
            'bank_name'       => 'required',
            'account_number' => 'required',
            'ifsc_code'      => 'required',
            'account_type'   => 'required|in:savings,current',
        ]);

        EmployeeBankDetail::updateOrCreate(
            ['user_id' => Auth::id()],
            $request->only('bank_name', 'account_number', 'ifsc_code', 'account_type')
        );

        return back()->with('success', 'Bank details saved successfully');
    }

    /* =====================================================
       VIEW DOCUMENT (EMPLOYEE + ADMIN) ✅ FIXED
    ===================================================== */
    public function viewDocument($id)
    {
        $doc = EmployeeDocument::findOrFail($id);

        if (Auth::user()->user_type !== 'admin' && $doc->user_id !== Auth::id()) {
            abort(403);
        }

        return response()->file(
            Storage::disk('public')->path($doc->file_path)
        );
    }

    /* =====================================================
       DOWNLOAD DOCUMENT (EMPLOYEE + ADMIN) ✅ FIXED
    ===================================================== */
    public function downloadDocument($id)
    {
        $doc = EmployeeDocument::findOrFail($id);

        if (Auth::user()->user_type !== 'admin' && $doc->user_id !== Auth::id()) {
            abort(403);
        }

        return Storage::disk('public')->download(
            $doc->file_path,
            $doc->document_name . '.' . $doc->file_extension
        );
    }

    /* =====================================================
       DELETE DOCUMENT
    ===================================================== */
    public function deleteDocument($id)
    {
        $doc = EmployeeDocument::findOrFail($id);

        if ($doc->status === 'verified') {
            return back()->with('error', 'Verified document cannot be deleted');
        }

        if (Auth::user()->user_type !== 'admin' && $doc->user_id !== Auth::id()) {
            abort(403);
        }

        Storage::disk('public')->delete($doc->file_path);
        $doc->delete();

        return back()->with('success', 'Document deleted successfully');
    }

    /* =====================================================
       SUBMIT FOR VERIFICATION
    ===================================================== */
    public function submitForVerification()
    {
        $user = Auth::user();

        $uploadedTypes = EmployeeDocument::where('user_id', $user->id)
            ->pluck('document_type')
            ->unique()
            ->toArray();

        $missing = array_diff(self::REQUIRED_DOCUMENTS, $uploadedTypes);

        if (!empty($missing)) {
            return back()->with('error', 'Missing documents: ' . implode(', ', $missing));
        }

        // Update all uploaded documents to submitted status
        EmployeeDocument::where('user_id', $user->id)
            ->where('status', 'uploaded')
            ->update(['status' => 'submitted']);

        return back()->with('success', 'Documents submitted for verification');
    }

    private function getDocumentDisplayName($type)
    {
        return ucwords(str_replace('_', ' ', $type));
    }

    /* =====================================================
       HIRED EMPLOYEES INDEX
    ===================================================== */
    public function hiredEmployeesIndex()
    {
        $hiredEmployees = Employee::where('user_type', 'employee')
            ->where('hired_status', 'hired')
            ->where(function($query) {
                $query->whereNull('action_status')
                      ->orWhereIn('action_status', ['', 'pending']);
            })
            
            ->orderBy('joining_date', 'desc')
            ->get();
    
        return view('auth.admin.employees.hired_index', compact('hiredEmployees'));
    }
    

    /* =====================================================
       UPDATE HIRED EMPLOYEE DATA
    ===================================================== */
    public function updateHiredEmployee(Request $request, $userId)
    {
        $request->validate([
            'induction_round' => 'nullable|in:yes,no',
            'training' => 'nullable|in:yes,no',
            'certification_period' => 'nullable|integer|min:1|max:30',
            'action_status' => 'nullable|in:selected,not_selected,reason',
            'joining_date' => 'nullable|date'
        ]);
    
        $employee = Employee::findOrFail($userId);
    
        // ✅ OLD STATUS PEHLE LO (IMPORTANT)
        $oldStatus = $employee->action_status;
    
        // Certification check
        if ($request->has('action_status') && $request->action_status) {
    
            if (!$employee->joining_date && !$request->joining_date) {
                return back()->with('error', 'Cannot select/reject employee without joining date');
            }
    
            $joiningDate = $request->joining_date 
                ? \Carbon\Carbon::parse($request->joining_date) 
                : $employee->joining_date;
    
            $certificationEndDate = $joiningDate->copy()->addDays($employee->certification_period ?? 5);
            $daysRemaining = now()->diffInDays($certificationEndDate, false);
    
            if ($daysRemaining > 0) {
                return back()->with('error', "Cannot select/reject employee. Certification period ends in {$daysRemaining} days.");
            }
        }
    
        $updateData = [];
    
        if ($request->has('induction_round')) {
            $updateData['induction_round'] = $request->induction_round;
        }
        if ($request->has('training')) {
            $updateData['training'] = $request->training;
        }
        if ($request->has('certification_period')) {
            $updateData['certification_period'] = $request->certification_period;
        }
        if ($request->has('joining_date')) {
            $updateData['joining_date'] = $request->joining_date;
        }
        if ($request->has('action_status') && $request->action_status) {
            $updateData['action_status'] = $request->action_status;
            $updateData['action_reason'] = $request->action_reason ?? null;
        }
    
        // ✅ Single update only
        $employee->update($updateData);
    
        // 🔥 JOINING LETTER TRIGGER
        if ($request->action_status === 'selected' && $oldStatus !== 'selected') {
    
            $employee->update([
                'employee_status' => 'active',
                'is_approved' => true,
                'hired_status' => 'hired',
                'action_status' => 'selected'
            ]);
    
            Mail::to($employee->email)
                ->send(new \App\Mail\JoiningLetterMail($employee));
                // ✅ Add this
EmailLog::create([
    'to_email' => $employee->email,
    'subject' => 'Joining Letter - ' . $employee->full_name,
    'content' => view('emails.joining-letter', compact('employee'))->render(),
    'sent_at' => now(),
    'status' => 'sent'
]);
    
            return redirect()->route('admin.employees.hired.index')
                ->with('success', 'Employee selected & joining letter sent successfully!');
        }
    
        if ($request->action_status === 'not_selected') {
            return redirect()->route('admin.employees.not-selected.index')
                ->with('success', 'Employee moved to Not Selected page');
        }
    
        return back()->with('success', 'Employee data updated successfully');
    }
    /* =====================================================
       NOT SELECTED EMPLOYEES INDEX
    ===================================================== */
    public function notSelectedEmployeesIndex()
    {
        $notSelectedEmployees = Employee::where('user_type', 'employee')
            ->where('action_status', 'not_selected')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('auth.admin.employees.not_selected_index', compact('notSelectedEmployees'));
    }

    /* =====================================================
       UPDATE HIRED STATUS
    ===================================================== */
    public function updateHiredStatus(Request $request, $id)
    {
        $request->validate([
            'hired_status' => 'required|in:not_hired,hired'
        ]);

        $employee = Employee::findOrFail($id);
        
        // Check if trying to change to hired
        if ($request->hired_status === 'hired') {
            // Check if all documents are uploaded
            $documents = EmployeeDocument::where('user_id', $id)
                ->whereIn('status', ['uploaded', 'submitted', 'verified'])
                ->get();
            
            $uploadedTypes = $documents->pluck('document_type')->unique()->toArray();
            
            // Check required documents
            $allRequiredUploaded = true;
            foreach (self::REQUIRED_DOCUMENTS as $docType) {
                if (!in_array($docType, $uploadedTypes)) {
                    $allRequiredUploaded = false;
                    break;
                }
            }
            
            // Check optional groups
            if ($allRequiredUploaded) {
                foreach (self::OPTIONAL_GROUPS as $group) {
                    $hasAnyFromGroup = !empty(array_intersect($group, $uploadedTypes));
                    if (!$hasAnyFromGroup) {
                        $allRequiredUploaded = false;
                        break;
                    }
                }
            }
            
            if (!$allRequiredUploaded) {
                return redirect()->back()->with('error', 'Cannot hire employee. All required documents must be uploaded first.');
            }
            
            // Check if offer letter was sent
            $offerLetterSent = EmailLog::where('to_email', $employee->email)
                ->where('subject', 'like', '%Offer Letter%')
                ->where('status', 'sent')
                ->exists();
            
            if (!$offerLetterSent) {
                return redirect()->back()->with('error', 'Cannot hire employee. Offer letter must be sent first.');
            }
        }
        
        $employee->update([
            'hired_status' => $request->hired_status,
            'joining_date' => $request->hired_status === 'hired' ? ($employee->joining_date ?? now()) : $employee->joining_date,
            'action_status' => $request->hired_status === 'hired' ? null : $employee->action_status
        ]);

        if ($request->hired_status === 'hired') {
            return redirect()->route('admin.employees.documents.index')->with('success', 'Employee moved to hired list');
        }

        return redirect()->route('admin.employees.documents.index')->with('success', 'Status updated successfully');
    }

    /* =====================================================
       REHIRE EMPLOYEE (Move from Not Selected to Hired)
    ===================================================== */
    public function rehireEmployee($id)
    {
        $employee = Employee::findOrFail($id);
        
        $employee->update([
            'action_status' => null,
            'action_reason' => null,
            'hired_status' => 'hired',
            'joining_date' => now()
        ]);

        return redirect()->route('admin.employees.hired.index')
            ->with('success', 'Employee rehired successfully with new joining date!');
    }

    /* =====================================================
       REJECT EMPLOYEE (No documents submitted)
    ===================================================== */
    public function rejectEmployee(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500'
        ]);

        $employee = Employee::findOrFail($id);

        try {
            $employee->update([
                'action_status' => 'rejected',
                'action_reason' => $request->rejection_reason,
                'is_interview_candidate' => false,
                'hired_status' => 'rejected'
            ]);

            $emailContent = view('emails.rejection-letter', [
                'employee' => $employee,
                'reason' => $request->rejection_reason
            ])->render();

            Mail::to($employee->email)->send(new \App\Mail\RejectionMail($employee, $request->rejection_reason));

            EmailLog::create([
                'to_email' => $employee->email,
                'subject' => 'Application Status - ' . $employee->full_name,
                'content' => $emailContent,
                'sent_at' => now(),
                'status' => 'sent'
            ]);

            return redirect()->route('admin.employees.rejected.index')
                ->with('success', 'Employee rejected successfully. Rejection email sent to ' . $employee->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Error rejecting employee: ' . $e->getMessage());
        }
    }

    /* =====================================================
       REJECTED EMPLOYEES INDEX
    ===================================================== */
    public function rejectedEmployeesIndex()
    {
        $rejectedEmployees = Employee::where('user_type', 'employee')
            ->where('action_status', 'rejected')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('auth.admin.employees.rejected_index', compact('rejectedEmployees'));
    }

    /* =====================================================
       REHIRE FROM REJECTED
    ===================================================== */
    public function rehireFromRejected($id)
    {
        $employee = Employee::findOrFail($id);
        
        $employee->update([
            'action_status' => 'selected',
            'action_reason' => null,
            'is_interview_candidate' => true,
            'hired_status' => 'not_hired'
        ]);

        return redirect()->route('admin.employees.documents.index')
            ->with('success', 'Employee rehired successfully and moved back to documents pending list!');
    }
}