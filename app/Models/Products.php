<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'brand_id', 'name', 'price', 'discount', 'is_available', 'is_trendy', 'image', 'amount', 'quantity'];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brands::class, 'brand_id');
    }
}
