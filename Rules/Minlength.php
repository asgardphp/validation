<?php
namespace Asgard\Validation\Rules;

class Minlength extends \Asgard\Validation\Rule {
	public $length;

	public function __construct($length) {
		$this->length = $length;
	}

	public function validate($input, \Asgard\Validation\InputBag $parentInput, \Asgard\Validation\Validator $validator) {
		return strlen($input) >= $this->length;
	}

	public function getMessage() {
		return ':attribute must be at least :length characters long.';
	}
}