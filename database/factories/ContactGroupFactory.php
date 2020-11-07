<?php

namespace Database\Factories;

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

    private $contacts;
    private $numberOfContacts;
    private $groups;
    private $numberOfGroups;

    public function __construct($count = null, ?Collection $states = null, ?Collection $has = null, ?Collection $for = null, ?Collection $afterMaking = null, ?Collection $afterCreating = null, $connection = null)
    {
        parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection);

        $this->contacts = Contact::all('id');
        $this->numberOfContacts = sizeof($this->contacts);
        $this->groups = Group::all('id');
        $this->numberOfGroups = sizeof($this->groups);
    }

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition()
    {
        return [
            'contact_id' => $this->contacts[random_int(1, $this->numberOfContacts - 1)],
            'group_id' => $this->groups[random_int(1, $this->numberOfGroups - 1)],
        ];
    }
}
