<?php

namespace App\Models\Places;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table = 'countries';

    protected $fillable = [
        'CountryName',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function cities()
    {
        return $this->hasMany(City::class, 'country_id');
    }

}
