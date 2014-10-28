<?php
namespace watoki\smokey;

class EventDispatcher {

    public static $CLASSNAME = __CLASS__;

    /**
     * @var array|array[]
     */
    private $listeners;

    private static $matchingsCache = array();

    function __construct() {
        $this->listeners = array();
    }

    /**
     * @param string $eventClass
     * @param callable $listener
     */
    public function addListener($eventClass, $listener) {
        if (!isset($this->listeners[$eventClass])) {
            $this->listeners[$eventClass] = array();
        }
        $this->listeners[$eventClass][] = $listener;
    }

    /**
     * @param object $event
     * @return Result
     */
    public function fire($event) {
        $result = new DirectResult($event);
        foreach ($this->listeners as $className => $listeners) {
            if ($this->isEventMatching($event, $className)) {
                foreach ($listeners as $listener) {
                    try {
                        $result->addSuccess(call_user_func($listener, $event));
                    } catch (\Exception $e) {
                        $result->addException($e);
                    }
                }
            }
        }
        return $result;
    }

    private function isEventMatching($event, $className) {
        $eventName = get_class($event);

        if ($eventName == $className) {
            return true;
        }

        $cacheKey = $eventName . $className;
        if (isset(self::$matchingsCache[$cacheKey])) {
            return self::$matchingsCache[$cacheKey];
        }

        $classRefl = new \ReflectionClass($event);
        do {
            if ($classRefl->getName() == $className) {
                self::$matchingsCache[$cacheKey] = true;
                return true;
            }

            $classRefl = $classRefl->getParentClass();
        } while ($classRefl);

        self::$matchingsCache[$cacheKey] = false;
        return false;
    }
}
