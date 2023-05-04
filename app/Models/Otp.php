<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = ["otp"];

    public static function booted()
    {
        static::updating(function ($otp) {
            $otp->expires_at = Carbon::now()->addMinutes(5);
        });
    }

    public $timestamps = false;

    public function user(): BelongsTo
    {
      return $this->belongsTo(User::class);
    }
}
