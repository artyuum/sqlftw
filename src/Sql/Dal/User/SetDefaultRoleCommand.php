<?php
/**
 * This file is part of the SqlFtw library (https://github.com/sqlftw)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Sql\Dal\User;

use Dogma\Check;
use SqlFtw\Sql\Names\UserName;
use SqlFtw\SqlFormatter\SqlFormatter;

class SetDefaultRoleCommand implements \SqlFtw\Sql\Command
{
    use \Dogma\StrictBehaviorMixin;

    /** @var \SqlFtw\Sql\Names\UserName[] */
    private $users;

    /** @var \SqlFtw\Sql\Dal\User\UserDefaultRolesSpecification|null */
    private $roles;

    /** @var \SqlFtw\Sql\Names\UserName[]|null */
    private $rolesList;

    /**
     * @param \SqlFtw\Sql\Names\UserName[] $users
     * @param \SqlFtw\Sql\Dal\User\UserDefaultRolesSpecification|null $roles
     * @param \SqlFtw\Sql\Names\UserName[]|null $rolesList
     */
    public function __construct(array $users, ?UserDefaultRolesSpecification $roles, ?array $rolesList = null)
    {
        Check::array($users, 1);
        Check::itemsOfType($users, UserName::class);
        if ($rolesList !== null) {
            Check::array($rolesList, 1);
            Check::itemsOfType($rolesList, UserName::class);
        }

        $this->users = $users;
        $this->roles = $roles;
        $this->rolesList = $rolesList;
    }

    /**
     * @return \SqlFtw\Sql\Names\UserName[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    public function getRoles(): ?UserDefaultRolesSpecification
    {
        return $this->roles;
    }

    /**
     * @return \SqlFtw\Sql\Names\UserName[]
     */
    public function getRolesList(): array
    {
        return $this->rolesList;
    }

    public function serialize(SqlFormatter $formatter): string
    {
        $result = 'SET DEFAULT ROLE ';
        if ($this->roles !== null) {
            $result .= $this->roles->serialize($formatter);
        }
        if ($this->rolesList !== null) {
            $result .= $formatter->formatSerializablesList($this->rolesList);
        }
        $result .= ' TO ' . $formatter->formatSerializablesList($this->users);

        return $result;
    }

}
