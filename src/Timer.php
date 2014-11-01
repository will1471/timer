<?php

namespace Will1471\Timer;


class Timer implements \Will1471\Timer\TimerInterface
{

    /**
     * @var State\State
     */
    private $state;

    /**
     * @var State\Machine
     */
    private $stateMachine;


    /**
     * Class Construtor.
     */
    public function __construct()
    {
        $this->state = new State\State();
        $this->stateMachine = new State\Machine($this->state);
    }


    public function getElapsedSeconds()
    {
    }


    public function pause()
    {
        $this->stateMachine->applyTransition('pause');
    }


    public function resume()
    {
        $this->stateMachine->applyTransition('resume');
    }


    public function start()
    {
        $this->stateMachine->applyTransition('start');
    }


    public function stop()
    {
        $this->stateMachine->applyTransition('stop');
    }

}

