<?php

namespace Modules\Circulation\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Circulation\Events\BookBorrowed;

class LogBookBorrowed
{
    public function handle(BookBorrowed $event): void
    {
        $loan = $event->loan->loadMissing('items');

        Log::info('Buku dipinjam', [
            'loan_number' => $loan->loan_number,
            'member_id'   => $loan->member_id,
            'due_date'    => $loan->due_date->toDateString(),
            'books'       => $loan->items->pluck('book_title')->all(),
        ]);
    }
}