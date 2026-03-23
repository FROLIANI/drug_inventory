<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'price_per_unit',
        'expiry_date',
        'status',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'quantity' => 'integer',
        'price_per_unit' => 'decimal:2',
        'status' => 'integer',
    ];
}
