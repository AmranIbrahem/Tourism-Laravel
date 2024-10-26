<?php

namespace App\Models\User;

use App\Models\Guides\Guides;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guide_id',
        'report_text',
        'status',
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
