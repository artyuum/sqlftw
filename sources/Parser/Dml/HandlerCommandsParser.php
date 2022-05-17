<?php declare(strict_types = 1);
/**
 * This file is part of the SqlFtw library (https://github.com/sqlftw)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Parser\Dml;

use Dogma\StrictBehaviorMixin;
use SqlFtw\Parser\ExpressionParser;
use SqlFtw\Parser\TokenList;
use SqlFtw\Sql\Dml\Handler\HandlerCloseCommand;
use SqlFtw\Sql\Dml\Handler\HandlerOpenCommand;
use SqlFtw\Sql\Dml\Handler\HandlerReadCommand;
use SqlFtw\Sql\Dml\Handler\HandlerReadTarget;
use SqlFtw\Sql\Keyword;

class HandlerCommandsParser
{
    use StrictBehaviorMixin;

    /** @var ExpressionParser */
    private $expressionParser;

    public function __construct(ExpressionParser $expressionParser)
    {
        $this->expressionParser = $expressionParser;
    }

    /**
     * HANDLER tbl_name OPEN [[AS] alias]
     */
    public function parseHandlerOpen(TokenList $tokenList): HandlerOpenCommand
    {
        $tokenList->expectKeyword(Keyword::HANDLER);
        $table = $tokenList->expectQualifiedName();
        $tokenList->expectKeyword(Keyword::OPEN);

        $tokenList->passKeyword(Keyword::AS);
        $alias = $tokenList->getName();

        return new HandlerOpenCommand($table, $alias);
    }

    /**
     * HANDLER tbl_name READ index_name { = | <= | >= | < | > } (value1,value2, ...)
     *     [ WHERE where_condition ] [LIMIT ... ]
     * HANDLER tbl_name READ index_name { FIRST | NEXT | PREV | LAST }
     *     [ WHERE where_condition ] [LIMIT ... ]
     * HANDLER tbl_name READ { FIRST | NEXT }
     *     [ WHERE where_condition ] [LIMIT ... ]
     */
    public function parseHandlerRead(TokenList $tokenList): HandlerReadCommand
    {
        $tokenList->expectKeyword(Keyword::HANDLER);
        $table = $tokenList->expectQualifiedName();
        $tokenList->expectKeyword(Keyword::READ);

        $values = null;
        $index = $tokenList->getName();
        if ($index === Keyword::FIRST || $index === Keyword::NEXT) {
            $index = null;
            $tokenList->resetPosition(-1);
        }
        if ($index !== null) {
            $what = $tokenList->getAnyKeyword(...HandlerReadTarget::getKeywords());
            if ($what === null) {
                $what = $tokenList->expectAnyOperator(...HandlerReadTarget::getOperators());
                $values = [];
                $tokenList->expectSymbol('(');
                do {
                    $values[] = $this->expressionParser->parseLiteralValue($tokenList);
                } while ($tokenList->hasSymbol(','));
                $tokenList->expectSymbol(')');
            }
            $what = HandlerReadTarget::get($what);
        } else {
            /** @var HandlerReadTarget $what */
            $what = $tokenList->expectKeywordEnum(HandlerReadTarget::class);
        }

        $where = $limit = $offset = null;
        if ($tokenList->hasKeyword(Keyword::WHERE)) {
            $where = $this->expressionParser->parseExpression($tokenList);
        }
        if ($tokenList->hasKeyword(Keyword::LIMIT)) {
            [$limit, $offset] = $this->expressionParser->parseLimitAndOffset($tokenList);
        }

        return new HandlerReadCommand($table, $what, $index, $values, $where, $limit, $offset);
    }

    /**
     * HANDLER tbl_name CLOSE
     */
    public function parseHandlerClose(TokenList $tokenList): HandlerCloseCommand
    {
        $tokenList->expectKeyword(Keyword::HANDLER);
        $table = $tokenList->expectQualifiedName();
        $tokenList->expectKeyword(Keyword::CLOSE);

        return new HandlerCloseCommand($table);
    }

}
