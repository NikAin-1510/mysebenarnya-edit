<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model; // ✅ Required for Eloquent methods like hasOne()
use App\Models\PublicUser;


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

    public $timestamps = false;

    protected $hidden = ['Password'];
    public function getAuthIdentifierName()
    {
        return 'UserID'; // Your custom primary key
    }


    public function publicUser()
    {
        return $this->hasOne(PublicUser::class, 'UserID', 'UserID');
    }



    public function mcmc()
    {
        return $this->hasOne(MCMC::class, 'UserID', 'UserID');
    }

    public function agency()
    {
        return $this->hasOne(Agency::class, 'UserID', 'UserID');
    }
}
