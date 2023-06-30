<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use CrudTrait;
    use HasFactory;
    protected $fillable = [
        'name'
    ];

    public function products(): HasMany {
        return $this->hasMany(Product::class);
    }
}
