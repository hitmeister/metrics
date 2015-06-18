<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/18/15
 * Time: 9:30 PM
 */

$loader = require(__DIR__ .'/../vendor/autoload.php');

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
