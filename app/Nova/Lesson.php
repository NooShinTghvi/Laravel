<?php

namespace App\Nova;

use App\Rules\numberOfQuestionRule;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

class Lesson extends Resource
{
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Exam';
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Lesson::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    public function subtitle()
    {
        return $this->id;
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name', 'name')
                ->rules('required', 'max:100')
                ->sortable(),
            Number::make('Number of Questions', 'number_of_questions')
                ->min(1)->max(100)
                ->rules('required', 'integer')
                ->sortable(),
            Number::make('Coefficient', 'coefficient')
                ->min(1)->max(100)
                ->rules('required')
                ->sortable(),
            BelongsTo::make('Field', 'Field', 'App\Nova\Field'),
            BelongsTo::make('EducationBase', 'EducationBase', 'App\Nova\EducationBase'),
            BelongsTo::make('Category', 'Category', 'App\Nova\Category'),
            Textarea::make('Description', 'description')
                ->rows(3)
                ->nullable(),
            BelongsToMany::make('Question', 'Questions', 'App\Nova\Question')
                ->fields(function () {
                    return [];
                }),
            BelongsToMany::make('Phase', 'Phases', 'App\Nova\Phase')->fields(function () {
                return [];
            }),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
