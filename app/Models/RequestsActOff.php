<?php

namespace App\Models;

use App\Enums\Status;
use App\Enums\Venues;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

class RequestsActOff extends Model
{
    use HasFactory;
    protected $primaryKey = 'act_off_no';

    protected $fillable = ['act_off_no','csw', 'prepared_by', 'status', 'org_name', 'req_type',
                            'start_date', 'end_date', 'title', 'venues', 'participants_no'];
    protected $casts =[
        'created_at' => 'datetime',
        'status' => Status::class,
        'venues' => Venues::class
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function accreditation()
    {
        return $this->belongsTo(Accreditation::class, 'org_name_no', 'accred_no');
    }

    protected static function booted()
    {
        static::creating(function ($request) {
            $request->prepared_by = Auth::id();

            $accreditation = Accreditation::where('prepared_by', Auth::id())->first();

            if ($accreditation) {
                $request->org_name_no = $accreditation->accred_no;
            }
        });
    }

    public function calendarEvents(): MorphMany
    {
        return $this->morphMany(Calendar::class, 'eventable');
    }

}
