<?php declare(strict_types = 1);
/**
 * This file is part of the SqlFtw library (https://github.com/sqlftw)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Parser;

use Dogma\StrictBehaviorMixin;
use SqlFtw\Parser\Dml\QueryParser;
use SqlFtw\Sql\Dml\TableReference\EscapedTableReference;
use SqlFtw\Sql\Dml\TableReference\IndexHint;
use SqlFtw\Sql\Dml\TableReference\IndexHintAction;
use SqlFtw\Sql\Dml\TableReference\IndexHintTarget;
use SqlFtw\Sql\Dml\TableReference\InnerJoin;
use SqlFtw\Sql\Dml\TableReference\JoinSide;
use SqlFtw\Sql\Dml\TableReference\NaturalJoin;
use SqlFtw\Sql\Dml\TableReference\OuterJoin;
use SqlFtw\Sql\Dml\TableReference\StraightJoin;
use SqlFtw\Sql\Dml\TableReference\TableReferenceJsonTable;
use SqlFtw\Sql\Dml\TableReference\TableReferenceList;
use SqlFtw\Sql\Dml\TableReference\TableReferenceNode;
use SqlFtw\Sql\Dml\TableReference\TableReferenceParentheses;
use SqlFtw\Sql\Dml\TableReference\TableReferenceSubquery;
use SqlFtw\Sql\Dml\TableReference\TableReferenceTable;
use SqlFtw\Sql\Expression\ExpressionNode;
use SqlFtw\Sql\Expression\KeywordLiteral;
use SqlFtw\Sql\Keyword;
use SqlFtw\Sql\QualifiedName;
use function count;

class JoinParser
{
    use StrictBehaviorMixin;

    /** @var ExpressionParser */
    private $expressionParser;

    /** @var callable(): QueryParser */
    private $queryParserProxy;

    /**
     * @param callable(): QueryParser $queryParserProxy
     */
    public function __construct(ExpressionParser $expressionParser, callable $queryParserProxy)
    {
        $this->expressionParser = $expressionParser;
        $this->queryParserProxy = $queryParserProxy;
    }

    /**
     * table_references:
     *     escaped_table_reference [, escaped_table_reference] ...
     */
    public function parseTableReferences(TokenList $tokenList): TableReferenceNode
    {
        $references = [];
        do {
            $references[] = $this->parseTableReference($tokenList);
        } while ($tokenList->hasSymbol(','));

        if (count($references) === 1) {
            return $references[0];
        } else {
            return new TableReferenceList($references);
        }
    }

    /**
     * escaped_table_reference:
     *     table_reference
     *   | { OJ table_reference }
     */
    public function parseTableReference(TokenList $tokenList): TableReferenceNode
    {
        if ($tokenList->hasSymbol('{')) {
            $token = $tokenList->expectName();
            if ($token !== 'OJ') {
                $tokenList->missing('Expected ODBC escaped table reference introducer "OJ".');
            } else {
                $reference = $this->parseTableReference($tokenList);
                $tokenList->expectSymbol('}');

                return new EscapedTableReference($reference);
            }
        } else {
            return $this->parseTableReferenceInternal($tokenList);
        }
    }

    /**
     * table_reference:
     *     table_factor
     *   | join_table
     *
     * join_table:
     *     table_reference [INNER | CROSS] JOIN table_factor [join_condition]
     *   | table_reference STRAIGHT_JOIN table_factor
     *   | table_reference STRAIGHT_JOIN table_factor ON conditional_expr
     *   | table_reference {LEFT|RIGHT} [OUTER] JOIN table_reference join_condition
     *   | table_reference NATURAL [INNER | {LEFT|RIGHT} [OUTER]] JOIN table_factor
     *
     * join_condition:
     *     ON conditional_expr
     *   | USING (column_list)
     */
    private function parseTableReferenceInternal(TokenList $tokenList): TableReferenceNode
    {
        $left = $this->parseTableFactor($tokenList);

        do {
            if ($tokenList->hasKeyword(Keyword::STRAIGHT_JOIN)) {
                // STRAIGHT_JOIN
                $right = $this->parseTableFactor($tokenList);
                $condition = null;
                if ($tokenList->hasKeyword(Keyword::ON)) {
                    $condition = $this->expressionParser->parseExpression($tokenList);
                }

                $left = new StraightJoin($left, $right, $condition);
                continue;
            }
            if ($tokenList->hasKeyword(Keyword::NATURAL)) {
                // NATURAL JOIN
                $side = null;
                if (!$tokenList->hasKeyword(Keyword::INNER)) {
                    /** @var JoinSide|null $side */
                    $side = $tokenList->getKeywordEnum(JoinSide::class);
                    if ($side !== null) {
                        $tokenList->passKeyword(Keyword::OUTER);
                    }
                }
                $tokenList->expectKeyword(Keyword::JOIN);
                $right = $this->parseTableFactor($tokenList);

                $left = new NaturalJoin($left, $right, $side);
                continue;
            }
            /** @var JoinSide|null $side */
            $side = $tokenList->getKeywordEnum(JoinSide::class);
            if ($side !== null) {
                // {LEFT|RIGHT} [OUTER] JOIN
                $tokenList->passKeyword(Keyword::OUTER);
                $tokenList->expectKeyword(Keyword::JOIN);
                $right = $this->parseTableReferenceInternal($tokenList);
                [$on, $using] = $this->parseJoinCondition($tokenList);

                $left = new OuterJoin($left, $right, $side, $on, $using);
                continue;
            }
            $keyword = $tokenList->getAnyKeyword(Keyword::INNER, Keyword::CROSS, Keyword::JOIN);
            if ($keyword !== null) {
                // INNER JOIN
                $cross = false;
                if ($keyword === Keyword::INNER) {
                    $tokenList->expectKeyword(Keyword::JOIN);
                } elseif ($keyword === Keyword::CROSS) {
                    $tokenList->expectKeyword(Keyword::JOIN);
                    $cross = true;
                }
                $right = $this->parseTableFactor($tokenList);
                [$on, $using] = $this->parseJoinCondition($tokenList);

                $left = new InnerJoin($left, $right, $cross, $on, $using);
                continue;
            }

            return $left;
        } while (true);
    }

    /**
     * @return mixed[]|array{ExpressionNode, string[]}
     */
    private function parseJoinCondition(TokenList $tokenList): array
    {
        $on = $using = null;
        if ($tokenList->hasKeyword(Keyword::ON)) {
            $on = $this->expressionParser->parseExpression($tokenList);
        } elseif ($tokenList->hasKeyword(Keyword::USING)) {
            $tokenList->expectSymbol('(');
            $using = [];
            do {
                $using[] = $tokenList->expectName();
            } while ($tokenList->hasSymbol(','));
            $tokenList->expectSymbol(')');
        }

        return [$on, $using];
    }

    /**
     * table_factor:
     *     tbl_name [PARTITION (partition_names)] [[AS] alias] [index_hint_list]
     *   | [LATERAL] [(] table_subquery [)] [AS] alias [(col_list)]
     *   | ( table_references )
     */
    private function parseTableFactor(TokenList $tokenList): TableReferenceNode
    {
        $position = $tokenList->getPosition();

        if ($tokenList->hasKeyword(Keyword::JSON_TABLE)) {
            $tokenList->expectSymbol('(');
            $table = $this->expressionParser->parseJsonTable($tokenList->resetPosition($position));

            if ($tokenList->hasKeyword(Keyword::AS)) {
                $alias = $tokenList->expectName();
            } else {
                $alias = $tokenList->getName();
            }

            return new TableReferenceJsonTable($table, $alias);
        }

        // todo: QueryParser should be able to detect this better and resolve with ParenthesizedQueryExpression
        $selectInParentheses = false;
        if ($tokenList->hasSymbol('(')) {
            $selectInParentheses = $tokenList->hasAnyKeyword(Keyword::SELECT, Keyword::TABLE, Keyword::VALUES, Keyword::WITH);
            if (!$selectInParentheses) {
                $references = $this->parseTableReferences($tokenList);
                $tokenList->expectSymbol(')');

                return new TableReferenceParentheses($references);
            }
        }

        $keyword = $tokenList->getAnyKeyword(Keyword::SELECT, Keyword::TABLE, Keyword::VALUES, Keyword::WITH, Keyword::LATERAL);
        if ($selectInParentheses || $keyword !== null) {
            if ($keyword === Keyword::LATERAL) {
                if ($tokenList->hasSymbol('(')) {
                    $selectInParentheses = true;
                }
                $tokenList->expectAnyKeyword(Keyword::SELECT, Keyword::TABLE, Keyword::VALUES, Keyword::WITH);
            }

            $query = ($this->queryParserProxy)()->parseQuery($tokenList->resetPosition(-1));

            if ($selectInParentheses) {
                $tokenList->expectSymbol(')');
            }

            $tokenList->passKeyword(Keyword::AS);
            $alias = $tokenList->expectName();
            $columns = null;
            if ($tokenList->hasSymbol('(')) {
                $columns = [];
                do {
                    $columns[] = $tokenList->expectName();
                } while ($tokenList->hasSymbol(','));
                $tokenList->expectSymbol(')');
            }

            return new TableReferenceSubquery($query, $alias, $columns, $selectInParentheses, $keyword === Keyword::LATERAL);
        } else {
            // tbl_name [PARTITION (partition_names)] [[AS] alias] [index_hint_list]
            if ($tokenList->hasKeyword(Keyword::DUAL)) {
                $table = new QualifiedName(Keyword::DUAL);
            } else {
                $table = $tokenList->expectQualifiedName();
            }
            $partitions = null;
            if ($tokenList->hasKeyword(Keyword::PARTITION)) {
                $tokenList->expectSymbol('(');
                $partitions = [];
                do {
                    // phpcs:ignore SlevomatCodingStandard.Commenting.InlineDocCommentDeclaration.NoAssignment
                    /** @var non-empty-array<string> $partitions */
                    $partitions[] = $tokenList->expectName();
                } while ($tokenList->hasSymbol(','));
                $tokenList->expectSymbol(')');
            }
            if ($tokenList->hasKeyword(Keyword::AS)) {
                $alias = $tokenList->expectName();
            } else {
                $alias = $tokenList->getNonKeywordName();
            }
            $indexHints = null;
            if ($tokenList->hasAnyKeyword(Keyword::USE, Keyword::IGNORE, Keyword::FORCE)) {
                $indexHints = $this->parseIndexHints($tokenList->resetPosition(-1));
            }

            return new TableReferenceTable($table, $alias, $partitions, $indexHints);
        }
    }

    /**
     * index_hint_list:
     *     index_hint [, index_hint] ...
     *
     * index_hint:
     *     USE {INDEX|KEY} [FOR {JOIN|ORDER BY|GROUP BY}] ([index_list])
     *   | IGNORE {INDEX|KEY} [FOR {JOIN|ORDER BY|GROUP BY}] (index_list)
     *   | FORCE {INDEX|KEY} [FOR {JOIN|ORDER BY|GROUP BY}] (index_list)
     *
     * index_list:
     *     index_name [, index_name] ...
     *
     * @return non-empty-array<IndexHint>
     */
    private function parseIndexHints(TokenList $tokenList): array
    {
        $hints = [];
        do {
            /** @var IndexHintAction $action */
            $action = $tokenList->getKeywordEnum(IndexHintAction::class);
            $tokenList->getAnyKeyword(Keyword::INDEX, Keyword::KEY);
            $target = null;
            if ($tokenList->hasKeyword(Keyword::FOR)) {
                $target = $tokenList->expectMultiKeywordsEnum(IndexHintTarget::class);
            }

            $tokenList->expectSymbol('(');
            $indexes = [];
            do {
                if ($tokenList->hasKeyword(Keyword::PRIMARY)) {
                    $indexes[] = new KeywordLiteral(Keyword::PRIMARY);
                } else {
                    $indexes[] = $tokenList->expectName();
                }
            } while ($tokenList->hasSymbol(','));
            $tokenList->expectSymbol(')');

            $hints[] = new IndexHint($action, $target, $indexes);
        } while ($tokenList->hasSymbol(','));

        return $hints;
    }

}
