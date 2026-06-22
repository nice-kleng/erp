<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'phone',
        'address',
        'city',
        'logo',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function (Store $store) {
            if (empty($store->slug)) {
                $store->slug = static::generateUniqueSlug($store->name, $store->city);
            }
        });

        static::saving(function (Store $store) {
            if (empty($store->slug)) {
                $store->slug = static::generateUniqueSlug($store->name, $store->city);
            }
        });
    }

    public static function generateUniqueSlug(string $name, ?string $city = null): string
    {
        $slug = Str::slug($name);

        if (! static::where('slug', $slug)->exists()) {
            return $slug;
        }

        if ($city) {
            $slugWithCity = $slug.'-'.Str::slug($city);

            if (! static::where('slug', $slugWithCity)->exists()) {
                return $slugWithCity;
            }

            $slug = $slugWithCity;
        }

        $base = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'store_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
