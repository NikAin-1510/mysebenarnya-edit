<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryProgress extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'inquiryprogress';

    // Set primary key if it's not the default id
    protected $primaryKey = 'StatusID';

    // Disable timestamps if the table doesn't use created_at and updated_at
    public $timestamps = false;

    // Fillable attributes for mass assignment
    protected $fillable = [
        'StatusID',
        'InquiryID',
        'AgencyID',
        'AssignmentID',
        'InvestigationBeginDate',
        'VerificationStatus',
        'VerificationDateTime',
        'InvestigationDetails',
        'InvestigationDoc',    // ✅ newly added
        'Notify',
    ];


    // Relationship to inquiry model
    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'InquiryID');
    }

    // Relationship to agency model
    public function agency()
    {
        return $this->belongsTo(Agency::class, 'AgencyID');
    }

    // Relationship to inquiry assignment model
    public function inquiryAssignment()
    {
        return $this->belongsTo(InquiryAssignment::class, 'AssignmentID');
    }
}
