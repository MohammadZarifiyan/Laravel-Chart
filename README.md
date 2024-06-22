# Laravel Chart
This Laravel package helps you to export data for charts using Eloquent ORM easily.
## Installation
To install package, just run the following command in the root of your project:
```shell
composer require mohammad-zarifiyan/laravel-chart
```
## Implementation
First you need to give trait `MohammadZarifiyan\LaravelChart\Traits\HasChart` to your model.
Then use the `exportForChart` method to extract the information.
1. The first parameter of this method must be an instance of `Carbon\CarbonPeriod` that specifies the beginning and end of the total time period.
2. The second parameter of this method must be a closure that its first parameter is an instance of `Illuminate\Database\Eloquent\Builder` and its second parameter is an instance of `Carbon\CarbonPeriod`. In this closure, you must apply conditions to the `Illuminate\Database\Eloquent\Builder` that limit the data to the time period given by `Carbon\CarbonPeriod` and then return the desired information for your chart.

The result of `exportForChart` method is an instance of `Illuminate\Support\Collection` that includes the data you returned in the closure, so you can use them in your charts.

### Example
In the following example, we have calculated the sum of the `amount` column of the invoices at the end of **each day** in the period of **one week ago until now**.
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MohammadZarifiyan\LaravelChart\Traits\HasChart;

class Invoice extends Model
{
    use HasChart;

    protected $fillable = [
        'amount',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function payment()
        return $this->belongsTo(Payment::class);
    }
}
```
```php
<?php

use App\Models\Invoice;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

$start = Carbon::now()->subDays(6)->startOfDay();
$end = Carbon::now();
$interval = CarbonInterval::day();
$period = CarbonPeriod::start($start)
    ->setEndDate($end)
    ->setDateInterval($interval);

$output = Invoice::exportForChart($period, function (Builder $builder, CarbonPeriod $period) {
    $builder->whereBetween('created_at', $period);
    
    return $builder->sum('amount');
});
```
Above code output will be something like this:
```php
[
    2,  // Sum amount, 6 days ago (all day)
    50, // Sum amount, 5 days ago (all day)
    49, // Sum amount, 4 days ago (all day)
    85, // Sum amount, 3 days ago (all day)
    140,// Sum amount, 2 days ago (all day)
    110,// Sum amount, 1 days ago (all day)
    80, // Sum amount, today (till now)
]
```
You can also filter the table information based on a column in a relation. In the example below, we get the sum of the invoices amount based on their **payment time**.
```php
<?php

use App\Models\Invoice;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

$start = Carbon::now()
    ->subDays(6)
    ->startOfDay();
$end = Carbon::now();
$interval = CarbonInterval::day();
$period = CarbonPeriod::start($start)
    ->setEndDate($end)
    ->setDateInterval($interval);
    
$output_for_chart = Invoice::exportForChart($period, function (Builder $builder, CarbonPeriod $period) {
    $builder->whereRelation('payment', fn (Builder $builder) => $builder->whereBetween('paid_at', $period));
    
    return $builder->sum('amount');
});
```
