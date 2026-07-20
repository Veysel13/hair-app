<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    private function business(): Business
    {
        $user = Auth::user();
        return $user->isAdmin() ? Business::first() : $user->businesses()->firstOrFail();
    }

    public function index(Request $request)
    {
        $business = $this->business();
        $year     = $request->get('year',  now()->year);
        $month    = $request->get('month', now()->month);

        // ── Gelir ──────────────────────────────
        $monthlyRevenue = Appointment::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereYear('appointment_date', $year)
            ->whereMonth('appointment_date', $month)
            ->sum('total_price');

        // ── Gider ──────────────────────────────
        $expenses = Expense::where('business_id', $business->id)
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month)
            ->orderByDesc('expense_date')
            ->get();

        $monthlyExpense = $expenses->sum('amount');
        $netProfit      = $monthlyRevenue - $monthlyExpense;

        // ── Aylık gelir grafiği (son 12 ay) ───
        $revenueByMonth = [];
        for ($m = 11; $m >= 0; $m--) {
            $d = now()->subMonths($m);
            $revenueByMonth[] = [
                'label'  => $d->format('M Y'),
                'revenue'=> Appointment::where('business_id', $business->id)
                    ->where('status', 'completed')
                    ->whereYear('appointment_date', $d->year)
                    ->whereMonth('appointment_date', $d->month)
                    ->sum('total_price'),
                'expense'=> Expense::where('business_id', $business->id)
                    ->whereYear('expense_date', $d->year)
                    ->whereMonth('expense_date', $d->month)
                    ->sum('amount'),
            ];
        }

        // ── Gider kategorisi dağılımı ──────────
        $expenseByCategory = $expenses->groupBy('category')
            ->map(fn($g) => $g->sum('amount'))
            ->sortDesc();

        // ── Bu haftanın randevuları ─────────────
        $weekStart = now()->startOfWeek();
        $weekRevenue = Appointment::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereBetween('appointment_date', [$weekStart->toDateString(), now()->toDateString()])
            ->sum('total_price');

        return view('admin.finance.index', compact(
            'business', 'year', 'month',
            'monthlyRevenue', 'monthlyExpense', 'netProfit',
            'expenses', 'revenueByMonth', 'expenseByCategory', 'weekRevenue'
        ));
    }

    public function storeExpense(Request $request)
    {
        $business = $this->business();
        $data = $request->validate([
            'category'    => 'required|in:rent,utilities,salary,supplies,equipment,marketing,tax,insurance,other',
            'description' => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'expense_date'=> 'required|date',
            'is_recurring'=> 'boolean',
            'recurring_period' => 'nullable|in:weekly,monthly,yearly',
        ]);

        Expense::create(array_merge($data, [
            'business_id' => $business->id,
            'created_by'  => Auth::id(),
        ]));

        return redirect()->route('admin.finance.index')->with('success', 'Gider eklendi.');
    }

    public function destroyExpense(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Gider silindi.');
    }
}
