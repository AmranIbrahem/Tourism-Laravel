<?php

namespace App\Models\Admin;

use App\Models\Places\Area;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripAreas extends Model
{
    use HasFactory;
    protected $table = 'trip_areas';
    protected $fillable = [
        'area_id',
        'trip_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function trip()
    {
        return $this->belongsTo(Trips::class, 'trip_id');
    }

}
