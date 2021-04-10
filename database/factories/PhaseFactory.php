<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\Phase;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Phase::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $exams = Exam::all('id');
        return [
            'name' => $this->faker->unique()->firstName . ' phase',
            'number' => $this->faker->numberBetween(1, 5),
            'exam_id' => $exams[$this->faker->numberBetween(1, sizeof($exams) - 1)]->id,
            'date' => $this->faker->date(),
            'time_start' => $this->faker->randomElement([06, 07, 10]) . ':00',
            'time_end' => $this->faker->randomElement([15, 18, 20, 23]) . ':59',
            'duration' => $this->faker->randomElement([60, 75, 90, 120, 150]),
            'negative_score' => $this->faker->numberBetween(2, 6),
        ];
    }
}
