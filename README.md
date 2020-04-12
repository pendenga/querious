# Querious
Tool to save SQL queries in an approved Git Repository and load them into your app as data sets, just as you would 
PDO queries. This will enable you to add approved queries by name without changes to your application.

I guess it's kind of like having stored procedures. 

The idea is... if your build pipeline requires you to run everything through all kinds of QA and testing, it may be
to add queries as a configuration change rather than a code change. This can be useful for saved filters on known 
datasets, custom exports for business operations, anywhere you might have a need to define a custom query that doesn't 
necessarily need to reside in your application code, or follow the same build pipeline as your application.

I realize this could be very dangerous to just inject SQL like this, blindly, into your application. That is why I 
decided to use a Git repository as the storage mechanism instead of just a table in a database. 
Any repo you connect to this should have an adequate security policy and a process for review of each query before 
it is pushed to production. Once it is in production, Querious will be able to fetch it by name.  

# Install #
Installation is simple with [Composer](https://getcomposer.org/).
 
```shell script
composer require pendenga/querious
``` 
 
# Usage #
Example:
```php
<?php

use PDOStatement;
use Pendenga\Querious;

$querious = new Querious();
$querious->setQuery(
    new Query($query_definition, $query_schema)
);

/** @var PDOStatement $stmt */
$stmt = $querious->execute(['username' => 'pendenga']);
forech ($stmt->fetchRow(PDO::FETCH_ASSOC) as $rs) {
    print_r($rs);
}
```
