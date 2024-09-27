<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\CLUB\Booking;
use App\Models\CLUB\Course;
use App\Models\CLUB\CRating;
use App\Models\CLUB\Equestrian_club;
use App\Models\CLUB\Trainer;
use App\Models\CLUB\TRating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
//use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'valid',
        'type',
        'mobile',
        'verificationCode',
        'verification_code_expires_at',
        'email_verified_at',
        'resetToken',
        'reset_token_expires_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function equestrian_club () {
        return $this->hasOne(Equestrian_club::class);
    }

    public function health_care () {
        return $this->hasOne(HealthCare::class);
    }

    public function profiles () {
        return $this->hasOne(Profile::class);
    }

//    public function seller_buyer () {
//        return $this->hasOne(SellerBuyer::class);
//    }

    public function trainers () {
        return $this->hasOne(Trainer::class);
    }

    public function CRating()
    {
        return $this->hasMany(CRating::class);
    }

    public function TRating()
    {
        return $this->hasMany(TRating::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }

    public function favoriteClubs()
    {
        return $this->belongsToMany(Equestrian_club::class, 'favorite_clubs');
    }

    public function favoriteAuctions()
    {
        return $this->belongsToMany(Auction::class, 'favorite_auctions');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function doctor () {
        return $this->hasOne(Doctor::class);
    }

    public function messageM()
    {
        return $this->hasMany(MessageM::class);
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
