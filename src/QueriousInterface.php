<?php

namespace Pendenga\Querious;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Interface QueriousInterface
 * @package Pendenga\Querious
 */
interface QueriousInterface
{
    /**
     * @param array $parameters
     * @return PDOStatement
     * @throws PDOException
     * @throws QueriousException
     */
    public function execute(PDO $pdo, array $parameters): PDOStatement;

    // /**
    //  * @param string $name
    //  * @return Querious
    //  * @throws QueriousException
    //  */
    // public function load(string $name): Querious;

    /**
     * Get the list of parameters required by the query definition
     * @return array
     */
    public function parameters(): array;

    /**
     * @param Query $query
     * @return Querious
     */
    public function setQuery(Query $query):Querious;
}
