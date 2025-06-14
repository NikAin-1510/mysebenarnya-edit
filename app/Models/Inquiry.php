<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Inquiry extends Model
{
    protected $table = 'inquiry';
    protected $primaryKey = 'InquiryID';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'InquiryID',
        'PublicID',
        'InquiryTitle',
        'InquiryDescription',
        'SubmissionDate',
        'SubmissionStatus',
        'SubmissionLink',
        'SubmissionEvidence',
        'SubmissionCategory', // Add the new field here
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inquiry) {
            if (!$inquiry->InquiryID) {
                $count = DB::table('inquiry')->count() + 1;
                $inquiry->InquiryID = 'IQ' . str_pad($count, 6, '0', STR_PAD_LEFT);
            }

            if (!$inquiry->SubmissionDate) {
                $inquiry->SubmissionDate = now();
            }

            // Default value if not provided
            if (!$inquiry->SubmissionCategory) {
                $inquiry->SubmissionCategory = 'Genuine';
            }
        });
    }
}
