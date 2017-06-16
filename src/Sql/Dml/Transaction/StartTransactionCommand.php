<?php
/**
 * This file is part of the SqlFtw library (https://github.com/sqlftw)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Sql\Dml\Transaction;

use SqlFtw\SqlFormatter\SqlFormatter;

class StartTransactionCommand implements \SqlFtw\Sql\Command
{
    use \Dogma\StrictBehaviorMixin;

    /** @var bool|null */
    private $consistent;

    /** @var bool|null */
    private $write;

    public function __construct(?bool $consistent = null, ?bool $write = null)
    {
        $this->consistent = $consistent;
        $this->write = $write;
    }

    public function getConsistent(): ?bool
    {
        return $this->consistent;
    }

    public function getWrite(): ?bool
    {
        return $this->write;
    }

    public function serialize(SqlFormatter $formatter): string
    {
        return 'START TRANSACTION';
    }

}
