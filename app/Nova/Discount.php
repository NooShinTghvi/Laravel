<?php

namespace App\Nova;

use Aloko\PersianDatepicker\PersianDate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class Discount extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Discount::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'code';

    public function subtitle()
    {
        return $this->type;
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'code',
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
            Text::make('Code', 'code')
                ->rules('required', 'max:15')
                ->creationRules('unique:discounts,code')
                ->updateRules('unique:discounts,code,{{resourceId}}')
                ->help('کدی که تعریف می شود می بایست منحصر به فرد باشد')
                ->sortable(),
            Select::make('Type', 'type')->options([
                'PERCENT' => 'Percent',
                'CASH' => 'Cash',
            ])->sortable()
                ->rules('required', Rule::in(['PERCENT', 'CASH'])),
            Number::make('Value', 'value')
                ->sortable()
                ->rules('required', 'integer'),
            Number::make('Maximum value', 'maximum_value')
                ->sortable()
                ->rules('required', 'integer'),
            Number::make('Count', 'count')
                ->sortable()
                ->rules('required', 'integer'),
            Number::make('Used number', 'used_number')
                ->sortable()
                ->rules('required', 'integer'),
            Date::make('Expire date', 'expire_date')
                ->sortable()
                ->rules('required', 'date'),
            BelongsToMany::make('Exams', 'exams')->fields(function () {
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
