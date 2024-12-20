<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Device;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'device_id' => 'required|exists:devices,id',
    ]);

    if (Attendance::hasActiveCheckIn($validated['user_id'])) {
        return response()->json(['error' => 'User already checked in'], 400);
    }

    $attendance = Attendance::create([
        'user_id' => $validated['user_id'],
        'device_id' => $validated['device_id'],
        'check_in' => now(),
    ]);

    return response()->json([
        'message' => 'Check-in successful',
        'attendance' => $attendance,
    ]);
}

public function checkOut(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'device_id' => 'required|exists:devices,id',
    ]);

    $attendance = Attendance::where('user_id', $validated['user_id'])
        ->whereNull('check_out')
        ->first();

    if (!$attendance) {
        return response()->json(['error' => 'No active check-in found'], 404);
    }

    $attendance->update(['check_out' => now()]);

    return response()->json([
        'message' => 'Check-out successful',
        'attendance' => $attendance,
    ]);
}
public function history($userId)
{
    $attendances = Attendance::where('user_id', $userId)->get();
    return response()->json($attendances);
}

public function daily()
{
    $attendances = Attendance::whereDate('check_in', now()->toDateString())
        ->with('user')
        ->get();

    return response()->json($attendances);
}

public function monthly($year, $month)
{
    $attendances = Attendance::whereYear('check_in', $year)
        ->whereMonth('check_in', $month)
        ->with('user')
        ->get()
        ->groupBy('user_id');

    $response = $attendances->map(function ($records, $userId) {
        $user = $records->first()->user;
        return [
            'user' => ['id' => $user->id, 'name' => $user->name],
            'attendance' => $records->map(function ($record) {
                return [
                    'check_in' => $record->check_in,
                    'check_out' => $record->check_out,
                ];
            }),
        ];
    });

    return response()->json($response->values());
}

   
public static function generateApiKey()
    {
        return bin2hex(random_bytes(16));
    }

}
