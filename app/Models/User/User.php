<?php

namespace App\Models\User;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\FavoriteUser\FavoriteAreas;
use App\Models\FavoriteUser\FavoriteGuide;
use App\Models\Guides\Guides;
use App\Models\Guides\PrivateGuideBooking;
use App\Models\ReviewsUser\ReviewsAreas;
use App\Models\ReviewsUser\ReviewsGuides;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable  implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'users';

    protected $fillable = [
        'FirstName',
        'LastName',
        'Role',
        'email',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    protected $hidden = [
        'password',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];


    /**
     * Get the identifier that will be stored in the JWT token.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key-value array, containing any custom claims to be added to the JWT token.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscriptions::class, 'user_id');
    }

    public function privateBookings()
    {
        return $this->hasMany(PrivateGuideBooking::class, 'user_id');
    }

    public function favoriteAreas()
    {
        return $this->hasMany(FavoriteAreas::class, 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(ReviewsAreas::class, 'user_id');
    }

    public function guides()
    {
        return $this->hasMany(Guides::class, 'user_id');
    }

    public function reviewsGuide()
    {
        return $this->hasMany(ReviewsGuides::class, 'user_id');
    }

    public function favoriteGuides()
    {
        return $this->hasMany(FavoriteGuide::class, 'user_id');
    }



}
