<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryAssignment extends Model
{
    protected $table = 'inquiryassignment'; // note: 3 's' to match actual table name
    protected $primaryKey = 'AssignmentID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'AssignmentID',
        'AgencyID',
        'mcmcID',
        'InquiryID',
        'AgencyName',
        'AssignDate',
        'JurisdictionStatus',
        'InquiryComment',
        'JurisdictionComment'
    ];

    // Relationships

    public function mcmc()
    {
        return $this->belongsTo(MCMC::class, 'mcmcID', 'mcmcID');
    }

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'InquiryID', 'InquiryID');
    }

    public function progress()
    {
        return $this->hasOne(InquiryProgress::class, 'InquiryID', 'InquiryID');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'AgencyID', 'AgencyID');
    }
}
