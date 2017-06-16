<?php
/**
 * This file is part of the SqlFtw library (https://github.com/sqlftw)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Sql\Dml\Select;

use SqlFtw\Sql\Expression\ExpressionNode;
use SqlFtw\Sql\Order;
use SqlFtw\SqlFormatter\SqlFormatter;

class GroupByExpression implements \SqlFtw\Sql\SqlSerializable
{
    use \Dogma\StrictBehaviorMixin;

    /** @var \SqlFtw\Sql\Expression\ExpressionNode */
    private $expression;

    /** @var \SqlFtw\Sql\Order|null */
    private $order;

    public function __construct(ExpressionNode $expression, ?Order $order = null)
    {
        $this->expression = $expression;
        $this->order = $order;
    }

    public function getExpression(): ExpressionNode
    {
        return $this->expression;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function serialize(SqlFormatter $formatter): string
    {
        $result = $this->expression->serialize($formatter);
        if ($this->order !== null) {
            $result .= ' ' . $this->order->serialize($formatter);
        }

        return $result;
    }

}
