# Metrics  [![Build Status](https://travis-ci.org/hitmeister/metrics.svg?branch=master)](https://travis-ci.org/hitmeister/metrics)

[![Latest Stable Version](http://img.shields.io/github/release/hitmeister/metrics.svg)](https://packagist.org/packages/hitmeister/metrics)
[![Coverage Status](http://img.shields.io/coveralls/hitmeister/metrics.svg)](https://coveralls.io/r/hitmeister/metrics?branch=master)
[![Total Downloads](http://img.shields.io/packagist/dt/hitmeister/metrics.svg)](https://packagist.org/packages/hitmeister/metrics)

Metrics forwarder for PHP.

## Some benchmarks

```
Hitmeister\Component\Metrics\Benchmarks\MetricToStatsDaemon\NormalFlush1Event
    Method Name             Iterations    Average Time      Ops/second
    ---------------------  ------------  --------------    -------------
    nameCounter          : [1,000     ] [0.0003904850483] [2,560.91752]
    nameSampleRateCounter: [1,000     ] [0.0000395340919] [25,294.62423]
    nameOneTagCounter    : [1,000     ] [0.0000472486019] [21,164.64741]
    nameFiveTagsCounter  : [1,000     ] [0.0000648760796] [15,414.00169]

Hitmeister\Component\Metrics\Benchmarks\MetricToStatsDaemon\NormalFlush2Event
    Method Name                         Iterations    Average Time      Ops/second
    ---------------------------------  ------------  --------------    -------------
    nameCounterWithGlobTags          : [1,000     ] [0.0000440697670] [22,691.29310]
    nameSampleRateCounterWithGlobTags: [1,000     ] [0.0000466732979] [21,425.52692]
    nameOneTagCounterWithGlobTags    : [1,000     ] [0.0000498499870] [20,060.18576]
    nameFiveTagsCounterWithGlobTags  : [1,000     ] [0.0000682659149] [14,648.59881]

Hitmeister\Component\Metrics\Benchmarks\MetricToStatsDaemon\ShutdownFlush1Event
    Method Name             Iterations    Average Time      Ops/second
    ---------------------  ------------  --------------    -------------
    nameCounter          : [1,000     ] [0.0000145456791] [68,748.93868]
    nameSampleRateCounter: [1,000     ] [0.0000164461136] [60,804.63903]
    nameOneTagCounter    : [1,000     ] [0.0000182757378] [54,717.35330]
    nameFiveTagsCounter  : [1,000     ] [0.0000273416042] [36,574.29869]

Hitmeister\Component\Metrics\Benchmarks\MetricToStatsDaemon\ShutdownFlush2Event
    Method Name                         Iterations    Average Time      Ops/second
    ---------------------------------  ------------  --------------    -------------
    nameCounterWithGlobTags          : [1,000     ] [0.0000172913074] [57,832.52671]
    nameSampleRateCounterWithGlobTags: [1,000     ] [0.0000264573097] [37,796.73786]
    nameOneTagCounterWithGlobTags    : [1,000     ] [0.0000205750465] [48,602.56321]
    nameFiveTagsCounterWithGlobTags  : [1,000     ] [0.0000292968750] [34,133.33333]
```
