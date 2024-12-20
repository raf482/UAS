<?php

namespace App\Models;

use App\Models\User;
use App\Models\Device;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'device_id', 'check_in', 'check_out',
    ];

    /**
     * Relasi ke model User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model Device.
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Periksa apakah karyawan memiliki check-in aktif.
     */
    public static function hasActiveCheckIn($userId)
    {
        return self::where('user_id', $userId)
            ->whereNull('check_out')
            ->exists();
    }
}
