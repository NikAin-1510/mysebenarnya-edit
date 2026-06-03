<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicUser extends Model
{
    protected $table = 'publicuser';
    protected $primaryKey = 'PublicID';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'PublicID',
        'UserID',
        'Gender'
    ];

    public $timestamps = false;

    // Relationship back to User
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
