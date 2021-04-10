<?php

namespace Database\Factories;

use App\Models\LessonTag;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonTagFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LessonTag::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->colorName . ' lsn tag',
        ];
    }
}
