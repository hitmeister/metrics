# Metrics  [![Build Status](https://travis-ci.org/hitmeister/metrics.svg?branch=master)](https://travis-ci.org/hitmeister/metrics)

[![Latest Stable Version](http://img.shields.io/github/release/hitmeister/metrics.svg)](https://packagist.org/packages/hitmeister/metrics)
[![Coverage Status](http://img.shields.io/coveralls/hitmeister/metrics.svg)](https://coveralls.io/r/hitmeister/metrics?branch=master)
[![Total Downloads](http://img.shields.io/packagist/dt/hitmeister/metrics.svg)](https://packagist.org/packages/hitmeister/metrics)

Metrics forwarder for PHP. We can forward to [statsd](https://github.com/etsy/statsd) and [InfluxDB](https://influxdb.com/).

## Installation

You can install the component in two different ways:

* Install it via Composer: **hitmeister/metrics** on [Packagist](https://packagist.org/packages/hitmeister/metrics);
* Use the [official Git repository](https://github.com/hitmeister/metrics): `git clone git@github.com:hitmeister/metrics.git`.

## Usage

Here is some examples how you can use this library.

### Stats Daemon

```php
use Hitmeister\Component\Metrics\Collector;
use Hitmeister\Component\Metrics\Handler\StatsDaemonHandler;

// Create new handler
$handler = new StatsDaemonHandler('127.0.0.1', 8125);

// Create new collector and set handler
$collector = new Collector();
$collector->setHandler($handler);

// Increment one stats
$collector->increment('hello_world');

// Increments `one_long_task` counter and reports used memory and elapsed time
$collector->closure('one_long_task', function(){
    for ($i = 0; $i < 1000; $i++) {
        usleep(100);
    }
});
```

### InfluxDB 0.9.x UDP

#### Important notice

If you are using InfluxDb >= 0.9.3 you have to specify new Formatter for handler.

```php
use \Hitmeister\Component\Metrics\Formatter\InfluxDb\LineFormatter;
// .... 
$handler = new UdpHandler('127.0.0.1', 4444);
$handler->setFormatter(new LineFormatter());
// ....
```

If you are using InfluxDB <= 0.9.2 you don't have to change anything.

```php
use Hitmeister\Component\Metrics\Collector;
use Hitmeister\Component\Metrics\Handler\InfluxDb\UdpHandler;

// Create new handler
$handler = new UdpHandler('127.0.0.1', 4444);

// Create new collector and set handler
$collector = new Collector();
$collector->setHandler($handler);

// Set global tags
$collector->setTags([
    'env' => 'development',
    'instance' => 'web01',
]);

// Increment one stats
$collector->increment('hello', ['operation' => 'world']);

// Increments `one_long_task` counter and reports used memory and elapsed time
$collector->closure('one_long_task', function(){
    for ($i = 0; $i < 1000; $i++) {
        usleep(100);
    }
});
```

### Flush metrics on script shutdown

```php
use Hitmeister\Component\Metrics\Buffer\OnShutdownBuffer;
use Hitmeister\Component\Metrics\Collector;
use Hitmeister\Component\Metrics\Handler\StatsDaemonHandler;

// Create new handler
$handler = new StatsDaemonHandler('127.0.0.1', 8125);

// Create buffer
$buffer = new OnShutdownBuffer();
$buffer->setHandler($handler);

// Create new collector and set buffer
$collector = new Collector();
$collector->setBuffer($buffer);

// Increment some stats
for ($i = 0; $i < 100; $i++) {
    $collector->increment('stats_'.$i);
}

// All metrics will be flushed to the stats daemon after script shutdown
// It uses register_shutdown_function function under the hood
```

## Some benchmarks

```
Hitmeister\Component\Metrics\Benchmarks\Collector\InfluxUdpImmediateEvent
    Method Name                       Iterations    Average Time      Ops/second
    -------------------------------  ------------  --------------    -------------
    counterName                    : [1,000     ] [0.0002313616276] [4,322.23792]
    counterNameMultiValue          : [1,000     ] [0.0000676133633] [14,789.97570]
    counterPrefixName              : [1,000     ] [0.0000578238964] [17,293.88820]
    counterPrefixNameMultiValue    : [1,000     ] [0.0000662095547] [15,103.56028]
    counterTagsName                : [1,000     ] [0.0000726783276] [13,759.25993]
    counterTagsNameMultiValue      : [1,000     ] [0.0000773749352] [12,924.08191]
    counterTagsNamePrefix          : [1,000     ] [0.0000779783726] [12,824.06861]
    counterTagsNamePrefixMultiValue: [1,000     ] [0.0000772831440] [12,939.43217]

Hitmeister\Component\Metrics\Benchmarks\Collector\InfluxUdpShutdownEvent
    Method Name                       Iterations    Average Time      Ops/second
    -------------------------------  ------------  --------------    -------------
    counterName                    : [1,000     ] [0.0000207498074] [48,193.21851]
    counterNameMultiValue          : [1,000     ] [0.0000219464302] [45,565.49701]
    counterPrefixName              : [1,000     ] [0.0000201663971] [49,587.43970]
    counterPrefixNameMultiValue    : [1,000     ] [0.0000193657875] [51,637.45599]
    counterTagsName                : [1,000     ] [0.0000242691040] [41,204.65262]
    counterTagsNameMultiValue      : [1,000     ] [0.0000208075047] [48,059.58315]
    counterTagsNamePrefix          : [1,000     ] [0.0000179619789] [55,673.15299]
    counterTagsNamePrefixMultiValue: [1,000     ] [0.0000188963413] [52,920.29726]

Hitmeister\Component\Metrics\Benchmarks\Collector\NoHandlerEvent
    Method Name                       Iterations    Average Time      Ops/second
    -------------------------------  ------------  --------------    -------------
    counterName                    : [1,000     ] [0.0000142347813] [70,250.46479]
    counterNameMultiValue          : [1,000     ] [0.0000161037445] [62,097.35876]
    counterPrefixName              : [1,000     ] [0.0000175075531] [57,118.20460]
    counterPrefixNameMultiValue    : [1,000     ] [0.0000142931938] [69,963.36947]
    counterTagsName                : [1,000     ] [0.0000159406662] [62,732.63536]
    counterTagsNameMultiValue      : [1,000     ] [0.0000165419579] [60,452.33634]
    counterTagsNamePrefix          : [1,000     ] [0.0000143866539] [69,508.86613]
    counterTagsNamePrefixMultiValue: [1,000     ] [0.0000144739151] [69,089.80694]

Hitmeister\Component\Metrics\Benchmarks\Collector\StatsDaemonImmediateEvent
    Method Name                       Iterations    Average Time      Ops/second
    -------------------------------  ------------  --------------    -------------
    counterName                    : [1,000     ] [0.0002136104107] [4,681.41977]
    counterNameMultiValue          : [1,000     ] [0.0000218720436] [45,720.46480]
    counterPrefixName              : [1,000     ] [0.0000505478382] [19,783.23971]
    counterPrefixNameMultiValue    : [1,000     ] [0.0000250971317] [39,845.19071]
    counterTagsName                : [1,000     ] [0.0000598993301] [16,694.67751]
    counterTagsNameMultiValue      : [1,000     ] [0.0000255017281] [39,213.02893]
    counterTagsNamePrefix          : [1,000     ] [0.0000610182285] [16,388.54526]
    counterTagsNamePrefixMultiValue: [1,000     ] [0.0000282423496] [35,407.81887]

Hitmeister\Component\Metrics\Benchmarks\Collector\StatsDaemonShutdownEvent
    Method Name                       Iterations    Average Time      Ops/second
    -------------------------------  ------------  --------------    -------------
    counterName                    : [1,000     ] [0.0000177154541] [56,447.88975]
    counterNameMultiValue          : [1,000     ] [0.0000172858238] [57,850.87308]
    counterPrefixName              : [1,000     ] [0.0000182156563] [54,897.82990]
    counterPrefixNameMultiValue    : [1,000     ] [0.0000215911865] [46,315.19435]
    counterTagsName                : [1,000     ] [0.0000220284462] [45,395.84822]
    counterTagsNameMultiValue      : [1,000     ] [0.0000185434818] [53,927.30499]
    counterTagsNamePrefix          : [1,000     ] [0.0000192592144] [51,923.19786]
    counterTagsNamePrefixMultiValue: [1,000     ] [0.0000217378139] [46,002.78585]

Hitmeister\Component\Metrics\Benchmarks\Formatter\InfluxDbLineEvent
    Method Name                Iterations    Average Time      Ops/second
    ------------------------  ------------  --------------    -------------
    counterName             : [10,000    ] [0.0000206048250] [48,532.32187]
    counterNameAndTags      : [1,000     ] [0.0000351431370] [28,455.05797]
    counterNameTagsAndSample: [1,000     ] [0.0000465621948] [21,476.65083]

Hitmeister\Component\Metrics\Benchmarks\Formatter\StatsDaemonEvent
    Method Name                Iterations    Average Time      Ops/second
    ------------------------  ------------  --------------    -------------
    counterName             : [10,000    ] [0.0000089899778] [111,234.97946]
    counterNameAndTags      : [1,000     ] [0.0000233101845] [42,899.70339]
    counterNameTagsAndSample: [1,000     ] [0.0000242178440] [41,291.86725]
```
