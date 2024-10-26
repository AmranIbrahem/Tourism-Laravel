<?php

namespace App\Models\Guides;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlots extends Model
{
    use HasFactory;
    protected $table = 'time_slots';

    protected $fillable = [
        'guide_availabilities_id',
        'startTime',
        'endTime',

    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function guideAvailability()
    {
        return $this->belongsTo(GuideAvailability::class, 'guide_availabilities_id');
    }

}
