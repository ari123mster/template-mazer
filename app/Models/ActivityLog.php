<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
    protected $table = 'activity_logs';
    protected $guarded = [];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'headers'  => 'array',
    ];
    public function user()
{
    return $this->belongsTo(User::class);
}
}
