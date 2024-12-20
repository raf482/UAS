<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_name',
        'device_ip',
        'api_key',
    ];

    /**
     * Generate an API key for the device.
     *
     * @return string
     */
    public static function generateApiKey()
    {
        return Str::random(32); // Menghasilkan API key acak sepanjang 32 karakter
    }
      /**
     * Relasi ke model Attendance.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}

  

