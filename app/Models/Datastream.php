<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Datastream extends Model
{
    use HasFactory;

    protected $table = 'datastream';

    // Kolom-kolom yang boleh diisi secara massal.
    // Ini harus sesuai dengan kolom di tabel database Anda.
    protected $fillable = [
        'ppm',
        'light_intensity',
        'temp',
        'humid',
        'vpd',
        'batt',
        'current',
        'voltage',
        'power',
        'pf',
        'freq',
        'energy',
        'duration', // Pastikan kolom ini ada di database Anda
        'timestamp'
    ];

    // Karena tabel Anda memiliki `created_at` dan `updated_at`,
    // tidak perlu mengatur public $timestamps = false; (defaultnya true).
}