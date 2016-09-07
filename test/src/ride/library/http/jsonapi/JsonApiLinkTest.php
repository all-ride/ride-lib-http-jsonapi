<?php

namespace ride\library\http\jsonapi;

class JsonApiLinkTest extends AbstractJsonApiElementTest {

    protected function createTestInstance() {
        return new JsonApiLink('http://link');
    }

    public function testConstruct() {
        $href = 'http://link';

        $resource = new JsonApiLink($href);

        $this->assertEquals($resource->getHref(), $href);
    }

    /**
     * @dataProvider providerJsonSerialize
     */
    public function testJsonSerialize($expected, JsonApiLink $link) {
        $this->assertEquals($expected, $link->jsonSerialize());
    }

    public function providerJsonSerialize() {
        $link1 = $this->createTestInstance();

        $link2 = new JsonApiLink('http://url.to.link');
        $link2->setMeta('meta1', 'value1');

        return array(
            array(
                'http://link',
                $link1,
            ),
            array(
                array(
                    'href' => $link2->getHref(),
                    'meta' => array(
                        'meta1' => 'value1',
                    ),
                ),
                $link2,
            ),
        );
    }

}
