<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Catalog\Models\{Author, Book, Category, Publisher};
use Modules\Membership\Models\{Member, Role};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin  = Role::create(['name' => 'Administrator', 'slug' => 'admin']);
        $member = Role::create(['name' => 'Member', 'slug' => 'member']);

        Member::factory()->count(5)->create()
            ->each(fn ($m) => $m->roles()->attach($member));
        Member::factory()->create(['name' => 'Admin Perpus', 'email' => 'admin@perpus.test'])
            ->roles()->attach($admin);

        Publisher::factory()->count(5)->create();
        $authors    = Author::factory()->count(10)->create();
        $categories = Category::factory()->count(6)->create();

        // Sub-kategori (self-referencing)
        $categories->take(3)->each(fn ($parent) =>
            Category::factory()->create(['parent_id' => $parent->id])
        );

        Book::factory()->count(30)->create()->each(function ($book) use ($authors, $categories) {
            $book->authors()->attach($authors->random(rand(1, 2))->pluck('id'));
            $book->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->mapWithKeys(
                    fn ($id) => [$id => ['added_at' => now()]]
                )
            );
        });
    }
}