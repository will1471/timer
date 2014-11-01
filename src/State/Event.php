<?php

namespace Will1471\Timer\State;


/**
 * Stores the details about a Timers change in state.
 */
class Event
{

    /**
     * @var string
     */
    private $type;

    /**
     * @var \DateTimeInterface
     */
    private $datetime;


    /**
     * Class Constructor.
     *
     * @param string $type
     * @param \DateTimeInterface $datetime
     */
    public function __construct($type, \DateTimeInterface $datetime)
    {
        $this->type = $type;
        $this->datetime = $datetime;
    }


    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * @return \DateTimeInterface
     */
    public function getDateTime()
    {
        return $this->datetime;
    }

}

