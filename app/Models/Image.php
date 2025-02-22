<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['image_1','product_id', 'image_2', 'image_3', 'image_4'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
