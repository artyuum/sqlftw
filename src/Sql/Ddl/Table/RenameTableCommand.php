<?php
/**
 * This file is part of the SqlFtw library (https://github.com/sqlftw)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Sql\Ddl\Table;

use Dogma\Check;
use SqlFtw\Sql\Names\TableName;
use SqlFtw\SqlFormatter\SqlFormatter;

class RenameTableCommand implements \SqlFtw\Sql\TablesCommand
{
    use \Dogma\StrictBehaviorMixin;

    /** @var \SqlFtw\Sql\Names\TableName[] */
    protected $tables;

    /** @var \SqlFtw\Sql\Names\TableName[] */
    private $newTables;

    /**
     * @param \SqlFtw\Sql\Names\TableName[] $tables
     * @param \SqlFtw\Sql\Names\TableName[] $newTables
     */
    public function __construct(array $tables, array $newTables)
    {
        Check::array($tables, 1);
        Check::itemsOfType($tables, TableName::class);
        Check::array($newTables, 1);
        Check::itemsOfType($newTables, TableName::class);
        if (count($tables) !== count($newTables)) {
            throw new \SqlFtw\Sql\InvalidDefinitionException('Count of old table names and new table names do not match.');
        }

        $this->tables = array_values($tables);
        $this->newTables = array_values($newTables);
    }

    /**
     * @return \SqlFtw\Sql\Names\TableName[]
     */
    public function getTables(): array
    {
        return $this->tables;
    }

    /**
     * @return \SqlFtw\Sql\Names\TableName[]
     */
    public function getNewTables(): array
    {
        return $this->newTables;
    }

    public function getIterator(): \IteratorAggregate
    {
        /// zip iterator
    }

    public function serialize(SqlFormatter $formatter): string
    {
        $result = 'RENAME TABLE';
        foreach ($this->tables as $i => $table) {
            $result .= ' ' . $table->serialize($formatter) . ' TO ' . $this->newTables[$i]->serialize($formatter) . ',';
        }

        return rtrim($result, ',');
    }

}
