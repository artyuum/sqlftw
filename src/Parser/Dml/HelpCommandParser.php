<?php
/**
 * This file is part of the SqlFtw library (https://github.com/sqlftw)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Parser\Dml;

use SqlFtw\Sql\Dml\Utility\HelpCommand;
use SqlFtw\Sql\Keyword;
use SqlFtw\Parser\TokenList;

class HelpCommandParser
{
    use \Dogma\StrictBehaviorMixin;

    /**
     * HELP 'search_string'
     */
    public function parseHelp(TokenList $tokenList): HelpCommand
    {
        $tokenList->consumeKeyword(Keyword::HELP);
        $term = $tokenList->consumeString();

        return new HelpCommand($term);
    }

}
