<?php declare(strict_types = 1);

namespace SqlFtw\Tests\Analyzer\Context;

use PDO;
use ReflectionClass;
use SqlFtw\Analyzer\Context\MysqlContextProvider;
use SqlFtw\Connection\PdoConnectionFactory;
use SqlFtw\Sql\Expression\SimpleName;
use SqlFtw\Sql\UserName;
use SqlFtw\Tests\Assert;
use function rd;

require __DIR__ . '/../../bootstrap.php';

$schema = 'sqlftw_mysql_context_provider_test';

// todo: load from config
$my = PdoConnectionFactory::mysql('localhost', 38032, 'root', 'root');
$myProvider = new MysqlContextProvider($my);


getSchemaDefinition:
$my->execute("DROP SCHEMA IF EXISTS {$schema}");
Assert::null($myProvider->getSchemaDefinition($schema));

$my->execute("CREATE SCHEMA {$schema} COLLATE utf8mb4_0900_ai_ci");
Assert::same(
    $myProvider->getSchemaDefinition($schema),
    "CREATE DATABASE `{$schema}` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */"
);


$my->query("USE {$schema}");

getTableDefinition:
Assert::null($myProvider->getTableDefinition(new SimpleName('foo')));

$my->execute("CREATE TABLE tbl1 (col1 int)");
Assert::same(
    $myProvider->getTableDefinition(new SimpleName('tbl1')),
    "CREATE TABLE `tbl1` (\n  `col1` int DEFAULT NULL\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci"
);


getViewDefinition:
Assert::null($myProvider->getViewDefinition(new SimpleName('view1')));

$my->execute("CREATE VIEW view1 AS SELECT * FROM tbl1");
Assert::same(
    $myProvider->getViewDefinition(new SimpleName('view1')),
    "CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `view1` AS select `tbl1`.`col1` AS `col1` from `tbl1`"
);


getEventDefinition:
Assert::null($myProvider->getEventDefinition(new SimpleName('evt1')));

$my->execute("CREATE EVENT evt1 ON SCHEDULE EVERY 1 HOUR STARTS '2023-04-02 00:00:00' DO CALL foo()");
Assert::same(
    $myProvider->getEventDefinition(new SimpleName('evt1')),
    "CREATE DEFINER=`root`@`%` EVENT `evt1` ON SCHEDULE EVERY 1 HOUR STARTS '2023-04-02 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO CALL foo()"
);


getFunctionDefinition:
Assert::null($myProvider->getFunctionDefinition(new SimpleName('fun1')));

$my->execute("CREATE FUNCTION fun1() RETURNS INT DETERMINISTIC RETURN 1");
Assert::same(
    $myProvider->getFunctionDefinition(new SimpleName('fun1')),
    "CREATE DEFINER=`root`@`%` FUNCTION `fun1`() RETURNS int\n    DETERMINISTIC\nRETURN 1"
);


getProcedureDefinition:
Assert::null($myProvider->getProcedureDefinition(new SimpleName('proc1')));

$my->execute("CREATE PROCEDURE proc1() SELECT 1");
Assert::same(
    $myProvider->getProcedureDefinition(new SimpleName('proc1')),
    "CREATE DEFINER=`root`@`%` PROCEDURE `proc1`()\nSELECT 1"
);


getTriggerDefinition:
rd($myProvider->getTriggerDefinition(new SimpleName('foo')));


getUserDefinition:
rd($myProvider->getUserDefinition(new UserName('foo', null)));


rd(new ReflectionClass(PDO::class));


