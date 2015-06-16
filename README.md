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
    counterName                    : [1,000     ] [0.0000184316635] [54,254.46267]
    counterNameMultiValue          : [1,000     ] [0.0000161714554] [61,837.35331]
    counterPrefixName              : [1,000     ] [0.0000188796520] [52,967.07793]
    counterPrefixNameMultiValue    : [1,000     ] [0.0000159733295] [62,604.35542]
    counterTagsName                : [1,000     ] [0.0000166232586] [60,156.67714]
    counterTagsNameMultiValue      : [1,000     ] [0.0000156123638] [64,051.79970]
    counterTagsNamePrefix          : [1,000     ] [0.0000152444839] [65,597.49765]
    counterTagsNamePrefixMultiValue: [1,000     ] [0.0000145139694] [68,899.13923]

Hitmeister\Component\Metrics\Benchmarks\Formatter\StatsDaemonEvent
    Method Name                Iterations    Average Time      Ops/second
    ------------------------  ------------  --------------    -------------
    counterName             : [10,000    ] [0.0000078526735] [127,345.16418]
    counterNameAndTags      : [1,000     ] [0.0000262596607] [38,081.22316]
    counterNameTagsAndSample: [1,000     ] [0.0000238325596] [41,959.40417]
```
