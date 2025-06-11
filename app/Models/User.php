<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'UserID';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'UserID',
        'Email',
        'Password',
        'Name',
        'PhoneNum',
        'ProfilePic',
        'Role',
        'Created_At',
        'Login_At',
        'Updated_At',
    ];

    public $timestamps = false; // since you're handling timestamps manually

    protected $hidden = [
        'Password',
    ];

    // Relationships
    public function publicuser()
    {
        return $this->hasOne(PublicUser::class, 'UserID');
    }

    public function mcmc()
    {
        return $this->hasOne(MCMC::class, 'UserID');
    }

    public function agency()
    {
        return $this->hasOne(Agency::class, 'UserID');
    }
}
