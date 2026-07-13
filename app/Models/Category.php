<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'station', // kitchen | barista — sumber routing BR-003
    ];

    /** Menu di dalam kategori ini. */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }
}
