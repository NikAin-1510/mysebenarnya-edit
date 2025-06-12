<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mcmc extends Model
{
    protected $table = 'mcmc';
    protected $primaryKey = 'mcmcID';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'mcmcID',
        'UserID',
        'Position'
    ];

    public $timestamps = false;

    // Relationship back to User
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
