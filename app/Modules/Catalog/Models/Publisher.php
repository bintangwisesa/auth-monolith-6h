<?php
namespace Modules\Catalog\Models;

use Database\Factories\PublisherFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    protected static function newFactory() { return PublisherFactory::new(); }

    public function books() { return $this->hasMany(Book::class); }
}