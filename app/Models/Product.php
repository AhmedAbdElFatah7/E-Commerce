<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'discount', 'reviews', 'rate', 'sell' , 'sub_category' , 'category' , 'trend'];

    protected $appends = ['price_after_discount']; 
    public function getPriceAfterDiscountAttribute()
    {
        $discountAmount = ($this->price * $this->discount) / 100; 
        return round($this->price - $discountAmount, 2); 
    }
    public function images()
    {
        return $this->hasOne(Image::class, 'product_id');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // ✅ حساب متوسط التقييمات
    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('stars'), 2) ?? 0;
    }
}
