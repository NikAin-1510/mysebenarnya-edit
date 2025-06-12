<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryAssignment extends Model
{
    protected $table = 'inquiryasssignment'; // note: 3 's' to match actual table name
    protected $primaryKey = 'AssignmentID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'AssignmentID',
        'AgencyID',
        'mcmcID',
        'InquiryID',
        'AssignDate',
        'JurisdictionStatus',
        'InquiryComment',
    ];

    // Relationships
    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'InquiryID', 'InquiryID');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'AgencyID', 'AgencyID');
    }

    public function mcmc()
    {
        return $this->belongsTo(MCMC::class, 'mcmcID', 'mcmcID');
    }

    public function progress()
    {
        return $this->hasOne(InquiryProgress::class, 'AssignmentID', 'AssignmentID');
    }
}
