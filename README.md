Timer
=====

A timer that keeps track of its events, using a [Finite State Machine] to validate the timers internal state, and the stored events.

Usage
-----

From its initial state, a timer can only be started, any other actions will result in an exception being thrown.

```php
<?php

use \Will1471\Timer\Timer;

$timer = new Timer();
$timer->start();
```

Running timers can be stopped, or paused and later resumed.

```php
sleep(1);
$timer->pause();
sleep(1);
$timer->resume();
sleep(1);
$timer->stop();
```

A timer that has been stopped can not be resumed or restarted.

At any point in the timers life cycle, the number of elapsed seconds can be calculated.

```php
var_dump($timer->getElapsedSeconds());
// int(2)

```

License
-------

MIT


[Finite State Machine]:http://en.wikipedia.org/wiki/Finite-state_machine

