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
        $builder->macro('exportForChart', function (Builder $builder, string|Closure $column, CarbonPeriod $period, Closure $closure) {
            $stack = new Collection;

            foreach ($period as $item) {
                $end = $item->clone()->add($period->interval);
                $between = CarbonPeriod::start($item)->setEndDate($end);
                $clone = $builder->clone();

                if ($column instanceof Closure) {
                    $column($clone, $between);
                }
                else {
                    $clone->whereBetween($column, $between);
                }

                $stack->push($closure($clone));
            }

            return $stack;
        });
    }
}
