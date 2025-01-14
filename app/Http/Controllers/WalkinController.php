<?php

namespace App\Http\Controllers;

use App\Models\ClientSchedules;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalkinController extends Controller
{
    public function admin_walkin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'classification' => 'required|in:0,1',
            'service_id' => 'required|exists:services,id',
            'adminstartTimeData' => 'required',
            'adminendTimeData' => 'required',
            'clientEmail' => [
                'nullable',
                'email',
                'required_if:classification,1'
            ],
            'selectedSchedID' => 'required|exists:schedules,id',
        ], [
            'classification.required' => 'Please specify if the client is registered or unregistered.',
            'classification.in' => 'Invalid classification selection.',
            'service_id.required' => 'Services must be selected after classification is chosen.',
            'service_id.exists' => 'The selected service does not exist.',
            'adminstartTimeData.required' => 'Please enter a start time.',
            'adminendTimeData.required' => 'Please enter an end time.',
            'clientEmail.email' => 'Please enter a valid email address.',
            'clientEmail.required_if' => 'Email is required for unregistered clients.',
            'selectedSchedID.required' => 'Please select a schedule ID.',
            'selectedSchedID.exists' => 'The selected schedule ID is invalid.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation errors occurred',
            ], 422);
        }

        $userId = null;
        if ($request->classification == 1 && $request->clientEmail) {
            $user = User::where('email', $request->clientEmail)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'No user found with the provided email for a registered client.'
                ], 422);
            }
            $userId = $user->id;
        }

        ClientSchedules::create([
            'user_id' => $userId,
            'service_id' => $request->service_id,
            'schedule_id' => $request->selectedSchedID,
            'start_time' => $request->adminstartTimeData,
            'end_time' => $request->adminendTimeData,
            'walk_in' => 1,
            'walk_in_name' => $request->clientName
        ]);

        return response()->json([
            'message' => 'Client schedule created successfully.'
        ], 200);
    }
}
