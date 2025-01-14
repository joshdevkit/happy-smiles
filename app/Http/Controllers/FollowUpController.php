<?php

namespace App\Http\Controllers;

use App\Mail\FollowUpMail;
use App\Models\ClientSchedules;
use App\Models\FollowUp;
use App\Models\Services;
use App\Models\User;
use App\Notifications\FollowUpResponseNotification;
use App\Notifications\FollowUpScheduleNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FollowUpController extends Controller
{

    public function store(Request $request)
    {
        $record = ClientSchedules::find($request->input('record_id'));

        if (!$record) {
            return redirect()->back()->with('error', 'Client schedule not found.');
        }

        $validatedData = $request->validate([
            'record_id' => 'required|integer|exists:client_schedules,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'service_id' => 'required|integer|exists:services,id',
            'description' => 'required|string|max:255',
        ]);

        $newlyAdded = ClientSchedules::create([
            'user_id' => $record->user_id,
            'service_id' => $request->input('service_id'),
            'schedule_id' => $request->input('scheduleIdSelected'),
            'start_time' => $request->input('start_time') . ":00",
            'end_time' => $request->input('end_time') . ":00",
            'status' => 'Follow Up'
        ]);

        $user = User::find($record->user_id);
        $serviceSelected =  Services::find($validatedData['service_id']);
        if ($user) {
            Notification::send($user, new FollowUpScheduleNotification([
                'title' => 'Follow Up Request',
                'message' =>
                date('F d, Y', strtotime($validatedData['date'])) . ' -
                ' . date('h:i A', strtotime($validatedData['start_time'])) . ' to
                ' . date('h:i A', strtotime($validatedData['end_time'])) . '
                ',
            ]));

            $followupDetails = [
                'service' => $serviceSelected->name,
                'date' => date('F d, Y', strtotime($validatedData['date'])),
                'start_time' => date('h:i A', strtotime($validatedData['start_time'])),
                'end_time' => date('h:i A', strtotime($validatedData['end_time'])),
            ];
        }

        Mail::to($user->email)->send(new FollowUpMail($user, $followupDetails));

        FollowUp::create([
            'record_id' => $newlyAdded->id,
            'date' => $validatedData['date'],
            'start_time' => $validatedData['start_time'],
            'end_time' => $validatedData['end_time'],
            'service_id' => $validatedData['service_id'],
            'description' => $validatedData['description'],
        ]);

        return redirect()->back()->with('success', 'Follow-up schedule sent to the Client.');
    }


    public function user()
    {
        $data = FollowUp::with('schedule.service')
            ->get();

        $dataWithFollowups = $data->filter(function ($record) {
            return $record->followup && $record->followup->service;
        });

        if ($dataWithFollowups->isNotEmpty()) {
            foreach ($dataWithFollowups as $record) {
                $followUpCreatedAt = Carbon::parse($record->followup->created_at);
                $currentTime = Carbon::now();

                if ($followUpCreatedAt->diffInMinutes($currentTime) > 60) {
                    $record->followup->delete();
                }
            }
        }

        // $data = [];

        return view('client.appointments.follow-up', compact('data'));
    }




    public function accept(Request $request)
    {
        $followUp = FollowUp::findOrFail($request->input('id'));
        $toUpdate = ClientSchedules::find($followUp->record_id);
        $toUpdate->status = 'Pending';
        $toUpdate->save();
        $followUp->update(['is_accepted' => 1]);
        $admins = User::role('Admin')->get();
        Notification::send($admins, new FollowUpResponseNotification([
            'title' => 'Followup Request Accepted',
            'message' =>  'The follow-up request for record ID ' . $followUp->id . ' has been accepted.'
        ]));
    }

    public function reject(Request $request)
    {
        $followUp = FollowUp::findOrFail($request->input('id'));
        $followUp->update(['is_accepted' => 2]);
        $admins = User::role('Admin')->get();
        $sched = ClientSchedules::findOrFail($followUp->record_id);
        $sched->delete();
        Notification::send($admins, new FollowUpResponseNotification([
            'title' => 'Followup Request Decline',
            'message' => 'The follow-up request for record ID ' . $followUp->id . ' has been rejected.'
        ]));
    }
}
