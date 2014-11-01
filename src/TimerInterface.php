<?php

namespace Will1471\Timer;


interface TimerInterface
{

    /**
     * @return void
     */
    public function start();

    /**
     * @return void
     */
    public function stop();

    /**
     * @return void
     */
    public function pause();

    /**
     * @return void
     */
    public function resume();

    /**
     * @return int Seconds.
     */
    public function getElapsedSeconds();

}
