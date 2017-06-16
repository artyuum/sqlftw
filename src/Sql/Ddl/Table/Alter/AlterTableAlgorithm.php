<?php
/**
 * This file is part of the SqlFtw library (https://github.com/sqlftw)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Sql\Ddl\Table\Alter;

use SqlFtw\Sql\Keyword;

class AlterTableAlgorithm extends \SqlFtw\Sql\SqlEnum
{

    public const DEFAULT = Keyword::DEFAULT;
    public const INPLACE = Keyword::INPLACE;
    public const COPY = Keyword::COPY;

}
