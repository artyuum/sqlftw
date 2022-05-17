<?php declare(strict_types = 1);

namespace SqlFtw\Parser;

use SqlFtw\Platform\Platform;
use SqlFtw\Platform\PlatformSettings;
use SqlFtw\Parser\TokenType as T;
use SqlFtw\Tests\Assert;

require '../bootstrap.php';

$settings = new PlatformSettings(Platform::get(Platform::MYSQL, '5.7'));
$lexer = new Lexer($settings, true, true);

// NUMBER
$tokens = $lexer->tokenizeAll(' 1 ');
Assert::count($tokens, 3);
Assert::token($tokens[0], T::WHITESPACE, ' ', 0);
Assert::token($tokens[1], T::VALUE | T::NUMBER | T::INT | T::UINT, 1, 1);
Assert::token($tokens[2], T::WHITESPACE, ' ', 2);

$tokens = $lexer->tokenizeAll(' 123 ');
Assert::count($tokens, 3);
Assert::token($tokens[0], T::WHITESPACE, ' ', 0);
Assert::token($tokens[1], T::VALUE | T::NUMBER | T::INT | T::UINT, 123, 1);
Assert::token($tokens[2], T::WHITESPACE, ' ', 4);

$tokens = $lexer->tokenizeAll(' +123 ');
Assert::count($tokens, 3);
Assert::token($tokens[0], T::WHITESPACE, ' ', 0);
Assert::token($tokens[1], T::VALUE | T::NUMBER | T::INT, 123, 1);
Assert::token($tokens[2], T::WHITESPACE, ' ', 5);

$tokens = $lexer->tokenizeAll(' -123 ');
Assert::count($tokens, 3);
Assert::token($tokens[0], T::WHITESPACE, ' ', 0);
Assert::token($tokens[1], T::VALUE | T::NUMBER | T::INT, -123, 1);
Assert::token($tokens[2], T::WHITESPACE, ' ', 5);

$tokens = $lexer->tokenizeAll(' --123 ');
Assert::count($tokens, 3);
Assert::token($tokens[0], T::WHITESPACE, ' ', 0);
Assert::token($tokens[1], T::VALUE | T::NUMBER | T::INT, 123, 1);
Assert::token($tokens[2], T::WHITESPACE, ' ', 6);

$tokens = $lexer->tokenizeAll(' ---123 ');
Assert::count($tokens, 3);
Assert::token($tokens[0], T::WHITESPACE, ' ', 0);
Assert::token($tokens[1], T::VALUE | T::NUMBER | T::INT, -123, 1);
Assert::token($tokens[2], T::WHITESPACE, ' ', 7);

$tokens = $lexer->tokenizeAll(' ----123 ');
Assert::count($tokens, 3);
Assert::token($tokens[0], T::WHITESPACE, ' ', 0);
Assert::token($tokens[1], T::VALUE | T::NUMBER | T::INT, 123, 1);
Assert::token($tokens[2], T::WHITESPACE, ' ', 8);

$tokens = $lexer->tokenizeAll(' 123.456 ');
Assert::count($tokens, 3);
Assert::token($tokens[0], T::WHITESPACE, ' ', 0);
Assert::token($tokens[1], T::VALUE | T::NUMBER, 123.456, 1);
Assert::token($tokens[2], T::WHITESPACE, ' ', 8);

$tokens = $lexer->tokenizeAll(' 123. ');
Assert::count($tokens, 3);
Assert::token($tokens[0], T::WHITESPACE, ' ', 0);
Assert::token($tokens[1], T::VALUE | T::NUMBER, 123.0, 1);
Assert::token($tokens[2], T::WHITESPACE, ' ', 5);

$tokens = $lexer->tokenizeAll(' .456 ');
Assert::count($tokens, 3);
Assert::token($tokens[0], T::WHITESPACE, ' ', 0);
Assert::token($tokens[1], T::VALUE | T::NUMBER, 0.456, 1);
Assert::token($tokens[2], T::WHITESPACE, ' ', 5);

$tokens = $lexer->tokenizeAll(' 1.23e4 ');
Assert::count($tokens, 3);
Assert::token($tokens[0], T::WHITESPACE, ' ', 0);
Assert::token($tokens[1], T::VALUE | T::NUMBER, '1.23e4', 1);
Assert::token($tokens[2], T::WHITESPACE, ' ', 7);

$tokens = $lexer->tokenizeAll(' 1.23E4 ');
Assert::count($tokens, 3);
Assert::token($tokens[0], T::WHITESPACE, ' ', 0);
Assert::token($tokens[1], T::VALUE | T::NUMBER, '1.23e4', 1);
Assert::token($tokens[2], T::WHITESPACE, ' ', 7);

$tokens = $lexer->tokenizeAll(' 1.23e+4 ');
Assert::count($tokens, 3);
Assert::token($tokens[0], T::WHITESPACE, ' ', 0);
Assert::token($tokens[1], T::VALUE | T::NUMBER, '1.23e4', 1);
Assert::token($tokens[2], T::WHITESPACE, ' ', 8);

$tokens = $lexer->tokenizeAll(' 1.23e-4 ');
Assert::count($tokens, 3);
Assert::token($tokens[0], T::WHITESPACE, ' ', 0);
Assert::token($tokens[1], T::VALUE | T::NUMBER, '1.23e-4', 1);
Assert::token($tokens[2], T::WHITESPACE, ' ', 8);

$tokens = $lexer->tokenizeAll(' 123.e4 ');
Assert::count($tokens, 3);
Assert::token($tokens[0], T::WHITESPACE, ' ', 0);
Assert::token($tokens[1], T::VALUE | T::NUMBER, '123.0e4', 1);
Assert::token($tokens[2], T::WHITESPACE, ' ', 7);

$tokens = $lexer->tokenizeAll(' 1.23e');
Assert::invalidToken($tokens[1], T::VALUE | T::NUMBER | T::INVALID, '~^Invalid number exponent~', 1);

$tokens = $lexer->tokenizeAll(' 1.23e+');
Assert::invalidToken($tokens[1], T::VALUE | T::NUMBER | T::INVALID, '~^Invalid number exponent~', 1);

$tokens = $lexer->tokenizeAll(' 1.23e-');
Assert::invalidToken($tokens[1], T::VALUE | T::NUMBER | T::INVALID, '~^Invalid number exponent~', 1);

$tokens = $lexer->tokenizeAll(' 1.23ef');
Assert::invalidToken($tokens[1], T::VALUE | T::NUMBER | T::INVALID, '~^Invalid number exponent~', 1);

$tokens = $lexer->tokenizeAll(' 1.23e+f');
Assert::invalidToken($tokens[1], T::VALUE | T::NUMBER | T::INVALID, '~^Invalid number exponent~', 1);

$tokens = $lexer->tokenizeAll(' 1.23e-f');
Assert::invalidToken($tokens[1], T::VALUE | T::NUMBER | T::INVALID, '~^Invalid number exponent~', 1);
