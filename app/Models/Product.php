<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
     public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}