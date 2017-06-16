<?php
/**
 * This file is part of the SqlFtw library (https://github.com/sqlftw)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Sql\Dml\Update;

use Dogma\Check;
use SqlFtw\Sql\Dml\OrderByExpression;
use SqlFtw\Sql\Dml\TableReference;
use SqlFtw\Sql\Expression\ExpressionNode;
use SqlFtw\SqlFormatter\SqlFormatter;

class UpdateCommand implements \SqlFtw\Sql\Command
{
    use \Dogma\StrictBehaviorMixin;

    /** @var \SqlFtw\Sql\Dml\TableReference[] */
    private $tableReferences;

    /** @var \SqlFtw\Sql\Dml\Update\SetColumnExpression[] */
    private $values;

    /** @var \SqlFtw\Sql\Expression\ExpressionNode|null */
    private $where;

    /** @var \SqlFtw\Sql\Dml\OrderByExpression[]|null */
    private $orderBy;

    /** @var int|null */
    private $limit;

    /** @var bool */
    private $ignore;

    /** @var bool */
    private $lowPriority;

    /**
     * @param \SqlFtw\Sql\Dml\TableReference[] $tableReferences
     * @param \SqlFtw\Sql\Dml\Update\SetColumnExpression[] $values
     * @param \SqlFtw\Sql\Expression\ExpressionNode|null $where
     * @param \SqlFtw\Sql\Dml\OrderByExpression[]|null $orderBy
     * @param int|null $limit
     * @param bool $ignore
     * @param bool $lowPriority
     */
    public function __construct(
        array $tableReferences,
        array $values,
        ?ExpressionNode $where = null,
        ?array $orderBy = null,
        ?int $limit = null,
        bool $ignore = false,
        bool $lowPriority = false
    ) {
        Check::itemsOfType($tableReferences, TableReference::class);
        Check::itemsOfType($values, SetColumnExpression::class);
        if ($orderBy !== null) {
            Check::itemsOfType($orderBy, OrderByExpression::class);
        }
        if (count($tableReferences) > 1 && ($orderBy !== null || $limit !== null)) {
            throw new \SqlFtw\Sql\InvalidDefinitionException('ORDER BY and LIMIT must not be set, when more table references are used.');
        }

        $this->tableReferences = $tableReferences;
        $this->values = $values;
        $this->where = $where;
        $this->orderBy = $orderBy;
        $this->limit = $limit;
        $this->ignore = $ignore;
        $this->lowPriority = $lowPriority;
    }

    /**
     * @return \SqlFtw\Sql\Dml\TableReference[]
     */
    public function getTableReferences(): array
    {
        return $this->tableReferences;
    }

    /**
     * @return \SqlFtw\Sql\Dml\Update\SetColumnExpression[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    public function getWhere(): ?ExpressionNode
    {
        return $this->where;
    }

    /**
     * @return \SqlFtw\Sql\Dml\OrderByExpression[]|null
     */
    public function getOrderBy(): ?array
    {
        return $this->orderBy;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function ignore(): bool
    {
        return $this->ignore;
    }

    public function lowPriority(): bool
    {
        return $this->lowPriority;
    }

    public function serialize(SqlFormatter $formatter): string
    {
        $result = 'UPDATE ';
        if ($this->lowPriority) {
            $result .= 'LOW_PRIORITY ';
        }
        if ($this->ignore) {
            $result .= 'IGNORE ';
        }

        $result .= $formatter->formatSerializablesList($this->tableReferences);
        $result .= ' SET ' . $formatter->formatSerializablesList($this->values);

        if ($this->where !== null) {
            $result .= ' WHERE ' . $this->where->serialize($formatter);
        }
        if ($this->orderBy !== null) {
            $result .= ' ORDER BY ' . $formatter->formatSerializablesList($this->orderBy);
        }
        if ($this->limit !== null) {
            $result .= ' LIMIT ' . $this->limit;
        }

        return $result;
    }

}
