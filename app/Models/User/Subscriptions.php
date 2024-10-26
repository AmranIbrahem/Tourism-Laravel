<?php

namespace App\Models\User;

use App\Models\Admin\Trips;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
    use HasFactory;
    protected $table = 'subscriptions';
    protected $fillable = [
        'trip_id',
        'user_id',
        'GoStatus',

    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function trip()
    {
        return $this->belongsTo(Trips::class, 'trip_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
