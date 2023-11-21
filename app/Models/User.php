<?php

namespace App\Models;

use App\Statuses\DepositStatus;
use App\Statuses\RequestStatus;
use App\Statuses\RequestType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use SoftDeletes;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password', 'work_email', 'mobile', 'phone', 'serial_number', 'nationalitie_id',
        'birthday_date', 'material_status', 'address', 'guarantor', 'position', 'branch', 'departement', 'gender', 'type', 'status', 'skills',
        'start_job_contract', 'end_job_contract', 'image', 'id_photo', 'biography',
        'employee_sponsorship', 'end_employee_sponsorship', 'visa', 'end_visa',
        'passport', 'end_passport', 'municipal_card', 'end_municipal_card', 'health_insurance', 'end_health_insurance',
        'basic_salary', 'company_id', 'employee_residence', 'end_employee_residence',
        'code', 'expired_at', 'entry_time', 'leave_time', 'number_working_hours', 'is_verifed', 'device_key', 'next_month_salary', 'temporary_email', 'reset_password'
    ];
    public $pushNotificationType = 'users';
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }


    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function overTimeAttendances()
    {
        return $this->hasMany(OverTimeAttendance::class);
    }
    public function attendancesMonthly()
    {
        $currentMonth = Carbon::now()->month;

        return $this->hasMany(Attendance::class)
            ->whereMonth('date', $currentMonth);
    }
    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function availableTime()
    {
        return $this->hasOne(EmployeeAvailableTime::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class)->where('status', RequestStatus::APPROVEED);
    }
    public function leaves()
    {
        return $this->hasMany(Request::class)->where('type', RequestType::VACATION);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function nationalitie()
    {
        return $this->belongsTo(Nationalitie::class, 'nationalitie_id');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status', DepositStatus::APPROVED)->where('extra_status', null);
    }
    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function dismissedReport()
    {
        return $this->hasMany(DismissedReport::class);
    }


    public function generate_code()
    {
        $this->timestamps = false;
        $this->code = rand(1000, 9999);
        $this->expired_at = now()->addMinute(10);
        $this->save();
    }
    public function reset_code()
    {
        $this->timestamps = false;
        $this->code = null;
        $this->expired_at = null;
        $this->save();
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'participants')->latest('last_message_id')->withPivot(['joined_at']);
    }
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'user_id', 'id');
    }

    public function receivedMessages()
    {
        return $this->belongsToMany(Message::class, 'recipients')->withPivot(['read_at', 'deleted_at']);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function routeNotificationForPusherPushNotifications($notification): string
    {
        return 'your.custom.interest.string';
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
