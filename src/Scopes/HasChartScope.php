<?php

namespace MohammadZarifiyan\LaravelChart\Scopes;

use Carbon\CarbonPeriod;
use Closure;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class HasChartScope implements Scope
{
	public function apply(Builder $builder, Model $model)
	{
		//
	}
	
	public function extend(Builder $builder)
	{
		$builder->macro('exportForChart', function (Builder $builder, string|Closure $column, int $limit, DateTimeInterface $start, $interval, Closure $closure) {
			if ($limit < 1) {
				throw new InvalidArgumentException('limit must not be less than one.');
			}
			
			$stack = new Collection;
			
			for ($i = 0; $i < $limit; $i++) {
                $clone = $builder->clone();

                $period = CarbonPeriod::start($start)->setDateInterval($interval);

                if ($column instanceof Closure) {
                    $column($clone, $period);
                }
                else {
                    $clone->whereBetween($column, $period);
                }

				$stack->push($closure($clone));
                $start = $period->getEndDate();
			}
			
			return $stack;
		});
	}
}
