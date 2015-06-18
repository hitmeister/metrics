# Metrics  [![Build Status](https://travis-ci.org/hitmeister/metrics.svg?branch=master)](https://travis-ci.org/hitmeister/metrics)

[![Latest Stable Version](http://img.shields.io/github/release/hitmeister/metrics.svg)](https://packagist.org/packages/hitmeister/metrics)
[![Coverage Status](http://img.shields.io/coveralls/hitmeister/metrics.svg)](https://coveralls.io/r/hitmeister/metrics?branch=master)
[![Total Downloads](http://img.shields.io/packagist/dt/hitmeister/metrics.svg)](https://packagist.org/packages/hitmeister/metrics)

Metrics forwarder for PHP.

## Some benchmarks

```
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
