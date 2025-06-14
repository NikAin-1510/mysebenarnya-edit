<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryProgress extends Model
{
    protected $table = 'inquiryprogress';
    protected $primaryKey = 'StatusID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'StatusID',
        'InquiryID',
        'AgencyID',
        'AssignmentID',
        'InvestigationBeginDate',
        'VerificationStatus',
        'VerificationDateTime',
        'InvestigationDetails',
        'InvestigationDoc'
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

    public function assignment()
    {
        return $this->belongsTo(InquiryAssignment::class, 'AssignmentID', 'AssignmentID');
    }
}
