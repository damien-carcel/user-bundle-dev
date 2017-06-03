<?php

namespace spec\Carcel\Bundle\UserBundle\Factory;

use Carcel\Bundle\UserBundle\Factory\SwiftMessageFactory;
use PhpSpec\ObjectBehavior;

class SwiftMessageFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SwiftMessageFactory::class);
    }

    function it_creates_a_swift_message()
    {
        $message = $this->create();

        $message->shouldBeAnInstanceOf(\Swift_Message::class);
    }
}
