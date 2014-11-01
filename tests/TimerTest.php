<?php

namespace Will1471\Timer\Test;

use Finite\Exception\StateException;
use Will1471\Timer\Timer;


class TimerTest extends \PHPUnit_Framework_TestCase
{

    private $timer;

    public function setUp()
    {
        $this->timer = new Timer();
    }

    protected function getPrivateProperty($object, $name)
    {
        $reflectionClass = new \ReflectionClass($object);
        $reflectionProperty = $reflectionClass->getProperty($name);
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty->getValue($object);
    }

    protected function setPrivateProperty($object, $name, $value)
    {
        $reflectionClass = new \ReflectionClass($object);
        $reflectionProperty = $reflectionClass->getProperty($name);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }

    public function testNewTimerCanStart()
    {
        $this->timer->start();
    }

    public function testNewTimerCanNotStop()
    {
        $this->setExpectedException(StateException::class, 'The "stop" transition can not be applied to the "default" state.');
        $this->timer->stop();
    }

    public function testNewTimerCanNotPause()
    {
        $this->setExpectedException(StateException::class, 'The "pause" transition can not be applied to the "default" state.');
        $this->timer->pause();
    }

    public function testNewTimerCanNotResume()
    {
        $this->setExpectedException(StateException::class, 'The "resume" transition can not be applied to the "default" state.');
        $this->timer->resume();
    }

    public function testTimerCannotBeStartedTwice()
    {
        $this->timer->start();
        $this->setExpectedException(StateException::class);
        $this->timer->start();
    }

    public function testStartedTimerCanStop()
    {
        $this->timer->start();
        $this->timer->stop();
    }

    public function testStartedTimerCanPause()
    {
        $this->timer->start();
        $this->timer->pause();
    }

    public function testPausedTimerCanNotPause()
    {
        $this->timer->start();
        $this->timer->pause();
        $this->setExpectedException(StateException::class);
        $this->timer->pause();
    }

    public function testPausedTimerCanResume()
    {
        $this->timer->start();
        $this->timer->pause();
        $this->timer->resume();
    }

    public function testStartAddsEvent()
    {
        $this->assertEquals(0, $this->getPrivateProperty($this->timer, 'events')->count());
        $this->timer->start();
        $this->assertEquals(1, $this->getPrivateProperty($this->timer, 'events')->count());

        $this->assertEquals('start', $this->getPrivateProperty($this->timer, 'events')[0]->getType());
    }

    public function testStopAddsEvent()
    {
        $this->assertEquals(0, $this->getPrivateProperty($this->timer, 'events')->count());
        $this->timer->start();
        $this->timer->stop();
        $this->assertEquals(2, $this->getPrivateProperty($this->timer, 'events')->count());

        $this->assertEquals('start', $this->getPrivateProperty($this->timer, 'events')[0]->getType());
        $this->assertEquals('stop', $this->getPrivateProperty($this->timer, 'events')[1]->getType());
    }

    public function testPauseAddsEvent()
    {
        $this->assertEquals(0, $this->getPrivateProperty($this->timer, 'events')->count());
        $this->timer->start();
        $this->timer->pause();
        $this->assertEquals(2, $this->getPrivateProperty($this->timer, 'events')->count());

        $this->assertEquals('start', $this->getPrivateProperty($this->timer, 'events')[0]->getType());
        $this->assertEquals('pause', $this->getPrivateProperty($this->timer, 'events')[1]->getType());
    }

    public function testResumeAddsEvent()
    {
        $this->assertEquals(0, $this->getPrivateProperty($this->timer, 'events')->count());
        $this->timer->start();
        $this->timer->pause();
        $this->timer->resume();
        $this->assertEquals(3, $this->getPrivateProperty($this->timer, 'events')->count());

        $this->assertEquals('start', $this->getPrivateProperty($this->timer, 'events')[0]->getType());
        $this->assertEquals('pause', $this->getPrivateProperty($this->timer, 'events')[1]->getType());
        $this->assertEquals('resume', $this->getPrivateProperty($this->timer, 'events')[2]->getType());
    }

    public function testGetElapsedSeconds()
    {
        $list = new \SplDoublyLinkedList();
        $list->push(new \Will1471\Timer\State\Event('start', new \DateTime('2014-01-01 00:00:00')));
        $list->push(new \Will1471\Timer\State\Event('start', new \DateTime('2014-01-01 00:00:30')));

        $this->setPrivateProperty($this->timer, 'events', $list);

        $this->assertEquals(30, $this->timer->getElapsedSeconds());
    }

    public function testGetElapsedSecondsStillRunning()
    {
        $list = new \SplDoublyLinkedList();
        $list->push(new \Will1471\Timer\State\Event('start', new \DateTime('2014-01-01 00:00:00')));
        $list->push(new \Will1471\Timer\State\Event('start', new \DateTime('2014-01-01 00:00:30')));
        $list->push(new \Will1471\Timer\State\Event('start', new \DateTime('2014-01-01 00:01:30')));

        $this->setPrivateProperty($this->timer, 'events', $list);

        $this->assertGreaterThan(30, $this->timer->getElapsedSeconds());
    }

    public function testWithValidHistory()
    {
        $this->timer = new Timer(array(
            new \Will1471\Timer\State\Event('start', new \DateTime('2014-01-01 00:00:00')),
            new \Will1471\Timer\State\Event('pause', new \DateTime('2014-01-01 00:01:00')),
        ));
        $this->assertEquals(60, $this->timer->getElapsedSeconds());
    }

    public function testWithInvalidHistory()
    {
        $this->setExpectedException(\DomainException::class, 'History is not valid.');
        $this->timer = new Timer(array(
            new \Will1471\Timer\State\Event('stop', new \DateTime('2014-01-01 00:00:00')),
        ));
    }

    public function testWithBadArgument()
    {
        $this->setExpectedException(\InvalidArgumentException::class, 'Expected an array of Will1471\Timer\State\Event as first argument.');
        $this->timer = new Timer(array(new \DateTime()));
    }

}