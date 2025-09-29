<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit; // ✅ garder Fit
// ❌ ne pas importer Spatie\Image\Enums\Format (absent de ta version)

class Article extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'is_published',
        'published_at',
        'user_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->useDisk('public')->singleFile();
        $this->addMediaCollection('images')->useDisk('public');
    }

    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        // Vignette
        $this->addMediaConversion('thumb_webp')
            ->fit(Fit::Crop, 400, 250) // ✅ enum Fit OK
            ->format('webp')           // ✅ string pour ta version
            ->optimize()
            ->nonQueued();

        // Couverture
        $this->addMediaConversion('cover_webp')
            ->fit(Fit::Crop, 1600, 900)
            ->format('webp')
            ->optimize()
            ->nonQueued();
    }
}
