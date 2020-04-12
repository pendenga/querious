<?php

namespace Pendenga\Querious\Test;

use Opis\JsonSchema\Schema;
use Pendenga\Querious\QueriousException;
use Pendenga\Querious\Query;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryTest
 * @package Pendenga\Querious\Test
 */
class QueryTest extends TestCase
{
    // simplest schema
    protected $test_schema = '{"required": ["command"], "properties": {"command": {"type": "string"}}}';

    // simplest schema
    protected $test_schema_array = '{"required": ["authors"], "properties": {"authors": {"items": {"type": "string"}}}}';

    /**
     * @expectedException \Opis\JsonSchema\Exception\InvalidSchemaException
     * @expectedExceptionMessage Schema must be an object or a boolean, NULL given
     * @throws QueriousException
     */
    public function testErrorInvalidJson()
    {
        $query = new Query('abc', Schema::fromJsonString('{"$schema'));

        $this->assertTrue(true);
    }

    /**
     * @expectedException \Pendenga\Querious\QueriousException
     * @expectedExceptionMessage Schema validation error - required: command
     */
    public function testErrorRequiredMissing()
    {
        $query = new Query('{"authors":["pendenga"]}', Schema::fromJsonString($this->test_schema));

        $this->assertTrue(true);
    }

    /**
     * @expectedException \Pendenga\Querious\QueriousException
     * @expectedExceptionMessage Schema validation error - string expected, integer used
     */
    public function testErrorInvalidType()
    {
        $query = new Query('{"authors":[5]}', Schema::fromJsonString($this->test_schema_array));

        $this->assertTrue(true);
    }

    /**
     * @throws QueriousException
     */
    public function testLoad()
    {
        $query = new Query('{"authors":["pendenga"]}', Schema::fromJsonString($this->test_schema_array));

        $this->assertTrue(true);
    }

    /**
     * @throws QueriousException
     */
    public function testSchemaQueryV1()
    {
        $query = new Query(
            '{
              "statement": "SELECT * FROM users WHERE username = :username",
              "command": "prepare",
              "parameters": ["username"],
              "authors": ["Grant Anderson"]
            }',
            Schema::fromJsonString(file_get_contents(__DIR__ . '/../schema/query-v1.json'))
        );
        $this->assertEquals('SELECT * FROM users WHERE username = :username', $query->statement());
        $this->assertEquals('prepare', $query->command());
        $this->assertEquals(['username'], $query->parameters());
        $this->assertEquals(['Grant Anderson'], $query->authors());
        $this->assertTrue(true);
    }
}
