<?php

namespace Will1471\Timer\State;

use Finite\State\State as S;
use Finite\StateMachine\StateMachine;


/**
 * This class is responsible for the rules that the timer will follow.
 */
class Machine
{

    /**
     * @var \Finite\StateMachine\StateMachine
     */
    private $sm;

    /**
     * @var State
     */
    private $state;


    /**
     * Class Constructor.
     *
     * @param \Closure $onChangeCallback Callback which is invoked after successful state change.
     */
    public function __construct(\Closure $onChangeCallback = null)
    {
        $this->state = new State();
        $this->sm = $this->constructStateMachine();
        if ($onChangeCallback) {
            $this->sm->getDispatcher()->addListener(\Finite\Event\FiniteEvents::POST_TRANSITION, $onChangeCallback);
        }
    }


    /**
     * @param \SplDoublyLinkedList $history
     *
     * @return void
     *
     * @throws \DomainException
     */
    public function initialize(\SplDoublyLinkedList $history)
    {
        if (! $this->isHistoryValid($history)) {
            throw new \DomainException('History is not valid.');
        }

        if ($history->count() > 0) {
            $this->state->setFiniteState($this->sm->getTransition($history->top()->getType())->getState());
        }

        $this->sm->setObject($this->state);
        $this->sm->initialize();
    }


   /**
     * @param string $transitionName
     *
     * @return void
     *
     * @throws \Finite\Exception\StateException on invalid transition.
     */
    public function applyTransition($transitionName)
    {
        $this->sm->apply($transitionName);
    }


    /**
     * Constructs a StateMachine with the rules for the Timer.
     *
     * @return \Finite\StateMachine\StateMachine
     */
    private function constructStateMachine()
    {
        $sm = new StateMachine();

        $sm->addState(new S('default', S::TYPE_INITIAL));
        $sm->addState('running');
        $sm->addState('paused');
        $sm->addState(new S('stopped', S::TYPE_FINAL));

        $sm->addTransition('start', 'default', 'running');
        $sm->addTransition('pause', 'running', 'paused');
        $sm->addTransition('resume', 'paused', 'running');
        $sm->addTransition('stop', 'running', 'stopped');

        return $sm;
    }


    /**
     * Steps thought the history and validates it by running it thought the StateMachine.
     *
     * @param \SplDoublyLinkedList $history
     *
     * @return boolean
     */
    private function isHistoryValid(\SplDoublyLinkedList $history)
    {
        if ($history->count() == 0) {
            return true;
        }

        $sm = $this->constructStateMachine();
        $sm->setObject(new State());
        $sm->initialize();

        $previous = null;
        foreach ($history as $event) {
            /* @var $event Event */

            if (isset($previous) && $event->getDateTime() < $previous->getDateTime()) {
                return false;
            }

            try {
                $sm->apply($event->getType());
            } catch (\Exception $e) {
                return false;
            }

            $previous = $event;
        }

        return true;
    }

}

