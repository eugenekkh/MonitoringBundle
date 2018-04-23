<?php

namespace Tests;

use StudioSite\MonitoringBundle\Parameter\ParameterCollection;
use PHPUnit\Framework\TestCase;
use Tests\TestService;

class ParameterCollectionTest extends TestCase
{
    protected $collection;
    protected $service;

    public function setUp()
    {
        parent::setUp();

        $this->collection = new ParameterCollection();
        $this->service = new TestService();

        $this->collection->addParameter($this->service, 'getParameter', 'test.parameter1');
        $this->collection->addParameter($this->service, 'getParameterWithArguments', 'test.parameter2');

    }

    public function testCollection()
    {
        $this->assertEquals('value', $this->collection->getValue('test.parameter1'));
        $this->assertEquals('testvalue', $this->collection->getValue('test.parameter2', ['test', 'value']));
    }

    public function testArguments()
    {
        $this->assertEquals([], $this->collection->getArguments('test.parameter1'));
        $this->assertEquals(['arg1', 'arg2'], $this->collection->getArguments('test.parameter2'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNotExistsParameter()
    {
        $this->collection->getValue('test.parameter999');
    }
}
