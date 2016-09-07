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

    public function testJsonSerialize() {
        $relationship = $this->createTestInstance();
        $relationship->setMeta('meta1', 'value1');
        $relationship->setLink('link1', 'http://url.to.link');
        $relationship->setLink('link2', 'http://url.to.link');
        $relationship->setResource(null);

        $expected = array(
            'data' => null,
            'meta' => array(
                'meta1' => 'value1',
            ),
            'links' => array(
                'link1' => new JsonApiLink('http://url.to.link'),
                'link2' => new JsonApiLink('http://url.to.link'),
            ),
        );

        $this->assertEquals($relationship->jsonSerialize(), $expected);

        $resource = new JsonApiResource('type', 'id');

        $relationship->setResource($resource);
        $expected['data'] = $resource->getJsonValue(false);

        $this->assertEquals($relationship->jsonSerialize(), $expected);

        $relationship->setResourceCollection(array($resource));
        $expected['data'] = array($expected['data']);

        $this->assertEquals($relationship->jsonSerialize(), $expected);
    }

    /**
     * @expectedException ride\library\http\jsonapi\exception\JsonApiException
     */
    public function testJsonSerializeThrowsExceptionWhenNoDataSet() {
        $relationship = $this->createTestInstance();
        $relationship->jsonSerialize();
    }

}
