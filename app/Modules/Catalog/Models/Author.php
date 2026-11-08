<?php
namespace Modules\Catalog\Models;

use Database\Factories\AuthorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    protected static function newFactory() { return AuthorFactory::new(); }

    public function books() { return $this->belongsToMany(Book::class); }
}