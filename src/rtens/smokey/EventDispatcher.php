<?php
namespace rtens\smokey;

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
     * @param string $className
     * @param \callable $listener
     */
    public function addListener($className, $listener) {
        if (!isset($this->listeners[$className])) {
            $this->listeners[$className] = array();
        }
        $this->listeners[$className][] = $listener;
    }

    public function fire($event) {
        foreach ($this->listeners as $className => $listeners) {
            if ($this->isEventMatching($event, $className)) {
                foreach ($listeners as $listener) {
                    /** @var $callable \callable */
                    $callable = $listener;
                    $callable($event);
                }
            }
        }
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
