<?php
/**
 * This file is part of the SqlFtw library (https://github.com/sqlftw)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Sql\Dal\User;

use SqlFtw\SqlFormatter\SqlFormatter;

class UserPrivilege implements \SqlFtw\Sql\SqlSerializable
{
    use \Dogma\StrictBehaviorMixin;

    /** @var \SqlFtw\Sql\Dal\User\UserPrivilegeType */
    private $type;

    /** @var string[]|null */
    private $columns;

    /**
     * @param \SqlFtw\Sql\Dal\User\UserPrivilegeType $type
     * @param string[]|null $columns
     */
    public function __construct(UserPrivilegeType $type, ?array $columns)
    {
        $this->type = $type;
        $this->columns = $columns;
    }

    public function getType(): UserPrivilegeType
    {
        return $this->type;
    }

    /**
     * @return string[]|null
     */
    public function getColumns(): ?array
    {
        return $this->columns;
    }

    public function serialize(SqlFormatter $formatter): string
    {
        $result = $this->type->serialize($formatter);
        if ($this->columns !== null) {
            $result .= ' (' . $formatter->formatNamesList($this->columns) . ')';
        }

        return $result;
    }

}
