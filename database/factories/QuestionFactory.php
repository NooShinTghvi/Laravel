<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $categories = Category::all('id');
        return [
            'question_text' => $this->faker->unique()->firstName . ' question',
            'choice1' => $this->faker->paragraph(),
            'choice2' => $this->faker->paragraph(),
            'choice3' => $this->faker->paragraph(),
            'choice4' => $this->faker->paragraph(),
            'answer' => $this->faker->randomElement(['1', '2', '3', '4']),
            'category_id' => $categories[$this->faker->numberBetween(1, sizeof($categories) - 1)]->id,
        ];
    }
}
