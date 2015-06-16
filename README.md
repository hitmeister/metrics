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
    counterName                    : [1,000     ] [0.0000155351162] [64,370.29420]
    counterNameMultiValue          : [1,000     ] [0.0000202043056] [49,494.40072]
    counterPrefixName              : [1,000     ] [0.0000157337189] [63,557.76458]
    counterPrefixNameMultiValue    : [1,000     ] [0.0000157635212] [63,437.60304]
    counterTagsName                : [1,000     ] [0.0000155723095] [64,216.55056]
    counterTagsNameMultiValue      : [1,000     ] [0.0000154478550] [64,733.90644]
    counterTagsNamePrefix          : [1,000     ] [0.0000160977840] [62,120.35131]
    counterTagsNamePrefixMultiValue: [1,000     ] [0.0000147726536] [67,692.64537]

Hitmeister\Component\Metrics\Benchmarks\Collector\StatsDaemonImmediateEvent
    Method Name                       Iterations    Average Time      Ops/second
    -------------------------------  ------------  --------------    -------------
    counterName                    : [1,000     ] [0.0002012920380] [4,967.90638]
    counterNameMultiValue          : [1,000     ] [0.0000223789215] [44,684.90582]
    counterPrefixName              : [1,000     ] [0.0000449631214] [22,240.44881]
    counterPrefixNameMultiValue    : [1,000     ] [0.0000221140385] [45,220.14382]
    counterTagsName                : [1,000     ] [0.0000583240986] [17,145.57146]
    counterTagsNameMultiValue      : [1,000     ] [0.0000219795704] [45,496.79463]
    counterTagsNamePrefix          : [1,000     ] [0.0000590469837] [16,935.66609]
    counterTagsNamePrefixMultiValue: [1,000     ] [0.0000223853588] [44,672.05589]

Hitmeister\Component\Metrics\Benchmarks\Collector\StatsDaemonShutdownEvent
    Method Name                       Iterations    Average Time      Ops/second
    -------------------------------  ------------  --------------    -------------
    counterName                    : [1,000     ] [0.0000205719471] [48,609.88584]
    counterNameMultiValue          : [1,000     ] [0.0000212697983] [47,015.02040]
    counterPrefixName              : [1,000     ] [0.0000175352097] [57,028.11769]
    counterPrefixNameMultiValue    : [1,000     ] [0.0000177738667] [56,262.37776]
    counterTagsName                : [1,000     ] [0.0000202953815] [49,272.29369]
    counterTagsNameMultiValue      : [1,000     ] [0.0000178763866] [55,939.71645]
    counterTagsNamePrefix          : [1,000     ] [0.0000175111294] [57,106.53941]
    counterTagsNamePrefixMultiValue: [1,000     ] [0.0000174787045] [57,212.47835]

Hitmeister\Component\Metrics\Benchmarks\Formatter\StatsDaemonEvent
    Method Name                Iterations    Average Time      Ops/second
    ------------------------  ------------  --------------    -------------
    counterName             : [10,000    ] [0.0000084429264] [118,442.34473]
    counterNameAndTags      : [1,000     ] [0.0000208334923] [47,999.63379]
    counterNameTagsAndSample: [1,000     ] [0.0000221014023] [45,245.99784]
```
