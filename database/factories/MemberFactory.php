<?php // MemberFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Membership\Models\Member;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            'member_number' => 'MBR-' . fake()->unique()->numerify('######'),
            'name'          => fake()->name(),
            'email'         => fake()->unique()->safeEmail(),
            'status'        => 'active',
            'max_loans'     => 3,
            'active_loans'  => 0,
        ];
    }
}