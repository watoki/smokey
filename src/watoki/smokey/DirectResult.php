<?php
namespace watoki\smokey;

class DirectResult implements Result {

    /** @var array|mixed[] */
    private $successes = array();

    /** @var array|\Exception[] */
    private $exceptions = array();

    /**
     * @param mixed $value
     */
    public function addSuccess($value) {
        $this->successes[] = $value;
    }

    /**
     * @param \Exception $e
     */
    public function addException(\Exception $e) {
        $this->exceptions[] = $e;
    }

    /**
     * @param callable $callback
     * @return static
     */
    public function onSuccess($callback) {
        foreach ($this->successes as $success) {
            call_user_func($callback, $success);
        }
        return $this;
    }

    /**
     * @param callable $callback
     * @return static
     */
    public function onException($callback) {
        foreach ($this->exceptions as $exception) {
            call_user_func($callback, $exception);
        }
        return $this;
    }
}