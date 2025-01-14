<?php

namespace App\Http\Controllers;

use App\Mail\ReScheduleMail;
use App\Models\ClientSchedules;
use App\Models\Schedules;
use App\Models\Services;
use App\Models\User;
use App\Services\ScheduleService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SchedulesController extends Controller
{
    protected $scheduleService;

    // Inject the ScheduleService via constructor
    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $records = Schedules::with(['record' => function ($query) {
            $query->where('status', 'Confirmed');
        }, 'record.user', 'record.service'])
            ->whereHas('record', function ($query) {
                $query->where('status', 'Confirmed');
            })
            ->get();

        return view('admin.schedules.index', compact('records'));
    }


    /**
     * Display all the schedule into a json format (javascript object notation method ito)
     */
    public function fetchSchedules()
    {
        $events = $this->scheduleService->schedules();
        return response()->json($events);
    }


    public function getScheduleData(Request $request)
    {
        $scheduleId = $request->query('id');
        $schedule = Schedules::find($scheduleId);

        if ($schedule) {
            return response()->json([
                'id' => $schedule->id,
                'date_added' => date('M, d Y', strtotime($schedule->date_added)),
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
            ]);
        }
    }


    public function getServiceData(Request $request)
    {
        $sercviceId = $request->query('id');
        $serviceData = Services::find($sercviceId);
        return response()->json($serviceData);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        Schedules::create([
            'date_added' => $validatedData['date'],
            'start_time' => $validatedData['start_time'],
            'end_time' => $validatedData['end_time'],
        ]);

        return redirect()->back()->with('success', 'Schedule created successfully!');
    }


    public function today()
    {
        $today = $this->scheduleService->getTodaySchedules();
        return view('admin.schedules.today-appointment', compact('today'));
    }




    public function unattended()
    {
        $unattended = ClientSchedules::with(['user', 'service', 'schedule'])->where('status', 'Not Attended')->get();
        return view('admin.schedules.unattended-appointment', compact('unattended'));
    }


    public function history()
    {
        $services = Services::all();
        $history = ClientSchedules::with(['user', 'service', 'schedule', 'followup'])->get();
        return view('admin.schedules.history', compact('history', 'services'));
    }


    public function date_validation($date)
    {
        $exists = Schedules::where('date_added', $date)->exists();

        if ($exists) {
            return response()->json(['status' => 'exists', 'message' => 'The date already exists.']);
        } else {
            return response()->json(['status' => 'not_exists', 'message' => 'The date is available.']);
        }
    }


    public function resched(Request $request)
    {
        $schedID = $request->query('schedID');
        $schedules = ClientSchedules::find($schedID);

        if ($schedules) {
            $data = Schedules::find($schedules->schedule_id);
        }
    }


    public function check_dates(Request $request)
    {
        $date = $request->query('date');
        $data = Schedules::where('date_added', $date)->get();
        if ($data->isEmpty()) {
            $data = [];
        }

        return response()->json([
            'data' => $data
        ]);
    }



    public function user_reschedule(Request $request)
    {

        $data = ClientSchedules::find($request->schedule_id);

        if (!$data) {
            return redirect()->back()->with('error', 'Schedule not found.');
        }

        if ($data->user_id == Auth::id()) {
            $validatedData = $request->validate([
                'new_date_id' => 'required|exists:schedules,id',
            ]);

            $newSchedule = Schedules::find($request->new_date_id);

            if (!$newSchedule) {
                return redirect()->back()->with('error', 'The new schedule ID does not exist.');
            }

            $data->schedule_id = $validatedData['new_date_id'];
            $data->start_time = $request->startTimeData;
            $data->end_time = $request->endTimeData;
            $data->save();

            return redirect()->back()->with('success', 'Schedule updated successfully.');
        } else {
            return redirect()->back()->with('error', 'You are not able to change the date of the appointment that does not belong to you.');
        }
    }


    public function admin_reschedule(Request $request)
    {
        $data = ClientSchedules::find($request->schedule_id);
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Schedule not found'
            ]);
        }

        $validatedData = $request->validate(
            [
                'new_date_id' => 'required|exists:schedules,id',
            ],
            [
                'new_date_id.required' => 'New date for rescheduling must be selected first'
            ]
        );

        if (!empty($data->user_id)) {
            $userFormail = User::find($data->user_id);
            $nameofUser = $userFormail->first_name . " " . $userFormail->middle_name . " " . $userFormail->last_name;
            $emailToSend = $userFormail->email;
        } else {
            $nameofUser = $data->guest_name;
            $emailToSend = $data->guest_email;
        }

        $newSchedule = Schedules::find($validatedData['new_date_id']);

        $data->schedule_id = $validatedData['new_date_id'];
        $data->start_time = $request->reschedstartTimeData;
        $data->end_time = $request->reschedendTimeData;
        $data->save();

        Mail::to($emailToSend)->send(new ReScheduleMail($data, $nameofUser));

        return redirect()->back()->with('success', 'Schedule updated successfully.');
    }


    public function markNotAttended(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:client_schedules,id',
        ]);

        $appointment = ClientSchedules::findOrFail($validated['schedule_id']);
        $appointment->status = "Not Attended";
        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated successfully.',
        ]);
    }


    public function status(Request $request)
    {
        $sched = ClientSchedules::where('schedule_id', $request->input('schedId'))
            ->where('start_time', $request->input('startTime'))
            ->get();

        if ($sched->isNotEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'Time already occupied, please select another starting time',
                'data' => $sched
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Time is available',
            ]);
        }
    }


    public function check_start_time(Request $request)
    {
        $start_time = $request->query('start_time');
        $date = $request->query('date');

        $schedule = Schedules::where('date_added', $date)->first();
        $existingSchedules = ClientSchedules::where('schedule_id', $schedule->id)->first();
        if ($start_time . ":00" == $existingSchedules->start_time) {
            return response()->json([
                'exist' => true
            ]);
        }
        return response()->json([
            'exist' => false
        ]);
    }
}
