<?php

namespace Modules\Catalog\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Shared\ValueObjects\Isbn;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['publisher_id', 'title', 'isbn', 'description', 'stock'];

    protected static function newFactory() { return BookFactory::new(); }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'book_category')->withPivot('added_at');
    }

    // Value Object: validasi ISBN saat disimpan
    public function setIsbnAttribute(string|Isbn $value): void
    {
        $this->attributes['isbn'] = (new Isbn((string) $value))->getValue();
    }

    public function scopeAvailable(Builder $q): Builder
    {
        return $q->where('stock', '>', 0);
    }

    public function scopeSearch(Builder $q, string $keyword): Builder
    {
        return $q->where(function ($w) use ($keyword) {
            $w->where('title', 'like', "%{$keyword}%")
              ->orWhere('isbn', 'like', "%{$keyword}%")
              ->orWhereHas('authors', fn ($a) => $a->where('name', 'like', "%{$keyword}%"));
        });
    }

    public function scopeInCategory(Builder $q, int $categoryId): Builder
    {
        return $q->whereHas('categories', fn ($c) => $c->where('categories.id', $categoryId));
    }

    public function isInStock(int $quantity = 1): bool
    {
        return $this->stock >= $quantity;
    }
}