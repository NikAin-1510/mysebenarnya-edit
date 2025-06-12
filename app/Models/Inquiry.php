<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $table = 'inquiry';
    protected $primaryKey = 'InquiryID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'InquiryID',
        'PublicID',
        'InquiryTitle',
        'InquiryDescription',
        'SubmissionDate',
        'SubmissionStatus',
        'SubmissionEvidence',
    ];

    public function publicuser()
    {
        return $this->belongsTo(PublicUser::class, 'PublicID', 'PublicID');
    }

    public function assignments()
    {
        return $this->hasMany(InquiryAssignment::class, 'InquiryID', 'InquiryID');
    }

    public function progress()
    {
        return $this->hasMany(InquiryProgress::class, 'InquiryID', 'InquiryID');
    }
}
