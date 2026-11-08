<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Catalog\Models\Author;
use Modules\Catalog\Models\Book;
use Modules\Catalog\Models\BookCategory;
use Modules\Catalog\Models\Publisher;
use Modules\Circulation\Models\Loan;
use Modules\Circulation\Services\LoanService;
use Modules\Membership\Models\Member;
use Modules\Membership\Models\MemberRole;

class LibrarySeeder extends Seeder
{
    public function run(LoanService $loans): void
    {
        // ---------- Membership ----------
        $adminRole = MemberRole::firstOrCreate(['slug' => 'admin'], ['name' => 'Administrator']);
        $memberRole = MemberRole::firstOrCreate(['slug' => 'member'], ['name' => 'Anggota']);

        $members = collect(range(1, 10))->map(function () use ($memberRole) {
            $member = Member::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
            ]);
            $member->roles()->attach($memberRole->id);

            return $member;
        });
        $members->first()->roles()->attach($adminRole->id);

        // ---------- Catalog ----------
        $authors = collect(range(1, 6))->map(fn () => Author::create(['name' => fake()->name()]));
        $publishers = collect(range(1, 3))->map(fn () => Publisher::create(['name' => 'Penerbit ' . fake()->company()]));

        $parents = collect(['Fiksi', 'Non-Fiksi', 'Teknologi'])->mapWithKeys(
            fn ($name) => [$name => BookCategory::create(['name' => $name, 'slug' => Str::slug($name)])]
        );

        $children = collect([
            ['Novel', 'Fiksi'], ['Cerpen', 'Fiksi'],
            ['Sains', 'Non-Fiksi'],
            ['Pemrograman', 'Teknologi'], ['Basis Data', 'Teknologi'],
        ])->map(fn ($c) => BookCategory::create([
            'name' => $c[0],
            'slug' => Str::slug($c[0]),
            'parent_id' => $parents[$c[1]]->id,
        ]));

        $books = collect(range(1, 30))->map(function () use ($authors, $publishers, $children) {
            $book = Book::create([
                'title' => Str::title(fake()->words(3, true)),
                'isbn' => fake()->unique()->numerify('978##########'),
                'author_id' => $authors->random()->id,
                'publisher_id' => $publishers->random()->id,
                'published_year' => fake()->numberBetween(2005, 2025),
                'stock' => fake()->numberBetween(3, 10),
            ]);
            $book->categories()->attach(
                $children->random(rand(1, 2))->pluck('id')->all(),
                ['added_at' => now()]
            );

            return $book;
        });

        // ---------- Circulation: peminjaman lewat service (memakai transaction) ----------
        $members->take(6)->each(function (Member $member) use ($loans, $books) {
            $bookIds = $books->random(rand(1, 3))->pluck('id')->all();
            $loans->borrow($member->id, $bookIds);
        });

        // Satu peminjaman dibuat terlambat 10 hari, untuk menguji denda
        Loan::first()?->update([
            'loan_date' => now()->subDays(17)->toDateString(),
            'due_date' => now()->subDays(10)->toDateString(),
        ]);

        // ---------- Data khusus pengujian ----------
        Book::create([
            'title' => 'Buku Stok Kosong',
            'isbn' => '9780000000001',
            'author_id' => $authors->first()->id,
            'publisher_id' => $publishers->first()->id,
            'published_year' => 2020,
            'stock' => 0,
        ]);

        Book::create([
            'title' => 'Buku Stok Satu',
            'isbn' => '9780000000002',
            'author_id' => $authors->first()->id,
            'publisher_id' => $publishers->first()->id,
            'published_year' => 2021,
            'stock' => 1,
        ]);
    }
}
