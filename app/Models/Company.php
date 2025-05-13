<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'name', 'industry', 'description', 'website', 'tagline', 'logo', 'brand_color', 'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'company_id');
    }

    public static function getMyCompanies()
    {
        $companies = Company::where('user_id', auth()->user()->id)->get();

        return $companies;
    }
}
