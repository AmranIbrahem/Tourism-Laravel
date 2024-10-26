<?php

namespace App\Models\Admin;

use App\Models\Places\Area;
use App\Models\User\Subscriptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trips extends Model
{
    use HasFactory;
    protected $table = 'trips';

    protected $fillable = [
        'AreaName',
        'NumberOfPeople',
        'Cost',
        'TripDetails',
        'TripHistory',
        'RegistrationStartDate',
        'RegistrationEndDate',
        'Pending',
        'Confirmed',
        'Completed'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function areas()
    {
        return $this->belongsToMany(Area::class, 'trip_areas', 'trip_id', 'area_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscriptions::class, 'trip_id');
    }

    public function trip()
    {
        return $this->hasMany(TripAreas::class, 'trip_id');
    }

}
