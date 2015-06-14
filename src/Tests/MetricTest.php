<?php
/**
 * User: Maksim Naumov <fromyukki@gmail.com>
 * Date: 6/13/15
 * Time: 11:09 PM
 */

namespace Hitmeister\Component\Metrics\Tests;

use Hitmeister\Component\Metrics\Metric;

class MetricTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests basic creation of metric instance
     */
    public function testCreateDefault()
    {
        $metric = new Metric('metric_name');
        $this->assertEquals('metric_name', $metric->getName());
        $this->assertEquals(1, $metric->getValue());
        $this->assertEquals(Metric::TYPE_COUNT, $metric->getType());
        $this->assertEquals([], $metric->getTags());
        $this->assertEquals('', $metric->getTagsAsString());
    }

    /**
     * Tests all get-set methods
     */
    public function testSetGet()
    {
        $metric = new Metric('metric_name');
        $metric->setName('new_name');
        $this->assertEquals('new_name', $metric->getName());

        $metric->setValue(10);
        $this->assertEquals(10, $metric->getValue());

        $metric->setSampleRate(0.5);
        $this->assertEquals(0.5, $metric->getSampleRate());

        $metric->setType(Metric::TYPE_TIME);
        $this->assertEquals(Metric::TYPE_TIME, $metric->getType());

        $now = time();
        $metric->setTime($now, Metric::PRECISION_SECONDS);
        $this->assertEquals($now, $metric->getTime());
        $this->assertEquals(Metric::PRECISION_SECONDS, $metric->getPrecision());

        $metric->setTags(['tag1' => 'value1']);
        $this->assertEquals(['tag1' => 'value1'], $metric->getTags());
        $this->assertTrue($metric->hasTags());
        $metric->addTags(['tag2' => 'value2']);
        $this->assertEquals(['tag1' => 'value1', 'tag2' => 'value2'], $metric->getTags());

        $metric->setTags([]);
        $metric->addTag('tag2', 'value2');
        $this->assertEquals(['tag2' => 'value2'], $metric->getTags());

        $metric->removeTag('tag2');
        $this->assertEquals([], $metric->getTags());
    }

    public function testSampleRateLimits()
    {
        $metric = new Metric('metric_name');

        $metric->setSampleRate(10);
        $this->assertEquals(1, $metric->getSampleRate());

        $metric->setSampleRate(-10);
        $this->assertEquals(0, $metric->getSampleRate());
    }

    /**
     * Tests tags as string method
     */
    public function testTagsAsString()
    {
        $expected = 'tag1.value1.tag2.value2';
        $metric = new Metric('metric_name');
        $metric->setTags(['tag1' => 'value1', 'tag2' => 'value2']);
        $this->assertEquals($expected, $metric->getTagsAsString());
    }

    /**
     * Tests sanitize functionality
     */
    public function testSanitize()
    {
        $metric = new Metric('metric.with.dots');
        $this->assertEquals('metric_with_dots', $metric->getName());

        $metric->setTags(['tag1.with.dots' => 'value']);
        $this->assertEquals(['tag1_with_dots' => 'value'], $metric->getTags());

        $metric->setTags([]);
        $metric->addTag('tag2.with.dots', 'value.with.dots');
        $this->assertEquals('tag2_with_dots.value_with_dots', $metric->getTagsAsString());
    }
}