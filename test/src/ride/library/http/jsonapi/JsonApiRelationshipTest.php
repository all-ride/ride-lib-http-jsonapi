<?php

namespace ride\library\http\jsonapi;

class JsonApiRelationshipTest extends AbstractLinkedJsonApiElementTest {

    protected function createTestInstance() {
        return new JsonApiRelationship();
    }

    public function testData() {
        $resource = new JsonApiResource('type');
        $collection = array($resource);
        $relationship = $this->createTestInstance();

        $this->assertFalse($relationship->getData());

        $relationship->setResource(null);

        $this->assertNull($relationship->getData());

        $relationship->setResource($resource);

        $this->assertEquals($resource, $relationship->getData());

        $relationship->setResourceCollection($collection);

        $this->assertEquals($collection, $relationship->getData());
    }

    /**
     * @dataProvider providerSetResourceCollectionThrowsExceptionWhenNoResource
     * @expectedException ride\library\http\jsonapi\exception\JsonApiException
     */
    public function testSetResourceCollectionThrowsExceptionWhenNoResource(array $collection) {
        $relationship = $this->createTestInstance();
        $relationship->setResourceCollection($collection);
    }

    public function providerSetResourceCollectionThrowsExceptionWhenNoResource() {
        return array(
            array(array(null)),
            array(array(false)),
            array(array('string')),
            array(array($this)),
        );
    }

}
