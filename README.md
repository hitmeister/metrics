# Metrics  [![Build Status](https://travis-ci.org/hitmeister/metrics.svg?branch=master)](https://travis-ci.org/hitmeister/metrics)

[![Latest Stable Version](http://img.shields.io/github/release/hitmeister/metrics.svg)](https://packagist.org/packages/hitmeister/metrics)
[![Coverage Status](http://img.shields.io/coveralls/hitmeister/metrics.svg)](https://coveralls.io/r/hitmeister/metrics?branch=master)
[![Total Downloads](http://img.shields.io/packagist/dt/hitmeister/metrics.svg)](https://packagist.org/packages/hitmeister/metrics)

Metrics forwarder for PHP.

## Some benchmarks

```
Hitmeister\Component\Metrics\Benchmarks\Collector\NoHandler\BaseEvent
    Method Name                 Iterations    Average Time      Ops/second
    -------------------------  ------------  --------------    -------------
    counterOnlyName          : [1,000     ] [0.0000134799480] [74,184.26219]
    counterOnlyNameMultiValue: [1,000     ] [0.0000136814117] [73,091.87230]

Hitmeister\Component\Metrics\Benchmarks\Collector\NoHandler\PrefixEvent
    Method Name                 Iterations    Average Time      Ops/second
    -------------------------  ------------  --------------    -------------
    counterOnlyName          : [1,000     ] [0.0000141932964] [70,455.79614]
    counterOnlyNameMultiValue: [1,000     ] [0.0000139153004] [71,863.34276]

Hitmeister\Component\Metrics\Benchmarks\Collector\NoHandler\TagsEvent
    Method Name                 Iterations    Average Time      Ops/second
    -------------------------  ------------  --------------    -------------
    counterOnlyName          : [1,000     ] [0.0000158238411] [63,195.78123]
    counterOnlyNameMultiValue: [1,000     ] [0.0000134763718] [74,203.94877]

Hitmeister\Component\Metrics\Benchmarks\Collector\NoHandler\TagsPrefixEvent
    Method Name                 Iterations    Average Time      Ops/second
    -------------------------  ------------  --------------    -------------
    counterOnlyName          : [1,000     ] [0.0000142233372] [70,306.98828]
    counterOnlyNameMultiValue: [1,000     ] [0.0000147016048] [68,019.78496]
```
