<?php

namespace Pendenga\Querious\Test;

use Exception;
use Opis\JsonSchema\Schema;
use PDO;
use Pendenga\File\Ini;
use Pendenga\File\FileNotFoundException;
use Pendenga\Querious\Querious;
use Pendenga\Querious\QueriousException;
use Pendenga\Querious\Query;
use PHPUnit\Framework\TestCase;

/**
 * Class AcceptanceTest
 * @package Pendenga\Querious\Test
 */
class AcceptanceTest extends TestCase
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var string
     */
    protected $query_json;

    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @throws QueriousException
     * @throws FileNotFoundException
     */
    public function setup() {
        // setup PDO
        $ini = Ini::section('mysql connection');
        $this->pdo = new PDO("mysql:host={$ini['servername']};dbname={$ini['database']}", $ini['username'], $ini['password']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // find query definition file
        $full_path = Ini::get('querious_repo') . 'user-data-v1.json';
        $this->query_json = file_get_contents($full_path);
        if ($this->query_json === false) {
            throw new QueriousException("Query file not found: {$full_path}");
        }

        // find schema to validate against
        $schema_path = __DIR__ . '/../schema/query-v1.json';
        $schema_json = file_get_contents($schema_path);
        if ($schema_json === false) {
            throw new QueriousException("Schema file not found: {$schema_path}");
        }
        $this->schema = Schema::fromJsonString($schema_json);
    }

    /**
     * example query from https://raw.githubusercontent.com/pendenga/querious_queries/master/query/user-data-v1.json
     * @throws Exception
     */
    public function testParameters()
    {
        $querious = new Querious();


        $querious->setQuery(new Query($this->query_json, $this->schema));
        $this->assertEquals(['username'], $querious->parameters());
    }

    /**
     * example query from https://raw.githubusercontent.com/pendenga/querious_queries/master/query/user-data-v1.json
     * @throws Exception
     */
    public function testValidateQueryData()
    {
        $querious = new Querious();
        $querious->setQuery(new Query($this->query_json, $this->schema));
        $stmt = $querious->execute($this->pdo, ['username' => Ini::get('username')]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(['user_id', 'username', 'email_address', 'full_name'], array_keys($row));
    }

}

