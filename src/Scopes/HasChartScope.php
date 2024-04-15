<?php

namespace MohammadZarifiyan\LaravelChart\Scopes;

use Carbon\CarbonPeriod;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Collection;

class HasChartScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        //
    }

    public function extend(Builder $builder)
    {
        $builder->macro('exportForChart', function (Builder $builder, CarbonPeriod $period, Closure $closure) {
            $stack = new Collection;

            foreach ($period as $start) {
                $end = $start->clone()->add($period->interval);
                $between = CarbonPeriod::start($start)->setEndDate($end);
                $clone = $builder->clone();

                $stack->push($closure($clone, $between));
            }

            return $stack;
        });
    }
}
