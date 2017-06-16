<?php
/**
 * This file is part of the SqlFtw library (https://github.com/paranoiq/dogma)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Sql\Ddl\View;

use SqlFtw\Sql\Keyword;

class DropViewOption extends \SqlFtw\Sql\SqlEnum
{

    public const RESTRICT = Keyword::RESTRICT;
    public const CASCADE = Keyword::CASCADE;

}
