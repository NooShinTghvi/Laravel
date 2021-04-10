<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;

class Student extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'mobile';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'mobile',
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
            Text::make('First Name', 'first_name')
                ->sortable()
                ->rules('required', 'max:100'),
            Text::make('Last Name', 'last_name')
                ->sortable()
                ->rules('required', 'max:100'),
            Text::make('Melli Code', 'melli_code')
                ->sortable()
                ->rules('max:10')
                ->creationRules('unique:users,melli_code')
                ->updateRules('unique:users,melli_code,{{resourceId}}')
                ->hideFromIndex(),
            Text::make('Mobile', 'mobile')
                ->sortable()
                ->rules('required', 'max:11')
                ->creationRules('unique:users,mobile')
                ->updateRules('unique:users,mobile,{{resourceId}}'),
            Text::make('Email', 'email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}')
                ->hideFromIndex(),
            BelongsTo::make('Field', 'Field', 'App\Nova\Field')
                ->sortable(),
            BelongsTo::make('EducationBase', 'EducationBase', 'App\Nova\EducationBase')
                ->sortable(),
            BelongsTo::make('City', 'City', 'App\Nova\City')
                ->sortable(),
            Image::make('Melli Image', 'melli_image_path')
                ->disk('public')
                ->rules('max:2500')
                ->hideFromIndex(),
            Boolean::make('Verify', 'isAcceptable')
                ->sortable(),
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
