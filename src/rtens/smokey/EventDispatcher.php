<?php
namespace rtens\smokey;

class EventDispatcher {

    public static $CLASSNAME = __CLASS__;

    /**
     * @var array|array[]
     */
    private $listeners;

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
        $classRefl = new \ReflectionClass($event);
        do {
            if ($classRefl->getName() == $className) {
                return true;
            }

            $classRefl = $classRefl->getParentClass();
        } while ($classRefl);

        return false;
    }
}
