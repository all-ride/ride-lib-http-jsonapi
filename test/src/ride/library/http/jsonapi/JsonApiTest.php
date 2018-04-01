<?php

namespace ride\library\http\jsonapi;

use PHPUnit\Framework\TestCase;

class JsonApiTest extends TestCase {

    public function setUp() {
        $this->api = new JsonApi();
    }

    public function testResourceAdapter() {
        $resourceAdapter = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiResourceAdapter')->getMock();

        $resourceAdapters = array(
            'test1' => $resourceAdapter,
            'test2' => $resourceAdapter,
        );

        $this->api->setResourceAdapters($resourceAdapters);
        $this->api->setResourceAdapter('test3', $resourceAdapter);

        $this->assertEquals($resourceAdapter, $this->api->getResourceAdapter('test1'));
        $this->assertEquals($resourceAdapter, $this->api->getResourceAdapter('test2'));
        $this->assertEquals($resourceAdapter, $this->api->getResourceAdapter('test3'));
    }

    /**
     * @expectedException ride\library\http\jsonapi\exception\JsonApiException
     */
    public function testGetResourceAdapterThrowsExceptionWhenAdapterDoesNotExist() {
        $this->api->getResourceAdapter('test');
    }

    public function testCreateQuery() {
        $parameters = array(
            'test' => $this,
        );

        $query = $this->api->createQuery($parameters);

        $this->assertTrue($query instanceof JsonApiQuery);
        $this->assertEquals($this, $query->getParameter('test'));
    }

    public function testCreateDocument() {
        $document = $this->api->createDocument();

        $this->assertTrue($document instanceof JsonApiDocument);
        $this->assertEquals($this->api, $document->getApi());
        $this->assertNull($document->getQuery());

        $query = new JsonApiQuery(array());

        $document = $this->api->createDocument($query);

        $this->assertTrue($document instanceof JsonApiDocument);
        $this->assertEquals($this->api, $document->getApi());
        $this->assertEquals($query, $document->getQuery());
    }

    public function testCreateResource() {
        $type = 'type';
        $id = 'id';
        $relationshipPath = 'relationshipPath';

        $resource = $this->api->createResource($type, $id);

        $this->assertTrue($resource instanceof JsonApiResource);
        $this->assertEquals($type, $resource->getType());
        $this->assertEquals($id, $resource->getId());
        $this->assertNull($resource->getRelationshipPath());

        $resource = $this->api->createResource($type, $id, $relationshipPath);

        $this->assertTrue($resource instanceof JsonApiResource);
        $this->assertEquals($type, $resource->getType());
        $this->assertEquals($id, $resource->getId());
        $this->assertEquals($relationshipPath, $resource->getRelationshipPath());
    }

    public function testCreateRelationship() {
        $relationship = $this->api->createRelationship();

        $this->assertTrue($relationship instanceof JsonApiRelationship);
    }

    public function testCreateErrorWithoutArguments() {
        $error = $this->api->createError();

        $this->assertTrue($error instanceof JsonApiError);
        $this->assertNull($error->getStatusCode());
        $this->assertNull($error->getCode());
        $this->assertNull($error->getTitle());
        $this->assertNull($error->getDetail());
    }

    public function testCreateErrorWithArguments() {
        $statusCode = 400;
        $code = 'code';
        $title = 'title';
        $detail = 'detail';

        $error = $this->api->createError($statusCode, $code, $title, $detail);

        $this->assertTrue($error instanceof JsonApiError);
        $this->assertEquals($statusCode, $error->getStatusCode());
        $this->assertEquals($code, $error->getCode());
        $this->assertEquals($title, $error->getTitle());
        $this->assertEquals($detail, $error->getDetail());
    }

}
