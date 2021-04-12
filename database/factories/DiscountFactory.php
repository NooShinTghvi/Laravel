<?php

namespace Database\Factories;

use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DiscountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Discount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $current = Carbon::now();
        $type = $this->faker->randomElement(['PERCENT', 'CASH']);
        $money = $this->faker->randomElement([2, 5, 10, 20, 25, 45, 50]) * 1000;
        return [
            'code' => $this->faker->unique()->firstName . ' code',
            'type' => $this->faker->randomElement(['PERCENT', 'CASH']),
            'value' => ($type == 'PERCENT') ? $this->faker->randomElement([5, 10, 25, 50]) : $money,
            'maximum_value' => ($type == 'PERCENT') ? $this->faker->randomElement([2, 5, 10, 20, 25, 45, 50]) * 1000 : $money,
            'expire_date' => $current->addDays($this->faker->numberBetween(2, 30))->format('Y-m-d'),
            'count' => $this->faker->numberBetween(25, 45),
        ];
    }
}
