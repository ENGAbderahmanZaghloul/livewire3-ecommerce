<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Address;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'grand_total',
        'payment_method',
        'payment_status',
        'status',
        'currency',
        'notes',
        'shipping_amount',
        'shipping_method',
    ];
    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function OrderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function address()
    {
        return $this->hasOne(Address::class);
    }
}
