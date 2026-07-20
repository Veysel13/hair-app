<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    private function business(): Business
    {
        $user = Auth::user();
        return $user->isAdmin() ? Business::first() : $user->businesses()->firstOrFail();
    }

    public function index(Request $request)
    {
        $business = $this->business();
        $staff    = $business->activeStaff;

        // Varsayılan: bu haftanın Pazartesi'si
        $weekStart = $request->filled('week')
            ? \Carbon\Carbon::parse($request->week)->startOfWeek(\Carbon\Carbon::MONDAY)
            : now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $weekEnd   = $weekStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        $appointments = Appointment::with(['customer', 'staff', 'services.service'])
            ->where('business_id', $business->id)
            ->whereBetween('appointment_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get();

        // Takvim için veri hazırla: gün => personel => randevular
        $calendarData = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i)->toDateString();
            $calendarData[$date] = [];
            foreach ($staff as $s) {
                $calendarData[$date][$s->id] = $appointments
                    ->filter(function ($appt) use ($date, $s) {
                        $apptDate = $appt->appointment_date instanceof \Carbon\Carbon
                            ? $appt->appointment_date->toDateString()
                            : substr($appt->appointment_date, 0, 10);
                        return $apptDate === $date && $appt->staff_id === $s->id;
                    })
                    ->values();
            }
        }

        $prevWeek = $weekStart->copy()->subWeek()->toDateString();
        $nextWeek = $weekStart->copy()->addWeek()->toDateString();

        return view('admin.calendar.index', compact(
            'business', 'staff', 'appointments',
            'calendarData', 'weekStart', 'weekEnd',
            'prevWeek', 'nextWeek'
        ));
    }
}
