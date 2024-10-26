<?php

namespace App\Models\Places;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PicturesArea extends Model
{
    use HasFactory;
    protected $table = 'pictures_areas';

    protected $fillable = [
        'area_id',
        'photo',

    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

}
