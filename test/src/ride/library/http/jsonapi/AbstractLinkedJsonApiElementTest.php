<?php

namespace ride\library\http\jsonapi;

abstract class AbstractLinkedJsonApiElementTest extends AbstractJsonApiElementTest {

    public function testLinks() {
        $instance = $this->createTestInstance();
        $name = 'name';
        $name2 = 'name2';
        $value = 'value';
        $link = new JsonApiLink($value);

        $this->assertEquals(array(), $instance->getLinks());
        $this->assertNull($instance->getLink($name));

        $instance->setLink($name, $link);

        $this->assertEquals(array($name => $link), $instance->getLinks());
        $this->assertEquals($link, $instance->getLink($name));

        $instance->setLink($name2, $value);

        $this->assertEquals(array($name => $link, $name2 => $link), $instance->getLinks());
        $this->assertEquals($link, $instance->getLink($name2));

        $instance->clearLinks();

        $this->assertEquals(array(), $instance->getLinks());
        $this->assertNull($instance->getLink($name));
    }

}
