<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leaves';
    protected $fillable = ['user_id', 'start_date', 'end_date', 'reason', 'status'];
    protected $casts = ['start_date' => 'datetime', 'end_date' => 'datetime'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }
}
