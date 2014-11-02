<?php

namespace Will1471\Timer;


class Timer implements \Will1471\Timer\TimerInterface
{

    /**
     * List of Timers changes in State, the StateMachine should keep this in
     * order, but the Event objects know their type of change so we could
     * add defensive checks.
     *
     * @var \SplDoublyLinkedList
     */
    private $events;

    /**
     * @var State\Machine
     */
    private $stateMachine;


    /**
     * Class Construtor.
     */
    public function __construct(array $events = array())
    {
        $this->events = new \SplDoublyLinkedList();

        foreach ($events as $event) {
            if (! $event instanceof State\Event) {
                throw new \InvalidArgumentException('Expected an array of ' . State\Event::class .' as first argument.');
            }
            $this->events->push($event);
        }

        $this->stateMachine = new State\Machine(
            /*
             * Callback invoked after a successful state change, the StateMachine
             * should not care about saving events, so this behaviour is injected.
             */
            function(\Finite\Event\TransitionEvent $event) {
                $this->pushEvent(
                    new State\Event(
                        $event->getTransition()->getName(),
                        new \DateTimeImmutable('now', new \DateTimeZone('UTC'))
                    )
                );
            }
        );

        $this->stateMachine->initialize($this->events);
    }

    /**
     * @param \Will1471\Timer\State\Event $event
     */
    protected function pushEvent(State\Event $event)
    {
        $this->events[] = $event;
    }


    /**
     * Returns the number of seconds a timer has been running for.
     *
     * @return int
     */
    public function getElapsedSeconds()
    {
        $previousEvent = null;
        $elapsed = 0;

        /*
         * By default SplDoublyLinkedList iterator mode is FIFO | KEEP.
         *
         * So we should get the events back in the order they where added, and
         * they should remain after iterating over them.
         */
        foreach ($this->events as $event) {

            /* @var $event State\Event */

            if (! isset($previousEvent)) {
                $previousEvent = $event;
                continue;
            }

            $elapsed += $event->getDateTime()->getTimestamp() - $previousEvent->getDateTime()->getTimestamp();
            unset($previousEvent);
        }

        if (isset($previousEvent)) {
            $now = new \DateTime('now', new \DateTimeZone('UTC'));
            $elapsed += $now->getTimestamp() - $previousEvent->getDateTime()->getTimestamp();
        }

        return $elapsed;
    }


    /**
     * @return void
     */
    public function pause()
    {
        $this->stateMachine->applyTransition('pause');
    }


    /**
     * @return void
     */
    public function resume()
    {
        $this->stateMachine->applyTransition('resume');
    }


    /**
     * @return void
     */
    public function start()
    {
        $this->stateMachine->applyTransition('start');
    }


    /**
     * @return void
     */
    public function stop()
    {
        $this->stateMachine->applyTransition('stop');
    }

}

