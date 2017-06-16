<?php
/**
 * This file is part of the SqlFtw library (https://github.com/sqlftw)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Parser\Dal;

use SqlFtw\Sql\Charset;
use SqlFtw\Sql\Dal\Set\SetCharacterSetCommand;
use SqlFtw\Sql\Dal\Set\SetNamesCommand;
use SqlFtw\Sql\Keyword;
use SqlFtw\Parser\TokenList;

class CharsetCommandsParser
{
    use \Dogma\StrictBehaviorMixin;

    /**
     * SET {CHARACTER SET | CHARSET}
     *     {'charset_name' | DEFAULT}
     */
    public function parseSetCharacterSet(TokenList $tokenList): SetCharacterSetCommand
    {
        $tokenList->consumeKeyword(Keyword::SET);
        $keyword = $tokenList->consumeAnyKeyword(Keyword::CHARACTER, Keyword::CHARSET);
        if ($keyword === Keyword::CHARACTER) {
            $tokenList->consumeKeyword(Keyword::SET);
        }
        $charset = null;
        if ($tokenList->mayConsumeKeyword(Keyword::DEFAULT) !== null) {
            $charset = Charset::get($tokenList->consumeNameOrString());
        }

        return new SetCharacterSetCommand($charset);
    }

    /**
     * SET NAMES {'charset_name'
     *     [COLLATE 'collation_name'] | DEFAULT}
     */
    public function parseSetNames(TokenList $tokenList): SetNamesCommand
    {
        $tokenList->consumeKeywords(Keyword::SET, Keyword::NAMES);
        $charset = $collation = null;
        if ($tokenList->mayConsumeKeyword(Keyword::DEFAULT) !== null) {
            $charset = Charset::get($tokenList->consumeNameOrString());
            if ($tokenList->mayConsumeKeyword(Keyword::COLLATE) !== null) {
                $collation = $tokenList->consumeNameOrString();
            }
        }

        return new SetNamesCommand($charset, $collation);
    }

}
