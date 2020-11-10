<?php

namespace Database\Factories;

use App\Http\Controllers\UserController;
use App\Models\Group;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

class GroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Group::class;

    private $users;
    private $numberOfUsers;

    public function __construct($count = null, ?Collection $states = null, ?Collection $has = null, ?Collection $for = null, ?Collection $afterMaking = null, ?Collection $afterCreating = null, $connection = null)
    {
        parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection);

        $userController = new UserController();
        $this->users = $userController->getUsers();
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
            'name' => $this->faker->firstName . ' group',
            'user_id' => $this->users[random_int(1, $this->numberOfUsers - 1)]->id,
        ];
    }
}
