<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/14/15
 * Time: 3:24 PM
 */

namespace Hitmeister\Component\Metrics\Tests;

use Hitmeister\Component\Metrics\Collector;
use Hitmeister\Component\Metrics\Handler\DummyHandler;
use Hitmeister\Component\Metrics\Metric;

class CollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Collector
     */
    protected function getCollector()
    {
        $handler = new TestHandler();
        return [$handler, new Collector($handler)];
    }

    /**
     *
     */
    public function testSetGet()
    {
        $collector = new Collector(new DummyHandler());
        $this->assertInstanceOf('\Hitmeister\Component\Metrics\Handler\DummyHandler', $collector->getHandler());

        $collector->setGlobalTags(['tag1' => 'value1']);
        $this->assertEquals(['tag1' => 'value1'], $collector->getGlobalTags());
        $this->assertTrue($collector->hasGlobalTags());

        $collector->setGlobalTags([]);
        $collector->addGlobalTag('tag2', 'value2');
        $this->assertEquals(['tag2' => 'value2'], $collector->getGlobalTags());

        $collector->removeGlobalTag('tag2');
        $this->assertEquals([], $collector->getGlobalTags());

        $collector->setWriteSilent(false);
        $this->assertFalse($collector->isWriteSilent());

        $collector->setFlushOnShutdown(false);
        $this->assertFalse($collector->isFlushOnShutdown());
    }

    /**
     * @return array
     */
    public function multiSetExpiry()
    {
        return [['count', 10], ['increment', 20], ['decrement', 30]];
    }

    /**
     * Count, Increment, Decrement
     * @dataProvider multiSetExpiry
     * @param string $type
     * @param int $value
     */
    public function testCountable($type, $value)
    {
        /** @var TestHandler $handler */
        /** @var Collector $collector */
        list($handler, $collector) = $this->getCollector();
        $collector->$type('countable_metric', $value);
        $this->assertCount(1, $handler->metrics);

        $expectedValue = ('decrement' == $type) ? -$value : $value;

        /** @var Metric $lastMetric */
        $lastMetric = array_shift($handler->metrics);
        $this->assertEquals(Metric::TYPE_COUNT, $lastMetric->getType());
        $this->assertEquals('countable_metric', $lastMetric->getName());
        $this->assertEquals($expectedValue, $lastMetric->getValue());
    }

    public function testTime()
    {
        /** @var TestHandler $handler */
        /** @var Collector $collector */
        list($handler, $collector) = $this->getCollector();
        $collector->time('time_metric', 32.6);
        $this->assertCount(1, $handler->metrics);

        /** @var Metric $lastMetric */
        $lastMetric = array_shift($handler->metrics);
        $this->assertEquals(Metric::TYPE_TIME, $lastMetric->getType());
        $this->assertEquals('time_metric', $lastMetric->getName());
        $this->assertEquals(32.6, $lastMetric->getValue());
    }

    public function testTimer()
    {
        /** @var TestHandler $handler */
        /** @var Collector $collector */
        list($handler, $collector) = $this->getCollector();
        $collector->startTimer('timer1');
        usleep(1000);
        $stop = $collector->stopTimer('timer1');
        $this->assertGreaterThanOrEqual(1, $stop);

        $collector->startTimer('timer2');
        usleep(1000);
        $collector->reportTimer('timer2');
        $this->assertCount(1, $handler->metrics);

        /** @var Metric $lastMetric */
        $lastMetric = array_shift($handler->metrics);
        $this->assertEquals(Metric::TYPE_TIME, $lastMetric->getType());
        $this->assertEquals('timer2', $lastMetric->getName());
        $this->assertGreaterThanOrEqual(1, $lastMetric->getValue());
    }

    public function testGauge()
    {
        /** @var TestHandler $handler */
        /** @var Collector $collector */
        list($handler, $collector) = $this->getCollector();
        $collector->gauge('gauge_metric', 400);
        $this->assertCount(1, $handler->metrics);

        /** @var Metric $lastMetric */
        $lastMetric = array_shift($handler->metrics);
        $this->assertEquals(Metric::TYPE_GAUGE, $lastMetric->getType());
        $this->assertEquals('gauge_metric', $lastMetric->getName());
        $this->assertEquals(400, $lastMetric->getValue());
    }

    public function testSet()
    {
        /** @var TestHandler $handler */
        /** @var Collector $collector */
        list($handler, $collector) = $this->getCollector();
        $collector->set('set_metric', 1234);
        $this->assertCount(1, $handler->metrics);

        /** @var Metric $lastMetric */
        $lastMetric = array_shift($handler->metrics);
        $this->assertEquals(Metric::TYPE_SET, $lastMetric->getType());
        $this->assertEquals('set_metric', $lastMetric->getName());
        $this->assertEquals(1234, $lastMetric->getValue());
    }

    public function testMemory()
    {
        /** @var TestHandler $handler */
        /** @var Collector $collector */
        list($handler, $collector) = $this->getCollector();
        $collector->startMemory('memory1');
        $array1 = array_fill(0, 100000, 1);
        $stop = $collector->stopMemory('memory1');
        $this->assertGreaterThanOrEqual(1, $stop);

        $collector->startMemory('memory2');
        $array2 = array_fill(0, 100000, 2);
        $collector->reportMemory('memory2');
        $this->assertCount(1, $handler->metrics);

        /** @var Metric $lastMetric */
        $lastMetric = array_shift($handler->metrics);
        $this->assertEquals(Metric::TYPE_COUNT, $lastMetric->getType());
        $this->assertEquals('memory2', $lastMetric->getName());
        $this->assertGreaterThanOrEqual(1, $lastMetric->getValue());

        unset($array1,$array2);
    }

    public function testGlobalAndLocalTags()
    {
        /** @var TestHandler $handler */
        /** @var Collector $collector */
        list($handler, $collector) = $this->getCollector();
        $collector->setGlobalTags(['global' => 'tag']);
        $collector->count('tags_metric', 1, ['local' => 'tag']);
        $this->assertCount(1, $handler->metrics);

        /** @var Metric $lastMetric */
        $lastMetric = array_shift($handler->metrics);
        $tags = $lastMetric->getTags();
        $this->assertArrayHasKey('global', $tags);
        $this->assertArrayHasKey('local', $tags);
    }

    public function testHandleBatchOnShutdown()
    {
        /** @var TestHandler $handler */
        /** @var Collector $collector */
        list($handler, $collector) = $this->getCollector();
        $collector->setFlushOnShutdown(true);
        $this->assertTrue($collector->isFlushOnShutdown());

        $collector->increment('one')->increment('two')->flush();
        $this->assertCount(2, $handler->metrics);
        $this->assertEquals('one', $handler->metrics[0]->getName());
        $this->assertEquals('two', $handler->metrics[1]->getName());
    }
}