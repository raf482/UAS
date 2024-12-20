<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
   public function generateToken(Request $request)
{
    $validated = $request->validate([
        'device_name' => 'required|string',
        'device_ip' => 'required|ip|unique:devices,device_ip',
    ]);

    $device = Device::updateOrCreate(
        ['device_ip' => $validated['device_ip']],
        [
            'device_name' => $validated['device_name'],
            'api_key' => Device::generateApiKey(),
        ]
    );

    return response()->json([
        'message' => $device->wasRecentlyCreated ? 'Device registered successfully' : 'Device already registered',
        'api_key' => $device->api_key,
    ]);
}


    public function index()
    {
        $devices = Device::all();
        return response()->json($devices);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_name' => 'required|string',
            'device_ip' => 'required|ip|unique:devices,device_ip',
        ]);

        $device = Device::create([
            'device_name' => $validated['device_name'],
            'device_ip' => $validated['device_ip'],
            'api_key' => Str::random(32),
        ]);

        return response()->json(['message' => 'Device created successfully', 'device' => $device], 201);
    }

}

