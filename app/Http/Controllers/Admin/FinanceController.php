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

        // ── Bugünkü Kasam (Daily Cash Desk) ────
        $todayStr = now()->toDateString();
        $todayAppts = Appointment::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereDate('appointment_date', $todayStr)
            ->get();

        $todayRevenue = $todayAppts->sum('total_price');
        $todayCash    = $todayAppts->sum('cash_amount');
        $todayCard    = $todayAppts->sum('card_amount');
        $todayTip     = $todayAppts->sum('tip_amount');

        // Eğer cash_amount ve card_amount henüz 0 ise ve method set edildiyse varsayılan böl
        if ($todayRevenue > 0 && ($todayCash + $todayCard) == 0) {
            $todayCash = $todayRevenue;
        }

        $todayExpensesList = Expense::where('business_id', $business->id)
            ->whereDate('expense_date', $todayStr)
            ->get();
        $todayExpense = $todayExpensesList->sum('amount');
        $todayNetCash = ($todayCash + $todayCard) - $todayExpense;

        // ── Aylık Gelir & Dağılım ──────────────
        $monthlyAppts = Appointment::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereYear('appointment_date', $year)
            ->whereMonth('appointment_date', $month)
            ->get();

        $monthlyRevenue = $monthlyAppts->sum('total_price');
        $monthlyCash    = $monthlyAppts->sum('cash_amount');
        $monthlyCard    = $monthlyAppts->sum('card_amount');
        $monthlyTip     = $monthlyAppts->sum('tip_amount');

        if ($monthlyRevenue > 0 && ($monthlyCash + $monthlyCard) == 0) {
            $monthlyCash = $monthlyRevenue;
        }

        // ── Gider ──────────────────────────────
        $expenses = Expense::where('business_id', $business->id)
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month)
            ->orderByDesc('expense_date')
            ->get();

        $monthlyExpense = $expenses->sum('amount');
        $netProfit      = $monthlyRevenue - $monthlyExpense;

        // ── Aylık gelir & Net Kar grafiği (son 12 ay) ───
        $revenueByMonth = [];
        for ($m = 11; $m >= 0; $m--) {
            $d = now()->subMonths($m);
            $rev = Appointment::where('business_id', $business->id)
                ->where('status', 'completed')
                ->whereYear('appointment_date', $d->year)
                ->whereMonth('appointment_date', $d->month)
                ->sum('total_price');

            $exp = Expense::where('business_id', $business->id)
                ->whereYear('expense_date', $d->year)
                ->whereMonth('expense_date', $d->month)
                ->sum('amount');

            $revenueByMonth[] = [
                'label'  => $d->format('M Y'),
                'revenue'=> $rev,
                'expense'=> $exp,
                'profit' => $rev - $exp,
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
            'todayRevenue', 'todayCash', 'todayCard', 'todayTip', 'todayExpense', 'todayNetCash',
            'monthlyRevenue', 'monthlyCash', 'monthlyCard', 'monthlyTip', 'monthlyExpense', 'netProfit',
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

    public function zReport(Request $request)
    {
        $business = $this->business();
        $dateStr  = $request->get('date', now()->toDateString());
        $reportDate = \Carbon\Carbon::parse($dateStr);

        $zReportNo = 'Z-' . $reportDate->format('Ymd') . '-' . $business->id;

        // Tamamlanan Randevular (Adisyonlar)
        $completedAppts = Appointment::where('business_id', $business->id)
            ->where('status', 'completed')
            ->whereDate('appointment_date', $dateStr)
            ->with(['customer', 'staff', 'services.service'])
            ->orderBy('start_time')
            ->get();

        $totalRevenue = $completedAppts->sum('total_price');
        $cashTotal    = $completedAppts->sum('cash_amount');
        $cardTotal    = $completedAppts->sum('card_amount');
        $tipTotal     = $completedAppts->sum('tip_amount');

        if ($totalRevenue > 0 && ($cashTotal + $cardTotal) == 0) {
            $cashTotal = $totalRevenue;
        }

        // Günün Giderleri
        $expenses = Expense::where('business_id', $business->id)
            ->whereDate('expense_date', $dateStr)
            ->orderByDesc('created_at')
            ->get();

        $totalExpense   = $expenses->sum('amount');
        $netCashBalance = ($cashTotal + $cardTotal) - $totalExpense;

        // Personel Bazlı Günlük Ciro & Hak Ediş Dağılımı
        $staffMembers = \App\Models\Staff::where('business_id', $business->id)
            ->where('is_active', true)
            ->get();

        $staffPerformance = [];
        foreach ($staffMembers as $staff) {
            $staffAppts = $completedAppts->where('staff_id', $staff->id);
            $revenue = $staffAppts->sum('total_price');
            $rate    = $staff->commission_rate ?? 30.0;
            $commission = ($revenue * $rate) / 100;

            if ($staffAppts->count() > 0 || $revenue > 0) {
                $staffPerformance[] = [
                    'staff'        => $staff,
                    'count'        => $staffAppts->count(),
                    'revenue'      => $revenue,
                    'rate'         => $rate,
                    'commission'   => $commission,
                ];
            }
        }

        return view('admin.finance.z_report', compact(
            'business', 'reportDate', 'zReportNo',
            'completedAppts', 'totalRevenue', 'cashTotal', 'cardTotal', 'tipTotal',
            'expenses', 'totalExpense', 'netCashBalance', 'staffPerformance'
        ));
    }
}
