<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Inquiry extends Model
{
    protected $table = 'inquiries';
    protected $primaryKey = 'InquiryID';
    public $incrementing = false; // because InquiryID is a string, not auto-increment
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
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inquiry) {
            if (!$inquiry->InquiryID) {
                $count = DB::table('inquiries')->count() + 1;
                $inquiry->InquiryID = 'IQ' . str_pad($count, 6, '0', STR_PAD_LEFT);
            }

            if (!$inquiry->SubmissionDate) {
                $inquiry->SubmissionDate = now();
            }
        });
    }
}
