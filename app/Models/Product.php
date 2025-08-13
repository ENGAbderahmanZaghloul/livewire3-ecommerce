<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'images',
        'is_active',
        'price',
        'is_featured',
        'in_stock',
        'on_sale',
        'description',
    ];
        protected $casts = [
            'images' => 'array',

        ];
        public function category()
        {
            return $this->belongsTo(Category::class);
        }
        public function brand()
        {
            return $this->belongsTo(Brand::class);
        }
        public function OrderItems()
        {
            return $this->hasMany(OrderItem::class);
        }
}
