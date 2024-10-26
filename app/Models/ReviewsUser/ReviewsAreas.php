<?php

namespace App\Models\ReviewsUser;

use App\Models\Places\Area;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewsAreas extends Model
{
    use HasFactory;
    protected $table = 'reviews_areas';

    protected $fillable = [
        'user_id',
        'area_id',
        'rate'
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
