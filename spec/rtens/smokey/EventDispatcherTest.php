<?php
namespace rtens\smokey;

use rtens\smokey\EventDispatcher;
use rtens\collections\Map;
use rtens\collections\Liste;

class EventDispatcherTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var EventDispatcher
     */
    public $dispatcher;

    /**
     * @var Map
     */
    public $caught;

    public function testRegisterEvent() {
        $this->givenTheEvent('MyEvent');
        $this->givenIHaveRegisteredAListener_For('MyListener', 'MyEvent');

        $this->whenIFire('MyEvent');
        $this->thenListener_ShouldBeInvokedWith('MyListener', 'MyEvent');
    }

    public function testSeveralListeners() {
        $this->givenTheEvent('MultiListenerEvent');
        $this->givenIHaveRegisteredAListener_For('listenerOne', 'MultiListenerEvent');
        $this->givenIHaveRegisteredAListener_For('listenerTwo', 'MultiListenerEvent');

        $this->whenIFire('MultiListenerEvent');
        $this->thenListener_ShouldBeInvokedWith('listenerOne', 'MultiListenerEvent');
        $this->thenListener_ShouldBeInvokedWith('listenerTwo', 'MultiListenerEvent');
    }

    public function testRegisterBaseEvent() {
        $this->givenTheEvent('BaseEvent');
        $this->givenTheEvent_Extending('SubEvent', 'BaseEvent');
        $this->givenTheEvent_Extending('SubSubEvent', 'SubEvent');
        $this->givenIHaveRegisteredAListener_For('baseListener', 'BaseEvent');
        $this->givenIHaveRegisteredAListener_For('subListener', 'SubEvent');
        $this->givenIHaveRegisteredAListener_For('subSubListener', 'SubSubEvent');

        $this->whenIFire('BaseEvent');
        $this->thenListener_ShouldNotBeInvokedWith('subSubListener', 'BaseEvent');
        $this->thenListener_ShouldNotBeInvokedWith('subListener', 'BaseEvent');
        $this->thenListener_ShouldBeInvokedWith('baseListener', 'BaseEvent');

        $this->whenIFire('SubEvent');
        $this->thenListener_ShouldNotBeInvokedWith('subSubListener', 'SubEvent');
        $this->thenListener_ShouldBeInvokedWith('subListener', 'SubEvent');
        $this->thenListener_ShouldBeInvokedWith('baseListener', 'SubEvent');

        $this->whenIFire('SubSubEvent');
        $this->thenListener_ShouldBeInvokedWith('subSubListener', 'SubSubEvent');
        $this->thenListener_ShouldBeInvokedWith('subListener', 'SubSubEvent');
        $this->thenListener_ShouldBeInvokedWith('baseListener', 'SubSubEvent');
    }

    private function givenTheEvent($event) {
        eval("class $event {}");
    }

    private function givenTheEvent_Extending($subEvent, $baseEvent) {
        eval("class $subEvent extends $baseEvent {}");
    }

    private function givenIHaveRegisteredAListener_For($listener, $event) {
        $this->caught->set($listener, new Liste());

        $caught = $this->caught;
        $this->dispatcher->addListener($event, function ($event) use ($caught, $listener) {
            /** @var $caught Map */
            /** @var $list Liste */
            $list = $caught->get($listener);
            $list->append($event);
        });
    }

    private function whenIFire($event) {
        $this->dispatcher->fire(new $event);
    }

    private function thenListener_ShouldBeInvokedWith($listener, $event) {
        $this->assertGreaterThan(0, $this->getCaughtEventsCount($listener, $event));
    }

    private function thenListener_ShouldNotBeInvokedWith($listener, $event) {
        $this->assertEquals(0, $this->getCaughtEventsCount($listener, $event));
    }

    private function getCaughtEventsCount($listener, $event) {
        $count = 0;

        if ($this->caught->has($listener)) {
            foreach ($this->caught->get($listener) as $caughtEvent) {
                if (get_class($caughtEvent) == $event) {
                    $count++;
                }
            }
        }

        return $count;
    }

    protected function setUp() {
        parent::setUp();

        $this->dispatcher = new EventDispatcher();
        $this->caught = new Map();
    }

}
