<?php
/**
 * This file is part of the SqlFtw library (https://github.com/sqlftw)
 *
 * Copyright (c) 2017 Vlasta Neubauer (@paranoiq)
 *
 * For the full copyright and license information read the file 'license.md', distributed with this source code
 */

namespace SqlFtw\Sql\Dal\Flush;

use Dogma\Arr;
use Dogma\Check;
use SqlFtw\SqlFormatter\SqlFormatter;

class FlushCommand implements \SqlFtw\Sql\Command
{
    use \Dogma\StrictBehaviorMixin;

    /** @var \SqlFtw\Sql\Dal\Flush\FlushOption[] */
    private $options;

    /** @var string|null */
    private $channel;

    /** @var bool */
    private $local;

    /**
     * @param \SqlFtw\Sql\Dal\Flush\FlushOption[] $options
     * @param string|null $channel
     * @param bool $local
     */
    public function __construct(array $options, ?string $channel = null, bool $local = false)
    {
        Check::array($options, 1);
        Check::itemsOfType($options, FlushOption::class);

        $this->options = $options;
        $this->channel = $channel;
        $this->local = $local;
    }

    /**
     * @return \SqlFtw\Sql\Dal\Flush\FlushOption[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function isLocal(): bool
    {
        return $this->local;
    }

    public function serialize(SqlFormatter $formatter): string
    {
        $result = 'FLUSH ';
        if ($this->isLocal()) {
            $result .= ' LOCAL ';
        }
        $result .= implode(', ', Arr::map($this->options, function (FlushOption $option) use ($formatter) {
            if ($option->equals(FlushOption::RELAY_LOGS) && $this->channel !== null) {
                return $option->serialize($formatter) . ' FOR CHANNEL ' . $formatter->formatString($this->channel);
            } else {
                return $option->serialize($formatter);
            }
        }));

        return $result;
    }

}
