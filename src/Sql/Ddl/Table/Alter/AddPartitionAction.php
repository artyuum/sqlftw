<?php
/**
 * This file is part of the SqlFtw library (https://github.com/sqlftw)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Sql\Ddl\Table\Alter;

use SqlFtw\Sql\Ddl\Table\Partition\PartitionDefinition;
use SqlFtw\SqlFormatter\SqlFormatter;

class AddPartitionAction implements \SqlFtw\Sql\Ddl\Table\Alter\AlterTableAction
{
    use \Dogma\StrictBehaviorMixin;

    /** @var \SqlFtw\Sql\Ddl\Table\Partition\PartitionDefinition */
    private $partition;

    public function __construct(PartitionDefinition $partition)
    {
        $this->partition = $partition;
    }

    public function getType(): AlterTableActionType
    {
        return AlterTableActionType::get(AlterTableActionType::ADD_PARTITION);
    }

    public function getPartition(): PartitionDefinition
    {
        return $this->partition;
    }

    public function serialize(SqlFormatter $formatter): string
    {
        return 'ADD ' . $this->partition->serialize($formatter);
    }

}
