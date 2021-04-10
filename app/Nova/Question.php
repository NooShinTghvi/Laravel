<?php

namespace App\Nova;

use Cimpleo\NovaSummernote\NovaSummernote;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;

class Question extends Resource
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
    public static $model = \App\Models\Question::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'question_text';

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
        'id', 'question_text'
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
            NovaSummernote::make('Question', 'question_text')
                ->rules('required')
                ->hideFromIndex(),
            NovaSummernote::make('First Choice', 'choice1')
                ->rules('required')
                ->hideFromIndex(),
            NovaSummernote::make('Second Choice', 'choice2')
                ->rules('required')
                ->hideFromIndex(),
            NovaSummernote::make('Third Choice', 'choice3')
                ->rules('required')
                ->hideFromIndex(),
            NovaSummernote::make('Fourth Choice', 'choice4')
                ->rules('required')
                ->hideFromIndex(),
            Select::make('Answer', 'answer')->options([
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
            ])->sortable()
                ->rules('required', Rule::in(['1', '2', '3', '4']))
                ->hideFromIndex(),
            BelongsTo::make('Category', 'Category', 'App\Nova\Category'),
            BelongsToMany::make('Lesson', 'Lessons', 'App\Nova\Lesson')->fields(function () {
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
