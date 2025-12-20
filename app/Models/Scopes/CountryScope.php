<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CountryScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $countryCode = request()->header('X-Country-Code');

        if (!$countryCode) {
            return;
        }

        $builder->where(
            $model->getTable() . '.country_code',
            strtoupper($countryCode)
        );
    }
}
