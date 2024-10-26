<?php

namespace App\Models\Places;

use App\Models\Admin\Trips;
use App\Models\FavoriteUser\FavoriteAreas;
use App\Models\ReviewsUser\ReviewsAreas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Area extends Model
{
    use HasFactory;
    protected $table = 'areas';

    protected $fillable = [
        'city_id',
        'AreaName',
        'Details' ,
        'Rate' ,
        'NumberExistingTrips'
        ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function pictures()
    {
        return $this->hasMany(PicturesArea::class, 'area_id');
    }

    public function trips()
    {
        return $this->belongsToMany(Trips::class, 'trip_areas', 'area_id', 'trip_id');
    }

    public function reviews()
    {
        return $this->hasMany(ReviewsAreas::class, 'area_id');
    }

    public function favorites()
    {
        return $this->hasMany(FavoriteAreas::class, 'area_id');
    }


}
