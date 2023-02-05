<?php

namespace MohammadZarifiyan\LaravelChart\Traits;

use MohammadZarifiyan\LaravelChart\Scopes\HasChartScope;

trait HasChart
{
	public static function bootHasChart()
	{
		static::addGlobalScope('hasChart', new HasChartScope);
	}
}
