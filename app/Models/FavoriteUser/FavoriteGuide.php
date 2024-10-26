<?php

namespace App\Models\FavoriteUser;

use App\Models\Guides\Guides;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteGuide extends Model
{
    use HasFactory;

    protected $table = 'favorite_guides';

    protected $fillable = [
        'user_id',
        'guide_id',
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
