<?php

namespace App\Models\Guides;

use App\Models\Places\City;
use App\Models\ReviewsUser\ReviewsGuides;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guides extends Model
{
    use HasFactory;
    protected $table = 'guides';

    protected $fillable = [
        'user_id',
        'city_id',
        'CostPerHour',
        'Rate'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function availabilities()
    {
        return $this->hasMany(GuideAvailability::class, 'guide_id');
    }

    public function privateBookings()
    {
        return $this->hasMany(PrivateGuideBooking::class, 'guide_id');
    }

    public function reviews()
    {
        return $this->hasMany(ReviewsGuides::class, 'area_id');
    }
}
