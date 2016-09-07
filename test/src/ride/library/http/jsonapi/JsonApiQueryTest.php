<?php

namespace ride\library\http\jsonapi;

use \PHPUnit_Framework_TestCase;

class JsonApiQueryTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider providerGetInclude
     */
    public function testGetInclude($expected, $include) {
        $query = new JsonApiQuery(array(JsonApiQuery::PARAMETER_INCLUDE => $include));
        $result = $query->getInclude();

        $this->assertEquals($result, $expected);
    }

    public function providerGetInclude() {
        return array(
            array(array('type1' => true), 'type1'),
            array(array('type1' => true, 'type2' => true), 'type1,type2'),
        );
    }

    public function testGetIncludeWithNoParameters() {
        $query = new JsonApiQuery(array());

        $this->assertNull($query->getInclude());
        $this->assertNull($query->getInclude());
    }

    /**
     * @dataProvider providerIsIncluded
     */
    public function testIsIncluded($expected, $include, $type) {
        $query = new JsonApiQuery(array(JsonApiQuery::PARAMETER_INCLUDE => $include));
        $result = $query->isIncluded($type);

        $this->assertEquals($result, $expected);
    }

    public function providerIsIncluded() {
        return array(
            array(true, null, 'type1'),
            array(false, null, 'type1.attribute'),
            array(true, 'type1,type2', 'type2'),
            array(false, 'type1,type2', 'type3'),
            array(true, 'type1,type2', null),
            array(true, 'type1.attribute,type2', 'type1'),
        );
    }

    /**
     * @dataProvider providerGetFields
     */
    public function testGetFields($expected, $fields, $type) {
        $query = new JsonApiQuery(array(JsonApiQuery::PARAMETER_FIELDS => $fields));
        $result = $query->getFields($type);

        $this->assertEquals($result, $expected);
    }

    public function providerGetFields() {
        return array(
            array(null, null, 'type'),
            array(
                null,
                array(
                    'type1' => 'attribute1,attribute2',
                ),
                'type2',
            ),
            array(
                array(
                    'attribute1' => true,
                    'attribute2' => true,
                ),
                array(
                    'type1' => 'attribute1,attribute2',
                ),
                'type1',
            ),
        );
    }

    /**
     * @dataProvider providerGetFieldsThrowsExceptionWhenInvalidValueProvided
     * @expectedException ride\library\http\jsonapi\exception\BadRequestJsonApiException
     */
    public function testGetFieldsThrowsExceptionWhenInvalidValueProvided($fields, $type) {
        $query = new JsonApiQuery(array(JsonApiQuery::PARAMETER_FIELDS => $fields));
        $result = $query->getFields($type);
    }

    public function providerGetFieldsThrowsExceptionWhenInvalidValueProvided() {
        return array(
            array(
                'type1',
                'type1',
            ),
        );
    }

    /**
     * @dataProvider providerIsFieldRequested
     */
    public function testIsFieldRequested($expected, $fields, $type, $field) {
        $query = new JsonApiQuery(array(JsonApiQuery::PARAMETER_FIELDS => $fields));
        $result = $query->isFieldRequested($type, $field);

        $this->assertEquals($result, $expected);
    }

    public function providerIsFieldRequested() {
        return array(
            array(true, null, 'type', 'attribute'),
            array(
                true,
                array(
                    'type1' => 'attribute1,attribute2',
                ),
                'type1',
                'attribute1',
            ),
            array(
                false,
                array(
                    'type1' => 'attribute1,attribute2',
                ),
                'type1',
                'attribute3',
            ),
        );
    }

    public function testGetFilters() {
        $expected = array('test');

        $query = new JsonApiQuery(array(JsonApiQuery::PARAMETER_FILTER => $expected));
        $result = $query->getFilters();

        $this->assertEquals($result, $expected);
    }

    /**
     * @dataProvider providerGetFilter
     */
    public function testGetFilter($expected, $filters, $filter, $default = null) {
        $query = new JsonApiQuery(array(JsonApiQuery::PARAMETER_FILTER => $filters));
        $result = $query->getFilter($filter, $default);

        $this->assertEquals($result, $expected);
    }

    public function providerGetFilter() {
        return array(
            array(
                null,
                null,
                'filter',
            ),
            array(
                'query',
                array('filter' => 'query'),
                'filter',
            ),
            array(
                null,
                array('filter' => 'query'),
                'filter2',
            ),
            array(
                'default',
                array('filter' => 'query'),
                'filter2',
                'default'
            ),
        );
    }

    /**
     * @dataProvider providerGetLimit
     */
    public function testGetLimit($expected, $limit, $default = null) {
        $query = new JsonApiQuery(array(JsonApiQuery::PARAMETER_PAGE => array(JsonApiQuery::PARAMETER_LIMIT => $limit)));
        if ($default === null) {
            $result = $query->getLimit();
        } else {
            $result = $query->getLimit($default);
        }

        $this->assertEquals($result, $expected);
    }

    public function providerGetLimit() {
        return array(
            array(1, null, 1),
            array(5, 5, 1),
        );
    }

    /**
     * @dataProvider providerGetLimitThrowsExceptionWhenInvalidLimitParameterProvided
     * @expectedException ride\library\http\jsonapi\exception\BadRequestJsonApiException
     */
    public function testGetLimitThrowsExceptionWhenInvalidLimitParameterProvided($limit, $default = null, $maximum = null) {
        $query = new JsonApiQuery(array(JsonApiQuery::PARAMETER_PAGE => array(JsonApiQuery::PARAMETER_LIMIT => $limit)));
        $query->getLimit($default, $maximum);
    }

    public function providerGetLimitThrowsExceptionWhenInvalidLimitParameterProvided() {
        return array(
            array('test'),
            array(null, -1),
            array(20, 5, 10),
        );
    }

    /**
     * @dataProvider providerGetOffset
     */
    public function testGetOffset($expected, $offset) {
        $query = new JsonApiQuery(array(JsonApiQuery::PARAMETER_PAGE => array(JsonApiQuery::PARAMETER_OFFSET => $offset)));
        $result = $query->getOffset();

        $this->assertEquals($result, $expected);
    }

    public function providerGetOffset() {
        return array(
            array(0, null),
            array(10, 10),
        );
    }

    /**
     * @dataProvider providerGetOffsetThrowsExceptionWhenInvalidOffsetParameterProvided
     * @expectedException ride\library\http\jsonapi\exception\BadRequestJsonApiException
     */
    public function testGetOffsetThrowsExceptionWhenInvalidOffsetParameterProvided($offset) {
        $query = new JsonApiQuery(array(JsonApiQuery::PARAMETER_PAGE => array(JsonApiQuery::PARAMETER_OFFSET => $offset)));
        $query->getOffset();
    }

    public function providerGetOffsetThrowsExceptionWhenInvalidOffsetParameterProvided() {
        return array(
            array('test'),
            array(-1),
        );
    }

    /**
     * @dataProvider providerGetSort
     */
    public function testGetSort($expected, $sort, $default = null) {
        $query = new JsonApiQuery(array(JsonApiQuery::PARAMETER_SORT => $sort));
        $result = $query->getSort($default);

        $this->assertEquals($result, $expected);
    }

    public function providerGetSort() {
        return array(
            array(array(), null),
            array(array('field' => JsonApiQuery::SORT_ASC), null, '+field'),
            array(array('field' => JsonApiQuery::SORT_ASC), 'field'),
            array(array('field' => JsonApiQuery::SORT_DESC), '-field'),
            array(array('field1' => JsonApiQuery::SORT_ASC, 'field2' => JsonApiQuery::SORT_DESC), '+field1,-field2'),
        );
    }

    public function testGetParameter() {
        $parameters = array(
            'parameter1' => 'value1',
            'parameter2' => 'value2',
        );

        $query = new JsonApiQuery($parameters);

        $this->assertEquals($query->getParameter('parameter1'), 'value1');
        $this->assertEquals($query->getParameter('parameter2'), 'value2');
        $this->assertEquals($query->getParameter('parameter3'), null);
        $this->assertEquals($query->getParameter('parameter3', 'default'), 'default');
    }

}
