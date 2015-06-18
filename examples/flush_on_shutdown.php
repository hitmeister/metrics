<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/18/15
 * Time: 9:30 PM
 */

$loader = require(__DIR__ .'/../vendor/autoload.php');

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
// It uses `register_shutdown_function` function under the hood
