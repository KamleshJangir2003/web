<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        // Check if this is for employee expenses
        if ($request->route()->getName() === 'admin.employee-expenses.index') {
            return $this->employeeExpenses($request);
        }
        
        // Original admin expenses logic
        // Create expenses table if not exists
        DB::statement("CREATE TABLE IF NOT EXISTS admin_expenses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            expense_date DATE NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            description TEXT NOT NULL,
            category VARCHAR(100) NOT NULL,
            payment_method ENUM('UPI', 'Bank Transfer', 'Cash', 'Card', 'Scanner') NOT NULL,
            reference_number VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");

        // Handle form submission
        if ($request->isMethod('post') && $request->has('add_expense')) {
            DB::table('admin_expenses')->insert([
                'expense_date' => $request->expense_date,
                'amount' => $request->amount,
                'description' => $request->description,
                'category' => $request->category,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Cache::forget('dashboard_stats');
            return redirect()->back()->with('success', 'Expense added successfully!');
        }

        // Handle delete
        if ($request->has('delete')) {
            DB::table('admin_expenses')->where('id', $request->delete)->delete();
            Cache::forget('dashboard_stats');
            return redirect()->back()->with('success', 'Expense deleted successfully!');
        }

        // Get filter values
        $selected_month = $request->get('month', date('Y-m'));
        $selected_year = $request->get('year', date('Y'));
        $category_filter = $request->get('category', '');
        $payment_filter = $request->get('payment_method', '');

        // Get categories for filter
        $categories = DB::table('admin_expenses')->distinct()->pluck('category');

        // Build expenses query with filters
        $query = DB::table('admin_expenses')->where(function($q) {});
        
        if ($selected_month) {
            $query->whereRaw("DATE_FORMAT(expense_date, '%Y-%m') = ?", [$selected_month]);
        }
        
        if ($category_filter) {
            $query->where('category', $category_filter);
        }
        
        if ($payment_filter) {
            $query->where('payment_method', $payment_filter);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->orderBy('created_at', 'desc')->get();

        // Calculate totals
        $monthly_total = $expenses->sum('amount');
        $yearly_total = DB::table('admin_expenses')->whereYear('expense_date', $selected_year)->sum('amount');

        // Get monthly breakdown
        $monthly_breakdown = DB::table('admin_expenses')
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(amount) as total")
            ->whereYear('expense_date', $selected_year)
            ->groupByRaw("DATE_FORMAT(expense_date, '%Y-%m')")
            ->orderBy('month')
            ->get();

        // Get category breakdown
        $category_breakdown = DB::table('admin_expenses')
            ->selectRaw('category, SUM(amount) as total, COUNT(*) as count')
            ->whereRaw("DATE_FORMAT(expense_date, '%Y-%m') = ?", [$selected_month])
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();

        return view('admin.expenses.index', compact(
            'expenses', 'categories', 'selected_month', 'selected_year', 
            'category_filter', 'payment_filter', 'monthly_total', 'yearly_total',
            'monthly_breakdown', 'category_breakdown'
        ));
    }
    
    private function employeeExpenses(Request $request)
    {
        $status = $request->get('status', 'all');
        
        // Only show employee expenses (with employee_id)
        $query = Expense::with('employee')->whereNotNull('employee_id');
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $expenses = $query->orderBy('created_at', 'desc')->get();
        
        $stats = [
            'pending' => Expense::whereNotNull('employee_id')->where('status', 'pending')->count(),
            'approved' => Expense::whereNotNull('employee_id')->where('status', 'approved')->count(),
            'rejected' => Expense::whereNotNull('employee_id')->where('status', 'rejected')->count(),
            'total_amount' => Expense::whereNotNull('employee_id')->where('status', 'approved')->sum('amount')
        ];
        
        return view('admin.employee-expenses.index', compact('expenses', 'stats', 'status'));
    }
    
    public function updateStatus(Request $request, Expense $expense)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:500'
        ]);
        
        $expense->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes
        ]);
        
        return redirect()->back()->with('success', 'Expense status updated successfully!');
    }
}