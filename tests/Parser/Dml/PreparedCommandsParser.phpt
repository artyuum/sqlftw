<?php declare(strict_types = 1);

namespace SqlFtw\Parser;

use SqlFtw\Tests\Assert;

require __DIR__ . '/../../bootstrap.php';


// {DEALLOCATE | DROP} PREPARE stmt_name
Assert::parse("DEALLOCATE PREPARE stm1");
Assert::parse("DROP PREPARE stm1", "DEALLOCATE PREPARE stm1"); // DROP -> DEALLOCATE


// EXECUTE stmt_name [USING @var_name [, @var_name] ...]
Assert::parse("EXECUTE stm1");
Assert::parse("EXECUTE stm1 USING @var1");
Assert::parse("EXECUTE stm1 USING @var1, @var2");


// PREPARE stmt_name FROM preparable_stmt
Assert::parse("PREPARE stm1 FROM 'SELECT 1'");
Assert::parse("PREPARE stm1 FROM @var1");
