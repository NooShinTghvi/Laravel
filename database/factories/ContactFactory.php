<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contact::class;

    private $permitted_chars = '0123456789012345678901234567890123456789';
    private $users;
    private $numberOfUsers;

    public function __construct($count = null, ?\Illuminate\Support\Collection $states = null, ?\Illuminate\Support\Collection $has = null, ?\Illuminate\Support\Collection $for = null, ?\Illuminate\Support\Collection $afterMaking = null, ?\Illuminate\Support\Collection $afterCreating = null, $connection = null)
    {
        parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection);

        $this->users = User::all('id');
        $this->numberOfUsers = sizeof($this->users);
    }

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws Exception
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'phone' => '09' . substr(str_shuffle($this->permitted_chars), 0, 9),
            'user_id' => $this->users[random_int(1, $this->numberOfUsers - 1)],
        ];
    }
}
