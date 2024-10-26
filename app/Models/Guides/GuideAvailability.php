<?php

namespace App\Models\Guides;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuideAvailability extends Model
{
    use HasFactory;
    protected $table = 'guide_availabilities';

    protected $fillable = [
        'availableDate',
        'isAvailable',
        'guide_id',

    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function guide()
    {
        return $this->belongsTo(Guides::class, 'guide_id');
    }

    public function timeSlots()
    {
        return $this->hasMany(TimeSlots::class, 'guide_availabilities_id');
    }

}
