<?php
namespace Asgard\Validation\Rules;

/**
 * Check that the input is equal or less than the given number.
 */
class Max extends \Asgard\Validation\Rule {
	/**
	 * Max number.
	 * @var float
	 */
	public $max;

	/**
	 * Constructor.
	 * @param float $max
	 */
	public function __construct($max) {
		$this->max = $max;
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate($input, \Asgard\Validation\InputBag $parentInput, \Asgard\Validation\Validator $validator) {
		return $input <= $this->max;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMessage() {
		return ':attribute must be less than :max.';
	}
}