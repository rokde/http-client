<?php

namespace Rokde\HttpClient;

class Header
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string[]
     */
    protected $value = [];

    /**
     * @param string $name
     * @param null $value
     */
    public function __construct(string $name, $value = null)
    {
        $this->name = strtolower($name);

        if ($value !== null) {
            foreach ((array)$value as $v) {
                $this->addValue($v);
            }
        }
    }

    public function addValue(string $value): self
    {
        $this->value[] = $value;

        return $this;
    }

    /**
     * factory creation
     *
     * @param  string $string
     * @return Header
     */
    public static function fromString(string $string): self
    {
        if (strpos($string, ':') === false) {
            throw new \InvalidArgumentException('Given string has not a valid header format');
        }

        $parts = explode(':', $string, 2);

        return new static(trim($parts[0]), trim($parts[1]));
    }

    public function value(): array
    {
        return $this->value;
    }

    public function firstValue()
    {
        return current($this->value);
    }

    public function setValue(string $value): self
    {
        $this->value = [];

        return $this->addValue($value);
    }

    public function __toString()
    {
        return $this->valueLine();
    }

    public function valueLine(): string
    {
        $line = '';
        foreach ($this->value as $value) {
            $line .= $this->name() . ': ' . $value . "\r\n";
        }

        return $line;
    }

    public function name(): string
    {
        return $this->name;
    }
}
