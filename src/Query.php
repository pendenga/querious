<?php

namespace Pendenga\Querious;

use Opis\JsonSchema\Schema;
use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Validator;

/**
 * Class Query
 * @package Pendenga\Querious
 */
class Query
{
    /**
     * @var string
     */
    protected $command, $statement;

    /**
     * @var array
     */
    protected $authors, $parameters;

    /**
     * @return array
     */
    public function authors(): array
    {
        return $this->authors;
    }

    /**
     * @return string
     */
    public function command(): string
    {
        return $this->command;
    }

    /**
     * @return array
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function statement(): string
    {
        return $this->statement;
    }

    /**
     * Validate that given parameters match the ones from the schema
     * @param array $parameters
     * @throws QueriousException
     */
    public function validateParameters(array $parameters)
    {
        if ($this->parameters() != array_keys($parameters)) {
            throw new QueriousException('Invalid query parameters. Expected ' . implode(', ', $this->parameters()));
        }
    }

    /**
     * @param ValidationError $error
     * @return string
     */
    protected function formatError(ValidationError $error): string
    {
        $args = $error->keywordArgs();
        switch ($error->keyword()) {
            case 'required':
                return 'Schema validation error - required: ' . $args['missing'];
                break;
            case 'type':
                return sprintf('Schema validation error - %s expected, %s used', $args['expected'], $args['used']);
                break;
            default:
                return 'Schema validation error - ' . $error->keyword();
        }
    }

    /**
     * @param string $query_json
     * @param Schema $schema
     * @throws QueriousException
     */
    public function validateSchema(string $query_json, Schema $schema)
    {
        // load from json (not array)
        $query_definition = json_decode($query_json);

        // test if valid against schema
        $validator = new Validator();
        $result = $validator->schemaValidation($query_definition, $schema);

        if ($result->hasErrors()) {
            throw new QueriousException($this->formatError($result->getFirstError()));
        }

        // as array this time
        $this->data = json_decode($query_json, true);

        // set local data
        $this->authors = $query_definition->authors;
        $this->command = $query_definition->command;
        $this->statement = $query_definition->statement;
        $this->parameters = $query_definition->parameters;
    }

    /**
     * @param string $query_json
     * @param Schema $schema
     * @throws QueriousException
     */
    public function __construct(string $query_json, Schema $schema)
    {
        $this->validateSchema($query_json, $schema);
    }
}
