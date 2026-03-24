<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sitrep extends Model
{
    protected $fillable = [

    'incident_type',
    'incident_date',
    'incident_time',
    'province',
    'municipality',
    'barangay',
    'overview',

    'affected_families',
    'affected_individuals',
    'partially_damaged',
    'totally_damaged',

    'open_ec',
    'displaced_family',
    'displaced_individual',

    'casualties',
    'dead',
    'injured',
    'missing',

    'dswd_cost',
    'lgu_cost',
    'ngo_cost',
    'partners_cost',

    'status',
    'created_by',
    'reviewed_by'
];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
