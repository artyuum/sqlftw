<?php declare(strict_types = 1);

namespace SqlFtw\Tests\Analyzer\Context;

use SqlFtw\Analyzer\Context\PostgreContextProvider;
use SqlFtw\Connection\PdoConnectionFactory;
use SqlFtw\Tests\Assert;
use function rd;

require __DIR__ . '/../../bootstrap.php';

$schema = 'sqlftw_postgre_context_provider_test';

$pg = PdoConnectionFactory::postgre('localhost', 51502, 'postgres', 'root', 'postgres');
$pgProvider = new PostgreContextProvider($pg);

$pg->query("DROP SCHEMA IF EXISTS {$schema}");
Assert::null($pgProvider->getSchemaDefinition($schema));


getSchemaDefinition:
$pg->query("CREATE SCHEMA {$schema}");
Assert::same(
    $pgProvider->getSchemaDefinition($schema),
    "CREATE SCHEMA \"{$schema}\" AUTHORIZATION \"postgres\""
);

rd($pg->query("SELECT array[1,2,33333333333333333333] foo")->all());

$pg->query("SELECT %i:foo FROM foo");