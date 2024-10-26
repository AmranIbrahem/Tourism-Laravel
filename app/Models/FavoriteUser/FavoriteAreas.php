<?php

namespace App\Models\FavoriteUser;

use App\Models\Places\Area;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteAreas extends Model
{
    use HasFactory;
    protected $table = 'favorite_areas';

    protected $fillable = [
        'user_id',
        'area_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

}
