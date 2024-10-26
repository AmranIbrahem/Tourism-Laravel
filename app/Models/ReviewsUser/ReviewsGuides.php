<?php

namespace App\Models\ReviewsUser;

use App\Models\Guides\Guides;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewsGuides extends Model
{
    use HasFactory;
    protected $table = 'reviews_guides';

    protected $fillable = [
        'user_id',
        'guide_id',
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

    public function guide()
    {
        return $this->belongsTo(Guides::class, 'guide_id');
    }
}
