<?php
namespace watoki\smokey;

interface Result {

    /**
     * @param callable $callback
     * @return static
     */
    public function onSuccess($callback);

    /**
     * @param callable $callback
     * @return static
     */
    public function onException($callback);

} 