<?php
namespace Asgard\Validation;

/**
 * Errors container.
 * @author Michel Hognerud <michel@hognerud.com>
 */
class Report implements \ArrayAccess, \Countable {
	/**
	 * The main error.
	 * @var string
	 */
	protected $self;
	/**
	 * Rules errors.
	 * @var array<string>
	 */
	protected $rules = [];
	/**
	 * Attributes errors.
	 * @var array
	 */
	protected $attributes = [];

	/**
	 * Constructor.
	 * @param array $errors array of string errors
	 */
	public function __construct(array $errors=[]) {
		if(isset($errors['self']))
			$this->self = $errors['self'];
		if(isset($errors['rules']))
			$this->rules = $errors['rules'];
		if(isset($errors['attributes'])) {
			foreach($errors['attributes'] as $attribute=>$_errors)
				$this->attributes[$attribute] = new static($_errors);
		}
	}

	/**
	 * __toString magic method.
	 * @return string
	 */
	public function __toString() {
		if($this->self)
			return (string)$this->self;
		return (string)$this->first();
	}

	/**
	 * Show the full error message.
	 * @return string
	 */
	public function fullMessage() {
		if($this->self)
			$str = (string)$this->self.':';
		else
			$str = (string)$this->first().':';

		foreach($this->rules as $rule)
			$str .= "\n\t".$rule;

		foreach($this->attributes as $attribute)
			$str .= "\n\t".$attribute->first();

		return $str;
	}

	/**
	 * Return the error of a rule if provided, otherwise the main error.
	 * @param  string $rule rule name
	 * @return string
	 */
	public function error($rule=null) {
		if($rule === null)
			return $this->self;
		elseif(isset($this->rules[$rule]))
			return $this->rules[$rule];
		elseif($attribute = $this->attribute($rule))
			return $attribute->error();
	}

	/**
	 * Return an array of rules and attributes errors.
	 * @return array
	 */
	public function errors($nested=true) {
		$errors = $this->rules;
		foreach($this->attributes as $attribute=>$report)
			$errors[$attribute] = $nested ? $report->errors():$report->error();
		return $errors;
	}

	/**
	 * Return the first error of the report or of an attribute if provided.
	 * @param  string $attribute attribute name
	 * @return string
	 */
	public function first($attribute=null) {
		if($attribute !== null)
			return $this->attribute($attribute)->first();
		else {
			if($this->rules)
				return array_values($this->rules)[0];
			elseif($this->attributes)
				return array_values($this->attributes)[0]->error();
		}
	}

	/**
	 * Return the array of failed attributes.
	 * @return array
	 */
	public function failed() {
		$failed = [];
		foreach($this->attributes as $attribute=>$report) {
			$attrFailed = $report->failed();
			if($attrFailed)
				$failed[$attribute] = $attrFailed;
			else
				$failed[] = $attribute;
		}
		return $failed;
	}

	/**
	 * Return an attribute report or set one if provided.
	 * @param  string $attribute attribute name
	 * @param  Report $report    attribute report
	 * @return Report            $this or the attrbute report
	 */
	public function attribute($attribute, Report $report=null) {
		if(is_string($attribute))
			$attribute = explode('.', $attribute);

		$next = array_shift($attribute);
		if(!isset($this->attributes[$next]))
			$this->attributes[$next] = new static;

		if($report !== null) {
			if(count($attribute) == 0)
				$this->attributes[$next] = $report;
			else
				$this->attributes[$next]->attribute($attribute, $report);
			return $this;
		}
		else {
			if(count($attribute) == 0)
				return $this->attributes[$next];
			else
				return $this->attributes[$next]->attribute($attribute);
		}
	}

	/**
	 * Return the reports of all attributes
	 * @return array
	 */
	public function attributes() {
		return $this->attributes;
	}

	/**
	 * Check if the report contains an error.
	 * @return boolean
	 */
	public function hasError() {
		return $this->rules || $this->attributes;
	}

	public function setRulesErrors(array $errors) {
		$this->rules = $errors;
	}

	public function getRulesErrors() {
		return $this->rules;
	}

	public function setSelf($error) {
		$this->self = $error;
	}

	public function offsetSet($offset, $value) {
		$this->rules[$offset] = $value;
	}

	public function offsetExists($offset) {
		return isset($this->rules[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->rules[$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->rules[$offset]) ? $this->rules[$offset] : $this->attribute($offset);
	}

	public function count() {
		return count($this->rules) + count($this->attributes);
	}

	public function valid() {
		return $this->count() === 0;
	}

	public function merge(Report $report) {
		$this->rules = array_merge($this->rules, $report->getRulesErrors());
		foreach($report->attributes() as $name=>$attr)
			$this->attribute($name)->merge($attr);

		return $this;
	}
}