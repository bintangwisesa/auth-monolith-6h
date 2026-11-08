<?php // BookFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Catalog\Models\{Book, Publisher};
use Modules\Shared\ValueObjects\Isbn;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        $first12 = '978' . fake()->unique()->numerify('#########');

        return [
            'publisher_id' => Publisher::inRandomOrder()->value('id'),
            'title'        => fake()->sentence(3),
            'isbn'         => $first12 . Isbn::checkDigit($first12),
            'description'  => fake()->paragraph(),
            'stock'        => fake()->numberBetween(0, 10),
        ];
    }
}