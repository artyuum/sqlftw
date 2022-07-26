<?php declare(strict_types = 1);
/**
 * This file is part of the SqlFtw library (https://github.com/sqlftw)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Formatter;

use DateTimeInterface;
use Dogma\Arr;
use Dogma\NotImplementedException;
use Dogma\Time\Date;
use Dogma\Time\DateTime;
use Dogma\Time\Time;
use SqlFtw\Session\Session;
use SqlFtw\Sql\Expression\AllLiteral;
use SqlFtw\Sql\Expression\Literal;
use SqlFtw\Sql\Expression\PrimaryLiteral;
use SqlFtw\Sql\Keyword;
use SqlFtw\Sql\SqlMode;
use SqlFtw\Sql\SqlSerializable;
use SqlFtw\Sql\Statement;
use function array_keys;
use function array_map;
use function array_values;
use function implode;
use function is_numeric;
use function is_string;
use function str_replace;

class Formatter
{

    private const MYSQL_ESCAPES = [
        '\\' => '\\\\',
        "\x00" => '\0',
        "\x08" => '\b',
        "\n" => '\n', // 0a
        "\r" => '\r', // 0d
        "\t" => '\t', // 09
        "\x1a" => '\Z', // 1a (legacy Win EOF)
    ];

    /** @var Session */
    private $session;

    /** @var string */
    public $indent;

    /** @var bool */
    public $comments;

    /** @var bool */
    public $quoteAllNames;

    public function __construct(
        Session $session,
        string $indent = '  ',
        bool $comments = false,
        bool $quoteAllNames = false
    ) {
        $this->session = $session;
        $this->indent = $indent;
        $this->comments = $comments;
        $this->quoteAllNames = $quoteAllNames;
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function indent(string $code): string
    {
        return str_replace("\n", "\n\t", $code);
    }

    public function formatName(string $name): string
    {
        $quote = $this->session->getMode()->containsAny(SqlMode::ANSI_QUOTES) ? '"' : '`';

        return $this->quoteAllNames
            ? $quote . $name . $quote
            : ($this->session->getPlatform()->isReserved($name)
                ? $quote . $name . $quote
                : $name);
    }

    /**
     * @param non-empty-array<string|AllLiteral|PrimaryLiteral> $names
     */
    public function formatNamesList(array $names, string $separator = ', '): string
    {
        return implode($separator, array_map(function ($name): string {
            return $name instanceof Literal ? $name->getValue() : $this->formatName($name);
        }, $names));
    }

    /**
     * @param int|float|bool|string|Date|Time|DateTimeInterface|SqlSerializable|null $value
     */
    public function formatValue($value): string
    {
        if ($value === null) {
            return Keyword::NULL;
        } elseif ($value === true) {
            return '1';
        } elseif ($value === false) {
            return '0';
        } elseif (is_string($value)) {
            return $this->formatString($value);
        } elseif (is_numeric($value)) {
            return (string) $value;
        } elseif ($value instanceof SqlSerializable) {
            return $value->serialize($this);
        } elseif ($value instanceof Date) {
            return $this->formatDate($value);
        } elseif ($value instanceof Time) {
            return $this->formatTime($value);
        } elseif ($value instanceof DateTimeInterface) {
            return $this->formatDateTime($value);
        }

        throw new NotImplementedException('Unknown type.');
    }

    /**
     * @param non-empty-array<int|float|bool|string|Date|Time|DateTimeInterface|SqlSerializable|null> $values
     */
    public function formatValuesList(array $values, string $separator = ', '): string
    {
        return implode($separator, array_map(function ($value): string {
            return $this->formatValue($value);
        }, $values));
    }

    public function formatString(string $string): string
    {
        if (!$this->session->getMode()->containsAny(SqlMode::NO_BACKSLASH_ESCAPES)) {
            $string = str_replace(array_keys(self::MYSQL_ESCAPES), array_values(self::MYSQL_ESCAPES), $string);
        }

        return "'" . str_replace("'", "''", $string) . "'";
    }

    /**
     * @param non-empty-array<string> $strings
     */
    public function formatStringList(array $strings, string $separator = ', '): string
    {
        return implode($separator, array_map(function (string $string): string {
            return $this->formatString($string);
        }, $strings));
    }

    /**
     * @param non-empty-array<SqlSerializable> $serializables
     */
    public function formatSerializablesList(array $serializables, string $separator = ', '): string
    {
        return implode($separator, array_map(function (SqlSerializable $serializable): string {
            return $serializable->serialize($this);
        }, $serializables));
    }

    /**
     * @param non-empty-array<SqlSerializable> $serializables
     */
    public function formatSerializablesMap(array $serializables, string $separator = ', ', string $keyValueSeparator = ' = '): string
    {
        return implode($separator, Arr::mapPairs($serializables, function (string $key, SqlSerializable $value) use ($keyValueSeparator): string {
            return $key . $keyValueSeparator . $value->serialize($this);
        }));
    }

    /**
     * @param Date|DateTimeInterface $date
     */
    public function formatDate($date): string
    {
        return "'" . $date->format(Date::DEFAULT_FORMAT) . "'";
    }

    /**
     * @param Time|DateTimeInterface $time
     */
    public function formatTime($time): string
    {
        return "'" . $time->format(Time::DEFAULT_FORMAT) . "'";
    }

    public function formatDateTime(DateTimeInterface $dateTime): string
    {
        return "'" . $dateTime->format(DateTime::DEFAULT_FORMAT) . "'";
    }

    public function serialize(SqlSerializable $serializable): string
    {
        if ($serializable instanceof Statement) {
            // todo: comments

            return $serializable->serialize($this);
        } else {
            return $serializable->serialize($this);
        }
    }

}
