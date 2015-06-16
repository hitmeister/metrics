<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/16/15
 * Time: 11:10 PM
 */

namespace Hitmeister\Component\Metrics\Tests\Formatter;

use Hitmeister\Component\Metrics\Formatter\StatsDaemonFormatter;
use Hitmeister\Component\Metrics\Metric\CounterMetric;
use Hitmeister\Component\Metrics\Metric\DummyMetric;
use Hitmeister\Component\Metrics\Metric\GaugeMetric;
use Hitmeister\Component\Metrics\Metric\MemoryMetric;
use Hitmeister\Component\Metrics\Metric\TimerMetric;
use Hitmeister\Component\Metrics\Metric\UniqueMetric;

class StatsDaemonFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StatsDaemonFormatter
     */
    private $formatter;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->formatter = new StatsDaemonFormatter();
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        $this->formatter = null;

        parent::tearDown();
    }

    /**
     * Tests counter formatter
     */
    public function testCounterMetric()
    {
        $metric1 = new CounterMetric('metric_name1', 10);
        $expected1 = 'metric_name1:10|c';
        $this->assertEquals($expected1, $this->formatter->format($metric1));

        $metric2 = new CounterMetric('metric_name2', 40, ['env' => 'dev']);
        $expected2 = 'env.dev.metric_name2:40|c';
        $this->assertEquals($expected2, $this->formatter->format($metric2));

        $metric3 = new CounterMetric('metric_name3', 40, ['env' => 'dev']);
        $metric3->setSampleRate(0.5);
        $expected3 = 'env.dev.metric_name3:40|c|@0.5';
        $this->assertEquals($expected3, $this->formatter->format($metric3));

        $metric4 = new CounterMetric('metric_name4', ['internal' => 10]);
        $this->assertFalse($this->formatter->format($metric4));
    }

    /**
     * Tests memory formatter
     */
    public function testMemoryMetric()
    {
        $metric1 = new MemoryMetric('metric_name1', 10);
        $expected1 = 'metric_name1:10|c';
        $this->assertEquals($expected1, $this->formatter->format($metric1));

        $metric2 = new MemoryMetric('metric_name2', 40, ['env' => 'dev']);
        $expected2 = 'env.dev.metric_name2:40|c';
        $this->assertEquals($expected2, $this->formatter->format($metric2));

        $metric3 = new MemoryMetric('metric_name3', 40, ['env' => 'dev']);
        $metric3->setSampleRate(0.5);
        $expected3 = 'env.dev.metric_name3:40|c|@0.5';
        $this->assertEquals($expected3, $this->formatter->format($metric3));

        $metric4 = new MemoryMetric('metric_name4', ['internal' => 10]);
        $this->assertFalse($this->formatter->format($metric4));
    }

    /**
     * Tests memory formatter
     */
    public function testTimerMetric()
    {
        $metric1 = new TimerMetric('metric_name1', 10);
        $expected1 = 'metric_name1:10|ms';
        $this->assertEquals($expected1, $this->formatter->format($metric1));

        $metric2 = new TimerMetric('metric_name2', 40, ['env' => 'dev']);
        $expected2 = 'env.dev.metric_name2:40|ms';
        $this->assertEquals($expected2, $this->formatter->format($metric2));

        $metric3 = new TimerMetric('metric_name3', 40, ['env' => 'dev']);
        $metric3->setSampleRate(0.5);
        $expected3 = 'env.dev.metric_name3:40|ms|@0.5';
        $this->assertEquals($expected3, $this->formatter->format($metric3));

        $metric4 = new TimerMetric('metric_name4', ['internal' => 10]);
        $this->assertFalse($this->formatter->format($metric4));
    }

    /**
     * Tests gauge formatter
     */
    public function testGaugeMetric()
    {
        $metric1 = new GaugeMetric('metric_name1', 10);
        $expected1 = 'metric_name1:10|g';
        $this->assertEquals($expected1, $this->formatter->format($metric1));

        $metric2 = new GaugeMetric('metric_name2', 40, ['env' => 'dev']);
        $expected2 = 'env.dev.metric_name2:40|g';
        $this->assertEquals($expected2, $this->formatter->format($metric2));

        $metric3 = new GaugeMetric('metric_name3', ['internal' => 10]);
        $this->assertFalse($this->formatter->format($metric3));

        $metric4 = new GaugeMetric('metric_name4', '20');
        $expected4 = 'metric_name4:20|g';
        $this->assertEquals($expected4, $this->formatter->format($metric4));

        $metric5 = new GaugeMetric('metric_name4', '+20');
        $expected5 = 'metric_name4:+20|g';
        $this->assertEquals($expected5, $this->formatter->format($metric5));

        $metric6 = new GaugeMetric('metric_name4', '-20');
        $expected6 = 'metric_name4:-20|g';
        $this->assertEquals($expected6, $this->formatter->format($metric6));

        $metric7 = new GaugeMetric('metric_name4', -20);
        $expected7 = 'metric_name4:-20|g';
        $this->assertEquals($expected7, $this->formatter->format($metric7));
    }

    /**
     * Tests unique/set formatter
     */
    public function testUniqueMetric()
    {
        $metric1 = new UniqueMetric('metric_name1', 10);
        $expected1 = 'metric_name1:10|s';
        $this->assertEquals($expected1, $this->formatter->format($metric1));

        $metric2 = new UniqueMetric('metric_name2', 40, ['env' => 'dev']);
        $expected2 = 'env.dev.metric_name2:40|s';
        $this->assertEquals($expected2, $this->formatter->format($metric2));

        $metric3 = new UniqueMetric('metric_name3', ['internal' => 10]);
        $this->assertFalse($this->formatter->format($metric3));
    }

    /**
     * Tests unsupported formatter
     */
    public function testUnsupportedMetric()
    {
        $metric1 = new DummyMetric('metric_name1', 10);
        $this->assertFalse($this->formatter->format($metric1));
    }

    /**
     * Tests batch formatting
     */
    public function testBatchFormatter()
    {
        $metrics = [
            new CounterMetric('counter_metric', 10),
            new MemoryMetric('memory_metric', 20),
            new TimerMetric('timer_metric', 30),
            new GaugeMetric('gauge_metric', 40),
            new UniqueMetric('unique_metric', 50),
            new UniqueMetric('false_metric', ['internal' => 10]) // false - should not be in the result
        ];

        $expected = [
            'counter_metric:10|c',
            'memory_metric:20|c',
            'timer_metric:30|ms',
            'gauge_metric:40|g',
            'unique_metric:50|s',
        ];

        $this->assertEquals($expected, $this->formatter->formatBatch($metrics));
    }
}