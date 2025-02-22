<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'discount', 'reviews', 'rate', 'sell'];

    public function images()
    {
        return $this->hasOne(Image::class, 'product_id');
    }
}
