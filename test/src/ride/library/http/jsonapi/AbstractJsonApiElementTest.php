<?php

namespace ride\library\http\jsonapi;

use PHPUnit\Framework\TestCase;

abstract class AbstractJsonApiElementTest extends TestCase {

    abstract protected function createTestInstance();

    public function testMeta() {
        $instance = $this->createTestInstance();
        $default = 'default';
        $value = 'value';
        $value2 = 'value2';
        $variable = 'variable';
        $variable2 = 'variable2';
        $variables = array($variable => $value);

        $this->assertEquals(array(), $instance->getMeta());
        $this->assertNull($instance->getMeta($variable));
        $this->assertEquals($default, $instance->getMeta($variable, $default));

        $instance->setMeta($variable, $value);

        $this->assertEquals($value, $instance->getMeta($variable));
        $this->assertEquals($variables, $instance->getMeta());

        $instance->setMeta($variable2, $value2);

        $variables[$variable2] = $value2;

        $this->assertEquals($value, $instance->getMeta($variable));
        $this->assertEquals($value2, $instance->getMeta($variable2));
        $this->assertEquals($variables, $instance->getMeta());

        $variables = array(
            $variable . '3' => $value . '3',
            $variable . '4' => $value . '4',
        );

        $instance->setMeta($variables);

        $this->assertNull($instance->getMeta($variable));
        $this->assertNull($instance->getMeta($variable2));
        $this->assertEquals($value . '3', $instance->getMeta($variable . '3'));
        $this->assertEquals($value . '4', $instance->getMeta($variable . '4'));

        $this->assertEquals($variables, $instance->getMeta());
    }

}
