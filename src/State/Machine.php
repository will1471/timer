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
     * Class Constructor.
     */
    public function __construct(\Finite\StatefulInterface $object, \Closure $onStartChangeCallback)
    {
        $this->sm = new StateMachine();

        $this->sm->addState(new S('default', S::TYPE_INITIAL));
        $this->sm->addState('running');
        $this->sm->addState('paused');
        $this->sm->addState(new S('stopped', S::TYPE_FINAL));

        $this->sm->addTransition('start', 'default', 'running');
        $this->sm->addTransition('pause', 'running', 'paused');
        $this->sm->addTransition('resume', 'paused', 'running');
        $this->sm->addTransition('stop', 'running', 'stopped');

        $this->sm->getDispatcher()->addListener(\Finite\Event\FiniteEvents::POST_TRANSITION, $onStartChangeCallback);

        $this->sm->setObject($object);
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

}

