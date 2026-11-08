<?php

namespace Modules\Catalog\Services;

use Modules\Catalog\Models\Book;
use Modules\Shared\Contracts\BookServiceInterface;

class BookService implements BookServiceInterface
{
    public function getBookForLoan(int $bookId): ?array
    {
        $book = Book::find($bookId);

        return $book ? [
            'id'    => $book->id,
            'title' => $book->title,
            'isbn'  => $book->isbn,
            'stock' => $book->stock,
        ] : null;
    }

    public function isStockAvailable(int $bookId, int $quantity): bool
    {
        return (bool) Book::find($bookId)?->isInStock($quantity);
    }

    public function reduceStock(int $bookId, int $quantity): bool
    {
        // Atomik: hanya berhasil jika stok masih cukup
        return Book::whereKey($bookId)
            ->where('stock', '>=', $quantity)
            ->decrement('stock', $quantity) > 0;
    }

    public function increaseStock(int $bookId, int $quantity): void
    {
        Book::whereKey($bookId)->increment('stock', $quantity);
    }
}