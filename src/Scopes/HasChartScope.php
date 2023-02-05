<?php

namespace MohammadZarifiyan\LaravelChart\Scopes;

use Carbon\Carbon;
use Carbon\CarbonInterval;
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
		$builder->macro('exportForChart', function (Builder $builder, string $column, int $limit, Carbon $start, Carbon $end, CarbonInterval $interval, string $method, ...$params) {
			if ($limit < 1) {
				throw new InvalidArgumentException('limit must not be less than one.');
			}
			
			$stack = new Collection;
			
			for ($i = 0; $i < $limit; $i++) {
				$query = $builder->whereBetween($column, [
					$start,
					$start = $start->add($interval)->min($end)
				]);
				
				$stack->push(
					$query->{$method}(...$params)
				);
			}
			
			return $stack;
		});
	}
}
