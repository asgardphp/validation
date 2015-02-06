<?php
namespace Asgard\Validation;

/**
 * Validator factory.
 * @author Michel Hognerud <michel@hognerud.com>
 */
class ValidatorFactory implements ValidatorFactoryInterface {
	/**
	 * Rules registry.
	 * @var RulesRegistry
	 */
	protected $rulesRegistry;

	/**
	 * Constructor.
	 * @param RulesRegistry $rulesRegistry
	 */
	public function __construct(RulesRegistry $rulesRegistry=null) {
		$this->rulesRegistry = $rulesRegistry;
	}

	/**
	 * {@inheritDoc}
	 * @return TemplateEngine
	 */
	public function create() {
		return new Validator($this->rulesRegistry);
	}
}