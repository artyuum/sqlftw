<?php declare(strict_types = 1);
/**
 * This file is part of the SqlFtw library (https://github.com/sqlftw)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Analyzer\Context;

use SqlFtw\Sql\Expression\ObjectIdentifier;

/**
 * Other objects (MySQL):
 *  - system variables
 *  - users
 *  - installed UDFs & loadable functions
 *  - installed storage engines
 *  - installed charsets & collations
 *  - server
 *  - tablespace
 *
 * Postgre (main):
 *  - aggregate
 *  - domain (data type with constraints)
 *  - operator
 *  - operator class
 *  - operator family
 *  - type
 *
 * Postgre (other):
 *  - access method
 *  - conversion
 *  - database
 *  - extension
 *  - foreign data wrapper
 *  - foreign table
 *  - group
 *  - language
 *  - policy
 *  - publication
 *  - rule
 *  - sequence
 *  - server
 *  - statistics
 *  - subscription
 *  - tablespace
 *  - text search ...
 *  - transform
 *  - user mapping
 */
interface ContextProvider
{

    /**
     * Returns parsable CREATE {SCHEMA|DATABASE} command
     */
    public function getSchemaDefinition(string $name): ?string;

    /**
     * Returns parsable CREATE TABLE command
     */
    public function getTableDefinition(ObjectIdentifier $name): ?string;

    /**
     * Returns parsable CREATE VIEW command
     */
    public function getViewDefinition(ObjectIdentifier $name): ?string;

    /**
     * Returns parsable CREATE EVENT command
     */
    public function getEventDefinition(ObjectIdentifier $name): ?string;

    /**
     * Return parsable CREATE FUNCTION command
     */
    public function getFunctionDefinition(ObjectIdentifier $name): ?string;

    /**
     * Return parsable CREATE PROCEDURE command
     */
    public function getProcedureDefinition(ObjectIdentifier $name): ?string;

    /**
     * Returns parsable CREATE TRIGGER command
     */
    public function getTriggerDefinition(ObjectIdentifier $name): ?string;

}
