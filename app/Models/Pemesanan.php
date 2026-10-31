<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_booking',
        'tamu_id',
        'kamar_id',
        'tanggal_check_in',
        'tanggal_check_out',
        'total_harga',
        'status',
        'pembuat_id',
    ];

    protected $casts = [
        'tanggal_check_in' => 'date',
        'tanggal_check_out' => 'date',
        'total_harga' => 'decimal:2',
    ];

    public function tamu()
    {
        return $this->belongsTo(User::class, 'tamu_id');
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id')->withTrashed();
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'pemesanan_id');
    }

    public static function generateKodeBooking(): string
    {
        return 'BKG-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
    }
}
