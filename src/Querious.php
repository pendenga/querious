<?php

namespace Pendenga\Querious;

use Opis\JsonSchema\Schema;
use Opis\JsonSchema\Validator;
use PDO;
use PDOStatement;

/**
 * Class Querious
 * @package Pendenga\Querious
 */
class Querious implements QueriousInterface
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @inheritDoc
     */
    public function setQuery(Query $query): Querious
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parameters(): array
    {
        return $this->query->parameters();
    }

    /**
     * @inheritDoc
     */
    public function execute(PDO $pdo, array $parameters): PDOStatement
    {
        if (!isset($this->query)) {
            throw new QueriousException("Load a valid query first");
        }
        $this->query->validateParameters($parameters);

        // $pdo = $this->config->getPDO();
        switch ($this->query->command()) {
            case 'prepare':
                $stmt = $pdo->prepare($this->query->statement());
                $stmt->execute($parameters);

                return $stmt;
                break;
            case 'query':
                $stmt = $pdo->query($this->query->statement());

                return $stmt;
                break;
            default:
                throw new QueriousException('Invalid command: ' . $this->query->command());
        }
    }
}
