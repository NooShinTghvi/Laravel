<?php

namespace App\Nova;

use App\Events\EvaluationEvent;
use App\Events\TestParticipantsEvent;
use Cimpleo\NovaSummernote\NovaSummernote;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use NovaButton\Button;

class Exam extends Resource
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
    public static $model = \App\Models\Exam::class;

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
            Number::make('Number of Phases', 'number_of_phases')
                ->min(1)->max(100)
                ->rules('required', 'integer')
                ->sortable(),
            Select::make('Day of Holding', 'day_of_holding')->options([
                'شنبه' => 'شنبه',
                'یک شنبه' => 'یک شنبه',
                'دو شنبه' => 'دو شنبه',
                'سه شنبه' => 'سه شنبه',
                'چهار شنبه' => 'چهار شنبه',
                'پنج شنبه' => 'پنج شنبه',
                'جمعه' => 'جمعه',
            ])
                ->rules('required', Rule::in(['شنبه', 'یک شنبه', 'دو شنبه', 'سه شنبه', 'چهارشنبه', 'پنج شنبه', 'جمعه'])),
            BelongsTo::make('Field', 'Field', 'App\Nova\Field'),
            BelongsTo::make('EducationBase', 'EducationBase', 'App\Nova\EducationBase'),
            Number::make('Price', 'price')
                ->rules('required', 'integer')
                ->sortable(),
            Image::make('Photo', 'image_path')
                ->disk('public')
                ->rules('max:2500')
                ->hideFromIndex(),
            NovaSummernote::make('Description', 'description')
                ->hideFromIndex()
                ->nullable(),
            File::make('Description File', 'description_file')
                ->disk('public')
                ->rules('max:2500')
                ->hideFromIndex(),
            HasMany::make('Phases', 'Phases', 'App\Nova\Phase'),
            HasMany::make('LessonTags', 'LessonTags', 'App\Nova\LessonTag'),
//            Button::make('Text')->link(route('exam.test.participants',$request->all()))
            Button::make('Participants')
                ->event(TestParticipantsEvent::class),
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
