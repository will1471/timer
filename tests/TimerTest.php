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
}