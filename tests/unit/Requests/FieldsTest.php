<?php

use Giadc\JsonApiRequest\Requests\Fields;
use PHPUnit\Framework\TestCase;

class FieldsTest extends TestCase
{
    private $fields;

    public function setUp(): void
    {
        $this->fields = new Fields([
            'user' => 'firstName,lastName',
            'article' => 'body',
        ]);
    }

    public function test_it_returns_a_field_array(): void
    {
        $expected = [
            'user' => ['firstName', 'lastName'],
            'article' => ['body'],
        ];

        $this->assertEquals($expected, $this->fields->toArray());
    }

    public function test_it_returns_a_query_string(): void
    {
        $expected = 'fields[user]=firstName,lastName&fields[article]=body';
        $this->assertEquals($expected, $this->fields->getQueryString());
        $this->assertEquals([$expected], $this->fields->getParamsArray());
    }

    public function test_it_allows_an_additional_field_to_be_added(): void
    {
        $expected = [
            'user' => ['firstName', 'lastName'],
            'article' => ['body'],
            'book' => ['title'],
        ];

        $this->fields->add('book', 'title');
        $this->assertEquals($expected, $this->fields->toArray());
    }
}
