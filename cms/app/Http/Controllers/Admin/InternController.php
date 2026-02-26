<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use App\Models\Employee;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InternsImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class InternController extends Controller
{
    public function index(Request $request)
    {
        $query = Intern::where(function($q) {
            $q->whereNull('condition_status')
              ->orWhere('condition_status', '');
        });

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('number', 'LIKE', "%{$search}%")
                  ->orWhere('role', 'LIKE', "%{$search}%");
            });
        }

        $interns = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('auth.admin.interns.index', compact('interns'));
    }

    public function uploadExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
            'role' => 'required|string'
        ]);

        try {
            Excel::import(new InternsImport($request->role), $request->file('excel_file'));
            return back()->with('success', 'Interns uploaded successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error uploading file: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $intern = Intern::findOrFail($id);
            $status = $request->status;
            $reason = $request->reason ?? '';
            
            // Handle Call Back status - move to callbacks table
            if ($status === 'Call Back') {
                \App\Models\InternCallback::create([
                    'number' => $intern->number,
                    'name' => $intern->name,
                    'role' => $intern->role,
                    'platform' => $intern->platform,
                    'callback_date' => now()->addDay(),
                    'notes' => $reason,
                    'status' => 'pending'
                ]);
                
                // Delete from interns table
                $intern->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Moved to callbacks',
                    'redirect' => '/admin/interns/callbacks'
                ]);
            }
            
            // For all other statuses, just update the intern record
            $intern->update([
                'condition_status' => $status,
                'reason' => $reason
            ]);
            
            // Determine redirect URL based on status
            $redirectUrl = $this->getRedirectUrlForInternStatus($status);
            
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'redirect' => $redirectUrl
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Intern status update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating status'
            ]);
        }
    }

    public function updateComment(Request $request, $id)
    {
        try {
            $intern = Intern::findOrFail($id);
            $intern->update(['comment' => $request->comment]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
    
    private function getRedirectUrlForInternStatus($status)
    {
        $redirectMap = [
            'Interested' => '/admin/interns/interested',
            'Rejected' => '/admin/interns/rejected',
            'Not Interested' => '/admin/interns/not-interested',
            'Wrong Number' => '/admin/interns/wrong-number',
            'Call Back' => '/admin/interns/callbacks'
        ];
        
        return $redirectMap[$status] ?? '/admin/interns';
    }

    public function showProfile($id)
    {
        $intern = Intern::findOrFail($id);
        return view('auth.admin.interns.profile', compact('intern'));
    }

    public function uploadResume(Request $request, $id)
    {
        $request->validate([
            'resume' => 'required|mimes:pdf,doc,docx|max:5120'
        ]);

        $intern = Intern::findOrFail($id);
        
        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/intern_resumes'), $filename);
            
            $intern->update(['resume' => $filename]);
        }

        return back()->with('success', 'Resume uploaded successfully!');
    }

    public function interested()
    {
        $interns = Intern::where('condition_status', 'Interested')
            ->whereNull('mentor_id')
            ->whereNotIn('final_result', ['Completed', 'Cancelled'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
        return view('auth.admin.interns.interested', compact('interns'));
    }

    public function rejected()
    {
        $interns = Intern::where('condition_status', 'Rejected')
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
        return view('auth.admin.interns.rejected', compact('interns'));
    }

    public function notInterested()
    {
        $interns = Intern::where('condition_status', 'Not Interested')
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
        return view('auth.admin.interns.not-interested', compact('interns'));
    }

    public function wrongNumber()
    {
        $interns = Intern::where('condition_status', 'Wrong Number')
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
        return view('auth.admin.interns.wrong-number', compact('interns'));
    }

    public function callbacks()
    {
        $callbacks = \App\Models\InternCallback::where('status', 'pending')
            ->orderBy('callback_date', 'desc')
            ->paginate(15);
        return view('auth.admin.interns.callbacks', compact('callbacks'));
    }

    public function updateCallback(Request $request, $id)
    {
        $callback = \App\Models\InternCallback::findOrFail($id);
        $callback->update($request->only(['callback_date', 'notes']));
        return response()->json(['success' => true]);
    }

    public function deleteCallback($id)
    {
        $callback = \App\Models\InternCallback::findOrFail($id);
        $callback->delete();
        return response()->json(['success' => true]);
    }

    public function updateCallbackStatus(Request $request, $id)
    {
        $callback = \App\Models\InternCallback::findOrFail($id);
        $newStatus = $request->status;
        $reason = $request->reason ?? $callback->notes;
        
        // Move callback back to interns table with new status
        \App\Models\Intern::create([
            'number' => $callback->number,
            'name' => $callback->name,
            'role' => $callback->role,
            'platform' => $callback->platform,
            'condition_status' => $this->mapCallbackStatusToInternStatus($newStatus),
            'reason' => $reason
        ]);
        
        // Delete from callbacks table
        $callback->delete();
        
        // Determine redirect URL based on status
        $redirectUrl = $this->getRedirectUrlForStatus($newStatus);
        
        return response()->json([
            'success' => true, 
            'message' => 'Status updated successfully',
            'redirect' => $redirectUrl
        ]);
    }
    
    private function mapCallbackStatusToInternStatus($callbackStatus)
    {
        $statusMap = [
            'rejected' => 'Rejected',
            'not_interested' => 'Not Interested',
            'wrong_number' => 'Wrong Number',
            'interested' => 'Interested'
        ];
        
        return $statusMap[$callbackStatus] ?? $callbackStatus;
    }
    
    private function getRedirectUrlForStatus($status)
    {
        $redirectMap = [
            'interested' => '/admin/interns/interested',
            'rejected' => '/admin/interns/rejected',
            'not_interested' => '/admin/interns/not-interested',
            'wrong_number' => '/admin/interns/wrong-number'
        ];
        
        return $redirectMap[$status] ?? '/admin/interns';
    }
    
    public function assignMentor(Request $request, $id)
    {
        $request->validate([
            'mentor_id' => 'required|exists:employees,id',
            'internship_duration' => 'required|integer|min:1|max:12',
            'stipend' => 'nullable|numeric|min:0',
            'start_date' => 'required|date'
        ]);

        $intern = Intern::findOrFail($id);
        
        $endDate = date('Y-m-d', strtotime($request->start_date . ' + ' . $request->internship_duration . ' months'));
        
        $intern->update([
            'mentor_id' => $request->mentor_id,
            'internship_duration' => $request->internship_duration,
            'stipend' => $request->stipend,
            'start_date' => $request->start_date,
            'end_date' => $endDate,
            'final_result' => 'Selected'
        ]);
        
        // Clear dashboard cache
        \Cache::forget('dashboard_stats');

        return back()->with('success', 'Mentor assigned successfully!');
    }
    
    public function showOngoing($id)
    {
        $intern = Intern::findOrFail($id);
        return view('auth.admin.interns.ongoing', compact('intern'));
    }
    
    public function ongoingList()
    {
        $interns = Intern::where('final_result', 'Ongoing')
            ->with(['mentor', 'hr'])
            ->orderBy('start_date', 'desc')
            ->paginate(15);
        return view('auth.admin.interns.ongoing-list', compact('interns'));
    }
    
    public function profiles()
    {
        $interns = Intern::with(['mentor', 'hr'])
            ->whereIn('final_result', ['Completed', 'Cancelled'])
            ->whereNotNull('mentor_id')
            ->orderByRaw("FIELD(final_result, 'Completed', 'Cancelled')")
            ->orderBy('completion_date', 'desc')
            ->paginate(15);
        return view('auth.admin.interns.profiles', compact('interns'));
    }
    
    public function editProfile($id)
    {
        $intern = Intern::with(['mentor', 'hr'])->findOrFail($id);
        return view('auth.admin.interns.edit-profile', compact('intern'));
    }
    
    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            'course' => 'required|string',
            'hr_id' => 'required|exists:employees,id',
            'mentor_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'internship_duration' => 'required|integer|min:1|max:12',
            'stipend' => 'nullable|numeric|min:0',
            'profile_details' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $intern = Intern::findOrFail($id);
        $endDate = date('Y-m-d', strtotime($request->start_date . ' + ' . $request->internship_duration . ' months'));
        
        $intern->update([
            'course' => $request->course,
            'hr_id' => $request->hr_id,
            'mentor_id' => $request->mentor_id,
            'start_date' => $request->start_date,
            'internship_duration' => $request->internship_duration,
            'end_date' => $endDate,
            'stipend' => $request->stipend,
            'profile_details' => $request->profile_details,
            'notes' => $request->notes
        ]);

        return redirect()->route('admin.interns.ongoing-list')->with('success', 'Profile updated successfully!');
    }
    
    public function payment($id)
    {
        $intern = Intern::with('payments')->findOrFail($id);
        return view('auth.admin.interns.payment', compact('intern'));
    }
    
    public function updateStipend(Request $request, $id)
    {
        $request->validate([
            'stipend' => 'required|numeric|min:0',
            'reason' => 'nullable|string'
        ]);

        $intern = Intern::findOrFail($id);
        $oldStipend = $intern->stipend;
        
        $intern->update([
            'stipend' => $request->stipend
        ]);
        
        // Clear dashboard cache
        \Cache::forget('dashboard_stats');

        return response()->json([
            'success' => true,
            'message' => 'Stipend updated successfully!',
            'old_stipend' => $oldStipend,
            'new_stipend' => $request->stipend
        ]);
    }
    
    public function addPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $intern = Intern::findOrFail($id);
        
        // Create payment record
        \App\Models\InternPayment::create([
            'intern_id' => $intern->id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes
        ]);
        
        // Update total paid
        $newTotalPaid = ($intern->total_paid ?? 0) + $request->amount;
        $intern->update(['total_paid' => $newTotalPaid]);
        
        // Clear dashboard cache
        \Cache::forget('dashboard_stats');

        return response()->json([
            'success' => true,
            'message' => 'Payment added successfully!',
            'amount' => $request->amount,
            'total_paid' => $newTotalPaid
        ]);
    }
    
    public function generatePayslip($id)
    {
        $intern = Intern::with(['mentor', 'hr', 'payments'])->findOrFail($id);
        return view('auth.admin.interns.payslip', compact('intern'));
    }
    
    public function sendPayslipWhatsApp($id)
    {
        $intern = Intern::with(['mentor', 'hr', 'payments'])->findOrFail($id);
        
        if (!$intern->number) {
            return response()->json(['success' => false, 'message' => 'Phone number not available']);
        }
        
        $pdf = Pdf::loadView('auth.admin.interns.payslip-pdf', compact('intern'));
        $filename = 'payslip_' . $intern->id . '_' . time() . '.pdf';
        $path = public_path('uploads/payslips/' . $filename);
        
        if (!file_exists(public_path('uploads/payslips'))) {
            mkdir(public_path('uploads/payslips'), 0777, true);
        }
        
        $pdf->save($path);
        
        $message = "Hi {$intern->name}, your training receipt from KWIKSTER is ready.";
        
        return response()->json([
            'success' => true,
            'phone' => $intern->number,
            'message' => $message,
            'pdf_path' => $path
        ]);
    }
    
    public function sendPayslipEmail(Request $request, $id)
    {
        try {
            $intern = Intern::with(['mentor', 'hr', 'payments'])->findOrFail($id);
            
            $email = $request->input('email') ?: $intern->email;
            
            if (!$email) {
                return response()->json(['success' => false, 'message' => 'Email not available']);
            }
            
            $pdf = Pdf::loadView('auth.admin.interns.payslip-pdf', compact('intern'));
            
            Mail::send('emails.payslip', ['intern' => $intern], function($message) use ($email, $intern, $pdf) {
                $message->to($email)
                        ->subject('Your Training Receipt - KWIKSTER')
                        ->attachData($pdf->output(), 'payslip_' . $intern->name . '.pdf');
            });
            
            \Log::info('Payslip email sent to: ' . $email);
            
            return response()->json(['success' => true, 'message' => 'Email sent successfully to ' . $email]);
        } catch (\Exception $e) {
            \Log::error('Email send failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send email. Please check mail configuration.']);
        }
    }
    
    public function setupOngoing(Request $request, $id)
    {
        $request->validate([
            'course' => 'required|string',
            'hr_id' => 'required|exists:employees,id',
            'mentor_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'internship_duration' => 'required|integer|min:1|max:12',
            'stipend' => 'nullable|numeric|min:0',
            'aadhar_card' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            'pan_card' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            'education_document' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            'profile_details' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        try {
            $intern = Intern::findOrFail($id);
            
            $endDate = date('Y-m-d', strtotime($request->start_date . ' + ' . $request->internship_duration . ' months'));
            
            // Handle document uploads
            $documentPaths = [];
            
            // Handle Aadhar Card
            if ($request->hasFile('aadhar_card')) {
                $file = $request->file('aadhar_card');
                $filename = time() . '_aadhar_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/intern_documents'), $filename);
                $documentPaths['aadhar_card'] = $filename;
            }
            
            // Handle PAN Card
            if ($request->hasFile('pan_card')) {
                $file = $request->file('pan_card');
                $filename = time() . '_pan_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/intern_documents'), $filename);
                $documentPaths['pan_card'] = $filename;
            }
            
            // Handle Education Document
            if ($request->hasFile('education_document')) {
                $file = $request->file('education_document');
                $filename = time() . '_education_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/intern_documents'), $filename);
                $documentPaths['education_document'] = $filename;
            }
            
            // Update intern with all the new information
            $intern->update([
                'mentor_id' => $request->mentor_id,
                'internship_duration' => $request->internship_duration,
                'stipend' => $request->stipend,
                'start_date' => $request->start_date,
                'end_date' => $endDate,
                'final_result' => 'Ongoing',
                'course' => $request->course,
                'hr_id' => $request->hr_id,
                'profile_details' => $request->profile_details,
                'notes' => $request->notes,
                'documents' => !empty($documentPaths) ? json_encode($documentPaths) : null
            ]);
            
            // Clear dashboard cache
            \Cache::forget('dashboard_stats');

            return redirect()->route('admin.interns.ongoing-list')->with('success', 'Ongoing internship setup completed successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Ongoing setup failed: ' . $e->getMessage());
            return back()->with('error', 'Error setting up ongoing internship: ' . $e->getMessage());
        }
    }
    
    public function completeInternship(Request $request, $id)
    {
        $request->validate([
            'completion_date' => 'required|date',
            'performance_rating' => 'nullable|string',
            'remarks' => 'nullable|string'
        ]);

        try {
            $intern = Intern::with(['mentor', 'hr'])->findOrFail($id);
            
            // Update intern status
            $intern->update([
                'final_result' => 'Completed',
                'completion_date' => $request->completion_date,
                'performance_rating' => $request->performance_rating,
                'completion_remarks' => $request->remarks
            ]);
            
            // Generate certificate PDF
            $pdf = Pdf::loadView('auth.admin.interns.certificate', compact('intern'));
            $filename = 'certificate_' . $intern->id . '_' . time() . '.pdf';
            $path = public_path('uploads/certificates/' . $filename);
            
            if (!file_exists(public_path('uploads/certificates'))) {
                mkdir(public_path('uploads/certificates'), 0777, true);
            }
            
            $pdf->save($path);
            $certificateUrl = url('uploads/certificates/' . $filename);
            
            // Save certificate path to database
            $intern->update(['certificate_path' => $filename]);
            
            // Send via Email
            if ($request->has('send_email') && $intern->email) {
                try {
                    Mail::send('emails.certificate', ['intern' => $intern], function($message) use ($intern, $pdf) {
                        $message->to($intern->email)
                                ->subject('Internship Completion Certificate - KWIKSTER')
                                ->attachData($pdf->output(), 'certificate_' . $intern->name . '.pdf');
                    });
                } catch (\Exception $e) {
                    \Log::error('Certificate email failed: ' . $e->getMessage());
                }
            }
            
            // Send via WhatsApp (return URL for WhatsApp integration)
            $whatsappMessage = null;
            if ($request->has('send_whatsapp') && $intern->number) {
                $whatsappMessage = "Congratulations {$intern->name}! Your internship at KWIKSTER has been completed successfully. Download your certificate: {$certificateUrl}";
            }
            
            \Cache::forget('dashboard_stats');
            
            return response()->json([
                'success' => true,
                'message' => 'Internship completed successfully!',
                'certificate_url' => $certificateUrl,
                'whatsapp_message' => $whatsappMessage,
                'whatsapp_number' => $intern->number
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Complete internship failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error completing internship: ' . $e->getMessage()
            ]);
        }
    }
    
    public function cancelInternship(Request $request, $id)
    {
        $request->validate([
            'cancellation_date' => 'required|date',
            'cancellation_reason' => 'required|string'
        ]);

        try {
            $intern = Intern::findOrFail($id);
            
            $intern->update([
                'final_result' => 'Cancelled',
                'cancellation_date' => $request->cancellation_date,
                'cancellation_reason' => $request->cancellation_reason
            ]);
            
            \Cache::forget('dashboard_stats');
            
            return response()->json([
                'success' => true,
                'message' => 'Internship cancelled successfully!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Cancel internship failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling internship: ' . $e->getMessage()
            ]);
        }
    }
    
    public function sendCertificate(Request $request, $id)
    {
        try {
            $intern = Intern::with(['mentor', 'hr'])->findOrFail($id);
            
            if (!$intern->certificate_path) {
                return response()->json(['success' => false, 'message' => 'Certificate not found']);
            }
            
            $certificateUrl = url('uploads/certificates/' . $intern->certificate_path);
            $sent = [];
            
            // Send via Email
            if ($request->has('send_email') && $request->send_email) {
                $email = $request->input('email') ?: $intern->email;
                
                if (!$email) {
                    return response()->json(['success' => false, 'message' => 'Email address is required']);
                }
                
                try {
                    $pdf = Pdf::loadView('auth.admin.interns.certificate', compact('intern'));
                    Mail::send('emails.certificate', ['intern' => $intern], function($message) use ($email, $intern, $pdf) {
                        $message->to($email)
                                ->subject('Internship Completion Certificate - KWIKSTER')
                                ->attachData($pdf->output(), 'certificate_' . $intern->name . '.pdf');
                    });
                    $sent[] = 'Email sent to ' . $email;
                } catch (\Exception $e) {
                    \Log::error('Certificate email failed: ' . $e->getMessage());
                    return response()->json(['success' => false, 'message' => 'Email sending failed: ' . $e->getMessage()]);
                }
            }
            
            // Send via WhatsApp
            if ($request->has('send_whatsapp') && $intern->number) {
                $whatsappMessage = "Congratulations {$intern->name}! Your internship at KWIKSTER has been completed successfully. Download your certificate: {$certificateUrl}";
                $sent[] = 'WhatsApp message prepared for ' . $intern->number;
                
                return response()->json([
                    'success' => true,
                    'message' => implode(' and ', $sent),
                    'whatsapp_message' => $whatsappMessage,
                    'whatsapp_number' => $intern->number,
                    'certificate_url' => $certificateUrl
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => implode(' and ', $sent)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Send certificate failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error sending certificate: ' . $e->getMessage()
            ]);
        }
    }
}