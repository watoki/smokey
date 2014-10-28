<?php
namespace watoki\smokey;

interface Dispatcher {

    /**
     * @param mixed $event
     * @return Result
     */
    public function fire($event);

} 