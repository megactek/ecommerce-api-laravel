<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;
    protected $fillable = ['status', 'user_id', 'location_id', 'total', 'date'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function location()
    {
        return $this->belongsTo(Locations::class, 'location_id');
    }
    public function items()
    {
        return $this->hasMany(OrdersItems::class);
    }
}
