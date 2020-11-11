<?php

use Giadc\JsonApiRequest\Requests\Excludes;
use PHPUnit\Framework\TestCase;

class ExcludesTest extends TestCase
{
    private $excludes;

    public function setUp(): void
    {
        $this->excludes = new Excludes([
            'user' => 'firstName,lastName',
            'article' => 'body',
        ]);
    }

    public function test_it_returns_a_exclude_array()
    {
        $expected = [
            'user' => ['firstName', 'lastName'],
            'article' => ['body'],
        ];

        $this->assertEquals($expected, $this->excludes->toArray());
    }

    public function test_it_returns_a_query_string()
    {
        $expected = 'excludes[user]=firstName,lastName&excludes[article]=body';
        $this->assertEquals($expected, $this->excludes->getQueryString());
        $this->assertEquals([$expected], $this->excludes->getParamsArray());
    }

    public function test_it_allows_an_additional_exclude_to_be_added()
    {
        $expected = [
            'user' => ['firstName', 'lastName'],
            'article' => ['body'],
            'book' => ['title'],
        ];

        $this->excludes->add('book', 'title');
        $this->assertEquals($expected, $this->excludes->toArray());
    }
}
