<?php

namespace Modules\Circulation\Services;

use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Circulation\Events\BookBorrowed;
use Modules\Circulation\Models\Loan;
use Modules\Circulation\Models\LoanItem;
use Modules\Shared\Contracts\{BookServiceInterface, MemberServiceInterface};
use Modules\Shared\Exceptions\InsufficientStockException;
use Modules\Shared\ValueObjects\{Denda, Isbn};

class BorrowingService
{
    public function __construct(
        private BookServiceInterface $books,
        private MemberServiceInterface $members,
    ) {}

    /** @param array<int, array{book_id:int, quantity:int}> $items */
    public function borrow(int $memberId, array $items, int $days = 7): Loan
    {
        $loan = DB::transaction(function () use ($memberId, $items, $days) {
            if (empty($items)) {
                throw new DomainException('Peminjaman minimal 1 buku');
            }
            if (!$this->members->getMemberForLoan($memberId)) {
                throw new DomainException("Anggota tidak ditemukan: {$memberId}");
            }

            $totalBooks = array_sum(array_column($items, 'quantity'));
            if (!$this->members->canBorrow($memberId, $totalBooks)) {
                throw new DomainException('Anggota tidak boleh meminjam (batas pinjam/status)');
            }

            $loan = Loan::create([
                'loan_number' => 'LN-' . strtoupper(Str::random(8)),
                'member_id'   => $memberId,
                'status'      => 'borrowed',
                'borrowed_at' => now(),
                'due_date'    => now()->addDays($days)->toDateString(),
            ]);

            foreach ($items as $item) {
                $book = $this->books->getBookForLoan($item['book_id']);
                if (!$book) {
                    throw new DomainException("Buku tidak ditemukan: {$item['book_id']}");
                }

                if (!$this->books->isStockAvailable($book['id'], $item['quantity'])
                    || !$this->books->reduceStock($book['id'], $item['quantity'])) {
                    throw new InsufficientStockException($book['title'], $book['stock'], $item['quantity']);
                }

                LoanItem::create([
                    'loan_id'    => $loan->id,
                    'book_id'    => $book['id'],
                    'book_title' => $book['title'],                       // snapshot
                    'book_isbn'  => (new Isbn($book['isbn']))->getValue(), // snapshot
                    'quantity'   => $item['quantity'],
                ]);
            }

            $this->members->recordLoanStarted($memberId, $totalBooks);

            return $loan->load('items');
        });

        // Event ditembakkan setelah transaksi COMMIT
        event(new BookBorrowed($loan));

        return $loan;
    }

    public function returnLoan(Loan $loan): Loan
    {
        return DB::transaction(function () use ($loan) {
            if ($loan->status === 'returned') {
                throw new DomainException('Peminjaman sudah dikembalikan');
            }

            $loan->load('items');
            $returnedAt = now();
            $fine = Denda::zero();

            foreach ($loan->items as $item) {
                $fine = $fine->add(Denda::calculate($loan->due_date, $returnedAt, $item->quantity));
                $this->books->increaseStock($item->book_id, $item->quantity);
            }

            $loan->update([
                'status'      => 'returned',
                'returned_at' => $returnedAt,
                'fine_amount' => $fine->getAmount(),
            ]);

            $this->members->recordLoanReturned($loan->member_id, $loan->items->sum('quantity'));

            return $loan->fresh('items');
        });
    }
}