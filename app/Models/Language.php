<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Language extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::created(function () {
            Cache::forget('languages');
        });

        static::updated(function () {
            Cache::forget('languages');
        });

        static::deleted(function () {
            Cache::forget('languages');
        });
    }
}
