<?php

namespace Database\Factories;

use App\Http\Controllers\ContactController;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

class ContactGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContactGroup::class;

    private $groups;
    private $numberOfGroups;
    public $contactController;

    public function __construct($count = null, ?Collection $states = null, ?Collection $has = null, ?Collection $for = null, ?Collection $afterMaking = null, ?Collection $afterCreating = null, $connection = null)
    {
        parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection);

        $this->groups = Group::all();
        $this->numberOfGroups = sizeof($this->groups);
        $this->contactController = new ContactController();
    }

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition()
    {
        $group = $this->groups[random_int(1, $this->numberOfGroups - 1)];
        $contacts = $this->contactController->findContactsFromOwnerOfGroup($group);
        $numberOfContacts = sizeof($contacts);
        return [
            'contact_id' => $contacts[random_int(1, $numberOfContacts - 1)]->id,
            'group_id' => $group->id,
        ];
    }
}
