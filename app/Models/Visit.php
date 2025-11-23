<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'pengawas_id',
        'approved_by',
        'note',
        'visit_date',
        'visit_time',
        'status',
        'accepted_at',
        'declined_at',
        'decline_reason',
        'report_file',
        'report_summary',
        'completed_at',
    ];

    protected $casts = [
        'visit_date'   => 'date',
        // Jika kolom di DB bertipe DATETIME, cast berikut aman:
        'visit_time'   => 'datetime:H:i',
        // Jika kolom di DB bertipe TIME, lebih aman pakai string:
        // 'visit_time' => 'string',
        'accepted_at'  => 'datetime',
        'declined_at'  => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }

    public function pengawas()
    {
        return $this->belongsTo(\App\Models\User::class, 'pengawas_id');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // Helper badge status untuk Blade
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'scheduled' => 'bg-blue-600',
            'rejected'  => 'bg-red-600',
            'done'      => 'bg-green-600',
            default     => 'bg-gray-500', // requested / lainnya
        };
    }
}
