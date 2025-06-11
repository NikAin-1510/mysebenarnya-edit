<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    protected $table = 'agency';
    protected $primaryKey = 'AgencyID';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['AgencyID', 'UserID', 'AgencyName'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }
}
