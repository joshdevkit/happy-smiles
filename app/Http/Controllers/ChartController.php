<?php

namespace App\Http\Controllers;

use App\Models\ClientSchedules;
use App\Models\PaymentScheduleData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function getRevenueData()
    {
        $schedules = PaymentScheduleData::selectRaw('MONTH(payment_date) as month, SUM(added_fee) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $revenue = [];

        foreach (range(1, 12) as $month) {
            $labels[] = Carbon::create()->month($month)->format('F');
            $revenue[] = $schedules->firstWhere('month', $month)->total ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'revenue' => $revenue,
        ]);
    }



    public function getVisitorsInsightData()
    {
        $data = DB::table('client_schedules')
            ->leftJoin('schedules', 'client_schedules.schedule_id', '=', 'schedules.id')
            ->selectRaw('MONTH(schedules.date_added) as month,
                SUM(CASE WHEN client_schedules.is_guest = 1 AND client_schedules.status = "Success" THEN 1 ELSE 0 END) as guests,
                SUM(CASE WHEN client_schedules.is_guest = 0 AND client_schedules.status = "Success" THEN 1 ELSE 0 END) as registered_clients')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $guests = [];
        $registeredClients = [];

        foreach (range(1, 12) as $month) {
            $labels[] = Carbon::create()->month($month)->format('F');

            $monthData = $data->firstWhere('month', $month);
            $guests[] = $monthData->guests ?? 0;
            $registeredClients[] = $monthData->registered_clients ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'guests' => $guests,
            'registered_clients' => $registeredClients,
        ]);
    }



    public function reports()
    {
        return view('admin.reports.index');
    }


    public function generate(Request $request)
    {
        // Validate the request inputs
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $year = $request->input('year');
        $month = $request->input('month');

        // Retrieve schedules with their payment data for the selected year and month
        $schedules = PaymentScheduleData::with(['schedule.service', 'schedule.user'])
            ->whereYear('payment_date', $year)
            ->whereMonth('payment_date', $month)
            ->get();

        // Calculate total revenue
        $totalRevenue = $schedules->sum('added_fee');

        // // Return the schedules and revenue for debugging or further use
        return response()->json([
            'year' => $year,
            'month' => \Carbon\Carbon::create()->month($month)->format('F'),
            'total_revenue' => $totalRevenue,
            'schedules' => $schedules,
        ]);
    }

    public function insights()
    {
        return view('admin.reports.insights');
    }

    public function generate_insights(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $year = $request->input('year');
        $month = $request->input('month');

        // Total patients with "Success" status, filtered by related schedule's date_added
        $totalSuccess = ClientSchedules::where('status', 'Success')
            ->whereHas('schedule', function ($query) use ($year, $month) {
                $query->whereYear('date_added', $year)
                    ->whereMonth('date_added', $month);
            })
            ->count();

        // Total unattended patients with "Not Attended" status, filtered by related schedule's date_added
        $totalUnattended = ClientSchedules::where('status', 'Not Attended')
            ->whereHas('schedule', function ($query) use ($year, $month) {
                $query->whereYear('date_added', $year)
                    ->whereMonth('date_added', $month);
            })
            ->count();

        $totalAppointment = ClientSchedules::where('status', 'Success')
            ->whereHas('schedule', function ($query) use ($year, $month) {
                $query->whereYear('date_added', $year)
                    ->whereMonth('date_added', $month);
            })
            ->count();

        return response()->json([
            'totalSuccess' => $totalSuccess,
            'totalUnattended' => $totalUnattended,
            'totalAppointment' => $totalAppointment
        ]);
    }
}
