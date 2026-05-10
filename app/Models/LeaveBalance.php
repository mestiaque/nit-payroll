<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $table = 'leave_balances';

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'year',
        'total_days',
        'used_days',
        'remaining_days',
    ];

    protected $casts = [
        'year' => 'integer',
        'total_days' => 'decimal:1',
        'used_days' => 'decimal:1',
        'remaining_days' => 'decimal:1',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
