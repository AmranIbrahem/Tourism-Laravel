<?php

namespace App\Models\Places;

use App\Models\Guides\Guides;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $table = 'cities';

    protected $fillable = [
        'country_id',
        'CityName',
        'CountOfGuides'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function areas()
    {
        return $this->hasMany(Area::class, 'city_id');
    }

    public function guides()
    {
        return $this->hasMany(Guides::class, 'city_id');
    }

}
