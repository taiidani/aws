<?php
namespace Rnd\Aws\Enum;

use InvalidArgumentException;
use ReflectionClass;

/**
 * A simple enumeration implementation
 */
abstract class BaseEnum {

	/**
	 * The default value if no value is provided. Can be overridden in subclasses
	 */
	const __default = null;

	/**
	 * The value that this enum is currently set to
	 * @var string
	 */
	private $value;

	/**
	 * Validates the given value against the allowed values and assigns it
	 * @param string $value A valid enumeration value
	 * @throws InvalidArgumentException if the provided value is not in the allowed constants
	 */
	public function __construct($value = null) {
		if(is_null($value)) {
			$value == static::__default;
		}

		if(!in_array($value, array_values($this->getConstants()))) {
			throw new InvalidArgumentException("Not a valid value '$value'");
		}

		$this->value = $value;
	}

	/**
	 * Returns the assigned value as a string
	 * @return string The assigned value
	 */
	public function __toString() {
		return $this->value;
	}

	/**
	 * Returns the list of allowed constant keys for this class
	 * @return array Allowed constant => strings to use for instantiating this class
	 */
    public function getConstants() {
    	$cls = new ReflectionClass($this);
    	return $cls->getConstants();
    }
    
}