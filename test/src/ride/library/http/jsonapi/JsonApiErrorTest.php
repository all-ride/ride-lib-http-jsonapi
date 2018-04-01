<?php

namespace ride\library\http\jsonapi;

use PHPUnit\Framework\TestCase;

class JsonApiErrorTest extends TestCase {

    public function setUp() {
        $this->error = new JsonApiError();
    }

    public function testId() {
        $id = 'id';

        $this->assertNull($this->error->getId());

        $this->error->setId($id);

        $this->assertEquals($this->error->getId(), $id);

        $this->error->setId(null);

        $this->assertNull($this->error->getId());
    }

    /**
     * @dataProvider providerInvalidStringValue
     * @expectedException ride\library\http\jsonapi\exception\JsonApiException
     */
    public function testSetIdThrowsExceptionWhenInvalidValueProvided($id) {
        $this->error->setId($id);
    }

    public function testStatusCode() {
        $statusCode = 400;

        $this->assertNull($this->error->getStatusCode());

        $this->error->setStatusCode($statusCode);

        $this->assertEquals($this->error->getStatusCode(), $statusCode);

        $this->error->setStatusCode(null);

        $this->assertNull($this->error->getStatusCode());
    }

    /**
     * @dataProvider providerInvalidStatusCodeValue
     * @expectedException ride\library\http\jsonapi\exception\JsonApiException
     */
    public function testSetStatusCodeThrowsExceptionWhenInvalidValueProvided($statusCode) {
        $this->error->setStatusCode($statusCode);
    }

    public function testCode() {
        $code = 'code';

        $this->assertNull($this->error->getCode());

        $this->error->setCode($code);

        $this->assertEquals($this->error->getCode(), $code);

        $this->error->setCode(null);

        $this->assertNull($this->error->getCode());
    }

    /**
     * @dataProvider providerInvalidStringValue
     * @expectedException ride\library\http\jsonapi\exception\JsonApiException
     */
    public function testSetCodeThrowsExceptionWhenInvalidValueProvided($code) {
        $this->error->setCode($code);
    }

    public function testTitle() {
        $title = 'title';

        $this->assertNull($this->error->getTitle());

        $this->error->setTitle($title);

        $this->assertEquals($this->error->getTitle(), $title);

        $this->error->setTitle(null);

        $this->assertNull($this->error->getTitle());
    }

    /**
     * @dataProvider providerInvalidStringValue
     * @expectedException ride\library\http\jsonapi\exception\JsonApiException
     */
    public function testSetTitleThrowsExceptionWhenInvalidValueProvided($title) {
        $this->error->setTitle($title);
    }

    public function testDetail() {
        $detail = 'detail';

        $this->assertNull($this->error->getDetail());

        $this->error->setDetail($detail);

        $this->assertEquals($this->error->getDetail(), $detail);

        $this->error->setDetail(null);

        $this->assertNull($this->error->getDetail());
    }

    /**
     * @dataProvider providerInvalidStringValue
     * @expectedException ride\library\http\jsonapi\exception\JsonApiException
     */
    public function testSetDetailThrowsExceptionWhenInvalidValueProvided($detail) {
        $this->error->setDetail($detail);
    }

    public function testSourcePointer() {
        $sourcePointer = 'sourcePointer';

        $this->assertNull($this->error->getSourcePointer());

        $this->error->setSourcePointer($sourcePointer);

        $this->assertEquals($this->error->getSourcePointer(), $sourcePointer);

        $this->error->setSourcePointer(null);

        $this->assertNull($this->error->getSourcePointer());
    }

    /**
     * @dataProvider providerInvalidStringValue
     * @expectedException ride\library\http\jsonapi\exception\JsonApiException
     */
    public function testSetSourcePointerThrowsExceptionWhenInvalidValueProvided($sourcePointer) {
        $this->error->setSourcePointer($sourcePointer);
    }

    public function testSourceParameter() {
        $sourceParameter = 'sourceParameter';

        $this->assertNull($this->error->getSourceParameter());

        $this->error->setSourceParameter($sourceParameter);

        $this->assertEquals($this->error->getSourceParameter(), $sourceParameter);

        $this->error->setSourceParameter(null);

        $this->assertNull($this->error->getSourceParameter());
    }

    /**
     * @dataProvider providerInvalidStringValue
     * @expectedException ride\library\http\jsonapi\exception\JsonApiException
     */
    public function testSetSourceParameterThrowsExceptionWhenInvalidValueProvided($sourceParameter) {
        $this->error->setSourceParameter($sourceParameter);
    }

    public function testJsonSerialize() {
        $id = 'id';
        $statusCode = 400;
        $code = 'code';
        $title = 'title';
        $detail = 'detail';
        $sourcePointer = 'sourcePointer';
        $sourceParameter = 'sourceParameter';

        $this->error->setId($id);
        $this->error->setStatusCode($statusCode);
        $this->error->setCode($code);
        $this->error->setTitle($title);
        $this->error->setDetail($detail);
        $this->error->setSourcePointer($sourcePointer);
        $this->error->setSourceParameter($sourceParameter);
        $this->error->setLink('link1', 'http://url.to.link');
        $this->error->setLink('link2', 'http://url.to.link');
        $this->error->setMeta('meta1', 'value1');

        $expected = array(
            'id' => $id,
            'status' => $statusCode,
            'code' => $code,
            'title' => $title,
            'detail' => $detail,
            'source' => array(
                'pointer' => $sourcePointer,
                'parameter' => $sourceParameter,
            ),
            'links' => array(
                'link1' => new JsonApiLink('http://url.to.link'),
                'link2' => new JsonApiLink('http://url.to.link'),
            ),
            'meta' => array(
                'meta1' => 'value1',
            ),
        );

        $this->assertEquals($this->error->jsonSerialize(), $expected);
    }

    /**
     * @expectedException ride\library\http\jsonapi\exception\JsonApiException
     */
    public function testJsonSerializeThrowsExceptionWhenNoPropertiesSet() {
        $this->error->jsonSerialize();
    }

    public function providerInvalidStringValue() {
        return array(
            array(false),
            array(array()),
            array($this),
        );
    }

    public function providerInvalidStatusCodeValue() {
        return array(
            array(false),
            array(200),
            array('test'),
            array(array()),
            array($this),
        );
    }

}
