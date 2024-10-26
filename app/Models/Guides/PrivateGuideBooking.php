<?php

namespace App\Models\Guides;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateGuideBooking extends Model
{
    use HasFactory;
    protected $table = 'private_guide_bookings';

    protected $fillable = [
        'bookingDate',
        'startDate',
        'endDate',
        'bookingStatus',
        'totalCost',
        'guide_id',
        'user_id',
    ];


    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function guide()
    {
        return $this->belongsTo(Guides::class, 'guide_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
