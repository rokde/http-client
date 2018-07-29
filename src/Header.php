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

	public function __construct(string $name, $value = null)
	{
		$this->name = strtolower($name);

		if ($value !== null) {
			foreach ((array)$value as $v) {
				$this->addValue($v);
			}
		}
	}

	public static function fromString(string $string): self
	{
		if (strpos($string, ':') === false) {
			throw new \InvalidArgumentException('Given string has not a valid header format');
		}

		$parts = explode(':', $string, 2);

		return new static(trim($parts[0]), trim($parts[1]));
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getValue(): array
	{
		return $this->value;
	}

	public function setValue(string $value): self
	{
		$this->value = [];

		return $this->addValue($value);
	}

	public function addValue(string $value): self
	{
		$this->value[] = $value;

		return $this;
	}

	public function getValueLine(): string
	{
		$line = '';
		foreach ($this->value as $value) {
			$line .= $this->getName() . ': ' . $value . "\r\n";
		}

		return $line;
	}

	public function __toString()
	{
		return $this->getValueLine();
	}
}