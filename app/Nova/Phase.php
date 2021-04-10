<?php

namespace App\Nova;

use App\Events\EvaluationEvent;
use App\Events\ResultsEvent;
use Illuminate\Http\Request;
use Laraning\NovaTimeField\TimeField;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use NovaButton\Button;

class Phase extends Resource
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
    public static $model = \App\Models\Phase::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

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
            Number::make('number')
                ->rules('required', 'integer'),
            BelongsTo::make('Exam', 'Exam', 'App\Nova\Exam'),
            Date::make('Date', 'date')
                ->rules('required', 'date'),
            TimeField::make('Time Start', 'time_start')
                ->rules('required', 'date_format:H:i'),
            TimeField::make('Time End', 'time_end')
                ->rules('required', 'date_format:H:i'/*, 'after:time_start'*/),
            Number::make('Duration', 'duration')
                ->rules('required')
                ->sortable(),
            Number::make('Negative Score', 'negative_score')
                ->rules('required', 'integer')
                ->hideFromIndex(),
            Image::make('Photo', 'image_path')
                ->disk('public')
                ->rules('max:2500')
                ->hideFromIndex(),
            File::make('File of Question', 'file_of_question_path')
                ->disk('public')
                ->rules('max:2500')
                ->hideFromIndex(),
            File::make('File_of Answer', 'file_of_answer_path')
                ->disk('public')
                ->rules('max:2500')
                ->hideFromIndex(),
            BelongsToMany::make('Lesson', 'Lessons', 'App\Nova\Lesson')->fields(function () {
                return [];
            }),
            Button::make('Evaluation')
//                ->confirm('Are you sure?')
                ->event(EvaluationEvent::class),
            Button::make('Results')
//                ->confirm('Are you sure?')
                ->event(ResultsEvent::class),
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
