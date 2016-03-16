<?php
namespace Asgard\Validation\Rule;

/**
 * Check that the input contains less than a given number.
 * @author Michel Hognerud <michel@hognerud.com>
 */
class Haslessthan extends \Asgard\Validation\Rule {
	/**
	 * Count.
	 * @var integer
	 */
	public $count;

	/**
	 * Constructor.
	 * @param integer $count
	 */
	public function __construct($count) {
		$this->count = $count;
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate($input, \Asgard\Validation\InputBag $parentInput, \Asgard\Validation\ValidatorInterface $validator) {
		return count($input) < $this->count;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMessage() {
		return ':attribute must have less than :count elements.';
	}
}