<?php

namespace App\Http\Controllers;

use App\Models\ClientSchedules;
use App\Models\Services;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServicesController extends Controller
{


    public function show($id)
    {
        $service = Services::findOrFail($id);
        return response()->json($service);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            // 'reserve_fee' => 'required|string|min:0',
            // 'price' => 'required|string|min:0',
            'classification' => 'required',
            'duration' => 'required|string|min:1',
            'availability' => 'required|string|in:available,not available',
        ]);

        Services::create($request->all());

        return redirect()->route('admin.dashboard')->with('success', 'Service added successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'serviceId' => 'required|integer',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            // 'editReserveFee' => 'required|string|min:0',
            // 'price' => 'required|string|min:0',
            'classification' => 'required',
            'duration' => 'required|string',
            'availability' => 'required|string',
        ]);

        $service = Services::findOrfail($request->input('serviceId'));

        $service->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            // 'reserve_fee' => $request->input('editReserveFee'),
            // 'price' => $request->input('price'),
            'classification' => $request->input('classification'),
            'duration' => $request->input('duration'),
            'availability' => $request->input('availability'),
        ]);
    }



    public function destroy($id)
    {
        $service = Services::findOrFail($id);
        $service->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Service deleted successfully.');
    }


    public function fetch_service(Request $request)
    {
        $classification = $request->input('classification');

        if ($classification == '1') {
            $services = Services::all();
        } else {
            $services = Services::where('classification', $classification)->get();
        }

        return response()->json([
            'services' => $services,
        ]);
    }
}
