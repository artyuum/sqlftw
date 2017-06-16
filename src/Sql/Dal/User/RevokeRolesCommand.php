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

class RevokeRolesCommand implements \SqlFtw\Sql\Command
{
    use \Dogma\StrictBehaviorMixin;

    /** @var \SqlFtw\Sql\Names\UserName[] */
    private $roles;

    /** @var \SqlFtw\Sql\Names\UserName[] */
    private $users;

    /**
     * @param \SqlFtw\Sql\Names\UserName[] $roles
     * @param \SqlFtw\Sql\Names\UserName[] $users
     */
    public function __construct(array $roles, array $users)
    {
        $this->roles = $roles;
        $this->users = $users;
    }

    /**
     * @return \SqlFtw\Sql\Names\UserName[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return \SqlFtw\Sql\Names\UserName[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    public function serialize(SqlFormatter $formatter): string
    {
        return 'REVOKE ' . $formatter->formatSerializablesList($this->roles)
            . ' FROM ' . $formatter->formatSerializablesList($this->users);
    }

}
