<?php
/**
 * This file is part of the SqlFtw library (https://github.com/sqlftw)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Sql\Dal\Show;

use SqlFtw\SqlFormatter\SqlFormatter;

class ShowSlaveStatusCommand extends \SqlFtw\Sql\Dal\Show\ShowCommand
{

    /** @var string|null */
    private $channel;

    public function __construct(?string $channel)
    {
        $this->channel = $channel;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function serialize(SqlFormatter $formatter): string
    {
        return 'SHOW SLAVE STATUS' . ($this->channel ? ' FOR ' . $formatter->formatName($this->channel) : '');
    }

}
