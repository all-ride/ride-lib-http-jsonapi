<?php

namespace ride\library\http\jsonapi;

use \PHPUnit_Framework_TestCase;

class JsonApiDocumentTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->document = new JsonApiDocument();
    }

    public function testApi() {
        $api = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApi')
                    ->getMock();

        $this->assertNull($this->document->getApi());

        $this->document->setApi($api);

        $this->assertEquals($this->document->getApi(), $api);
    }

    public function testQuery() {
        $query = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiQuery')
                      ->setConstructorArgs(array(array()))
                      ->getMock();

        $this->assertNull($this->document->getQuery());

        $this->document->setQuery($query);

        $this->assertEquals($this->document->getQuery(), $query);
    }

    public function testStatusCode() {
        $this->assertEquals($this->document->getStatusCode(), 204);

        $this->document->setStatusCode(299);

        $this->assertEquals($this->document->getStatusCode(), 299);

        $this->document->setStatusCode(null);

        $this->assertEquals($this->document->getStatusCode(), 204);

        $type = 'type';
        $id = 'id';
        $resource = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiResource')
                         ->setConstructorArgs(array($type, $id))
                         ->getMock();
        $resource->expects($this->once())
                 ->method('getRelationships')
                 ->will($this->returnValue(array()));

        $this->document->setResourceData($type, $resource);

        $this->assertEquals($this->document->getStatusCode(), 200);

        $error = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiError')
                      ->getMock();

        $this->document->addError($error);

        $this->assertEquals($this->document->getStatusCode(), 400);

        $error = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiError')
                      ->getMock();
        $error->expects($this->any())
              ->method('getStatusCode')
              ->will($this->returnValue(500));

        $this->document->addError($error);

        $this->assertEquals($this->document->getStatusCode(), 500);
    }

    public function testHasContentWithErrors() {
        $this->assertFalse($this->document->hasContent());

        $error = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiError')
                      ->getMock();

        $this->document->addError($error);

        $this->assertTrue($this->document->hasContent());
    }

    public function testHasContentWithMeta() {
        $this->assertFalse($this->document->hasContent());

        $this->document->setMeta('meta1', 'value1');

        $this->assertTrue($this->document->hasContent());
    }

    public function testHasContentWithResourceData() {
        $this->assertFalse($this->document->hasContent());

        $type = 'type';
        $id = 'id';
        $resource = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiResource')
                         ->setConstructorArgs(array($type, $id))
                         ->getMock();
        $resource->expects($this->once())
                 ->method('getRelationships')
                 ->will($this->returnValue(array()));

        $this->document->setResourceData($type, $resource);

        $this->assertTrue($this->document->hasContent());
    }

    public function testHasContentWithResourceCollection() {
        $this->assertFalse($this->document->hasContent());

        $type = 'type';
        $id = 'id';
        $resource = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiResource')
                         ->setConstructorArgs(array($type, $id))
                         ->getMock();
        $resource->expects($this->once())
                 ->method('getRelationships')
                 ->will($this->returnValue(array()));

        $this->document->setResourceCollection($type, array($resource));

        $this->assertTrue($this->document->hasContent());
    }

    public function testErrors() {
        $this->assertEquals($this->document->getErrors(), array());
        $this->assertEquals($this->document->getStatusCode(), 204);

        $error1 = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiError')
                       ->getMock();

        $this->document->addError($error1);

        $this->assertEquals($this->document->getStatusCode(), 400);

        $error2 = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiError')
                       ->getMock();
        $error2->expects($this->any())
               ->method('getStatusCode')
               ->will($this->returnValue(500));

        $this->document->addError($error2);

        $this->assertEquals($this->document->getStatusCode(), 500);
        $this->assertEquals($this->document->getErrors(), array($error1, $error2));
    }

    public function testSetResourceCollection() {
        $this->assertEquals($this->document->getErrors(), array());
        $this->assertFalse($this->document->getData());

        $error1 = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiError')
                       ->getMock();

        $this->document->addError($error1);

        $type = 'type';
        $id = 'id';
        $resource = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiResource')
                         ->setConstructorArgs(array($type, $id))
                         ->getMock();
        $resource->expects($this->once())
                 ->method('getRelationships')
                 ->will($this->returnValue(array()));

        $collection = array($resource);

        $this->document->setResourceCollection($type, $collection);

        $this->assertEquals($this->document->getData(), $collection);
        $this->assertEquals($this->document->getErrors(), array());
    }

    public function testSetResourceCollectionWithResourceAdaption() {
        $type = 'type';
        $id = 'id';
        $data = array(
            'type' => $type,
            'id' => $id,
        );

        $resource = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiResource')
                         ->setConstructorArgs(array($type, $id))
                         ->getMock();
        $resource->expects($this->once())
                 ->method('getRelationships')
                 ->will($this->returnValue(array()));

        $resourceAdapter = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiResourceAdapter')
                                ->getMock();
        $resourceAdapter->expects($this->once())
                        ->method('getResource')
                        ->with($this->equalTo($data))
                        ->will($this->returnValue($resource));

        $api = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApi')
                    ->getMock();
        $api->expects($this->once())
            ->method('getResourceAdapter')
            ->with($this->equalTo($type))
            ->will($this->returnValue($resourceAdapter));

        $collection = array($data);
        $expected = array($resource);

        $this->document->setApi($api);
        $this->document->setResourceCollection($type, $collection);

        $this->assertEquals($this->document->getData(), $expected);
    }

    public function testSetResourceData() {
        $this->assertEquals($this->document->getErrors(), array());
        $this->assertFalse($this->document->getData());

        $error = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiError')
                       ->getMock();

        $this->document->addError($error);

        $type = 'type';
        $id = 'id';
        $link = 'self';
        $url = 'http://url';
        $links = array(
            $link => new JsonApiLink($url),
        );

        $resource = new JsonApiResource($type, $id);
        $resource->setLink($link, $url);

        $this->document->setResourceData($type, $resource);

        $this->assertEquals($this->document->getData(), $resource);
        $this->assertEquals($this->document->getErrors(), array());
        $this->assertEquals($this->document->getLinks(), $links);
        $this->assertEquals($resource->getLinks(), array());
    }

    public function testSetResourceDataWithResourceAdaption() {
        $type = 'type';
        $id = 'id';
        $data = array(
            'type' => $type,
            'id' => $id,
        );

        $resource = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiResource')
                         ->setConstructorArgs(array($type, $id))
                         ->getMock();
        $resource->expects($this->once())
                 ->method('getRelationships')
                 ->will($this->returnValue(array()));

        $resourceAdapter = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiResourceAdapter')
                                ->getMock();
        $resourceAdapter->expects($this->once())
                        ->method('getResource')
                        ->with($this->equalTo($data))
                        ->will($this->returnValue($resource));

        $api = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApi')
                    ->getMock();
        $api->expects($this->once())
            ->method('getResourceAdapter')
            ->with($this->equalTo($type))
            ->will($this->returnValue($resourceAdapter));

        $this->document->setApi($api);
        $this->document->setResourceData($type, $data);

        $this->assertEquals($this->document->getData(), $resource);
    }

    public function testSetRelationshipData() {
        $this->assertEquals($this->document->getErrors(), array());
        $this->assertFalse($this->document->getData());

        $error = $this->getMockBuilder('ride\\library\\http\\jsonapi\\JsonApiError')
                      ->getMock();

        $this->document->addError($error);

        $type = 'type';
        $id = 'id';
        $link = 'self';
        $url = 'http://url';
        $links = array(
            $link => new JsonApiLink($url),
        );

        $resource = new JsonApiResource($type, $id);
        $resource->setLink($link, $url);

        $relationship = new JsonApiRelationship();
        $relationship->setResource($resource);

        $this->document->setRelationshipData($relationship);

        $this->assertEquals($this->document->getData(), $resource);
        $this->assertEquals($this->document->getErrors(), array());
        $this->assertEquals($this->document->getLinks(), $links);
        $this->assertEquals($resource->getLinks(), array());
    }

    /**
     * @dataProvider providerGetJsonValue
     */
    public function testGetJsonValue($expected, JsonApiDocument $document, $full = true) {
        $this->assertEquals($expected, $document->getJsonValue($full));
    }

    public function providerGetJsonValue() {
        $document1 = new JsonApiDocument();
        $document1->setMeta('meta1', 'value1');
        $document1->setLink('self', 'http://url');
        $expected1 = array(
            'jsonapi' => array(
                'version' => '1.0',
            ),
            'meta' => array(
                'meta1' => 'value1',
            ),
            'links' => array(
                'self' => $document1->getLink('self'),
            ),
        );

        $error = new JsonApiError();
        $error->setCode('my-error');
        $document2 = new JsonApiDocument();
        $document2->addError($error);
        $expected2 = array(
            'jsonapi' => array(
                'version' => '1.0',
            ),
            'errors' => array(
                $error,
            ),
        );

        $resource = new JsonApiResource('type', 'id');
        $resource->setAttribute('attribute1', 'value1');
        $resource->setAttribute('attribute2', 'value2');

        $document3 = new JsonApiDocument();
        $document3->setResourceData('type', $resource);
        $expected3 = array(
            'jsonapi' => array(
                'version' => '1.0',
            ),
            'data' => $resource->getJsonValue(true),
        );

        $document4 = new JsonApiDocument();
        $document4->setResourceData('type', null);
        $expected4 = array(
            'jsonapi' => array(
                'version' => '1.0',
            ),
            'data' => null,
        );

        return array(
            array(
                $expected1,
                $document1,
            ),
            array(
                $expected2,
                $document2,
            ),
            array(
                $expected3,
                $document3,
            ),
            array(
                $expected4,
                $document4,
            ),
        );
    }

}
