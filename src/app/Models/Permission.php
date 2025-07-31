<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'name',
        'needs',
        'description',
        'start_date',
        'end_date',
        'comment',
        'status',
    ];

    protected function casts() : array {
        return [
            'id' => 'string',
        ];
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
