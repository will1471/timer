<?php

namespace Will1471\Timer\Test\State;

class MachineTest extends \PHPUnit_Framework_TestCase
{

    private $machine;

    public function setUp()
    {
        $this->machine = new \Will1471\Timer\State\Machine();
    }

    private function invokePrivateMethod($object, $name, array $args = array())
    {
        $reflectionClass = new \ReflectionClass($object);
        $reflectionMethod = $reflectionClass->getMethod($name);
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invokeArgs($object, $args);
    }

    public function testIsHistoryValidEmpty()
    {
        $history = new \SplDoublyLinkedList();
        $this->assertTrue($this->invokePrivateMethod($this->machine, 'isHistoryValid', array($history)));
    }

    public function testValidHistory()
    {
        $history = new \SplDoublyLinkedList();
        $history->push(new \Will1471\Timer\State\Event('start', new \DateTime('2014-01-01 00:00:00')));
        $history->push(new \Will1471\Timer\State\Event('stop', new \DateTime('2014-01-01 00:01:00')));
        $this->assertTrue($this->invokePrivateMethod($this->machine, 'isHistoryValid', array($history)));
    }

    public function testInvalidHistory()
    {
        // Invalid becuse it the first Event is not start.
        $history = new \SplDoublyLinkedList();
        $history->push(new \Will1471\Timer\State\Event('stop', new \DateTime()));
        $this->assertFalse($this->invokePrivateMethod($this->machine, 'isHistoryValid', array($history)));
    }

    public function testInvalidHistoryOrder()
    {
        // Invalid becuse it the first Event is not start.
        $history = new \SplDoublyLinkedList();
        $history->push(new \Will1471\Timer\State\Event('start', new \DateTime('2014-01-01 00:01:00')));
        $history->push(new \Will1471\Timer\State\Event('stop', new \DateTime('2014-01-01 00:00:00')));
        $this->assertFalse($this->invokePrivateMethod($this->machine, 'isHistoryValid', array($history)));
    }

}