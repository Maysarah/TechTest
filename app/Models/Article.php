<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'content',
        'created_at',  // Include created_at if you are using it explicitly
    ];

    public function images()
    {
        return $this->hasMany(Image::class);
    }
    public $timestamps = false;
}
