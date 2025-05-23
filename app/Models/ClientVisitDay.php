<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientVisitDay extends Model
{
    use HasFactory;

    protected $table = 'client_visit_days';

    protected $fillable = [
        'client_id',
        'position',
        'visit_day',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'visit_day' => 'string',
    ];

    /**
     * Get the client that owns this visit day.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Scope a query to filter by visit day.
     */
    public function scopeOnDay($query, $day)
    {
        if (!$day) return $query;

        return $query->where('visit_day', $day);
    }

    public function scopeByEmployee($query, $employeeId)
    {
        if (!$employeeId) return $query;

        return $query->whereHas('client', function ($q) use ($employeeId) {
            $q->where('employee_id', $employeeId);
        });
    }
}
