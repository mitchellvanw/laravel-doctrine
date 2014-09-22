<?php namespace Mitch\LaravelDoctrine\Validation;

use Validator;

/**
 * Class Validation
 *
 * The validator class provides the base functionality required for any other validation classes for resources to
 * set rules, define the user input, and validate the input against these rules.
 *
 * If validation fails for whatever reason, a ValidationException is thrown, which contains a generic message
 * as well as the specific errors for each individual error that may have occurred.
 *
 * @package Mitch\LaravelDoctrine\Validation
 */

abstract class Validation
{
    /**
     * Stores the array of data that a user provided as part of the request.
     *
     * @var array
     */
    protected $input = [];

    /**
     * Stores the messages that should be returned when an error fails.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * If you do not wish to define custom rules for each method, you can define a rules array on the validator class itself.
     * If no method is defined for setting the validation rules, then the rules array will be used as the basis for the validation.
     * If no rules are provided, then validation will pass.
     *
     * @var array
     */
    protected $rules = [];

	/**
	 * Set the input for validation.
	 *
	 * @param array $input
	 * @return Validation
	 */
	public function setInput(array $input = [])
	{
		$this->input = $input;

		return $this;
	}

    /**
     * Returns the input array that was defined for validation.
     *
     * @return array
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Returns the value of a single field.
     *
     * @param $field
     * @return mixed
     */
    public function getValue($field)
    {
        if (isset($this->input[$field])) {
            return $this->input[$field];
        }

        return;
    }

    /**
     * Validates the rules provided either by a custom method or on the class against the user input provided.
     *
     * @throws ValidationConfigurationException
     * @throws ValidationException
     */
    public function validate()
    {
        $rules = $this->getRules();

        $validator = Validator::make($this->getInput(), $rules);

        if ($validator->fails()) {
            $exception = new ValidationException;
            $exception->setValidationErrors($validator->messages()->all());
            $exception->setFailedFields($validator->failed());

            throw $exception;
        }

        return true;
    }

    /**
     * Retrieves the rules that have defined for the validation.
     *
     * @return array
     * @throws ValidationConfigurationException
     */
    public function getRules()
    {
        if (!is_array($this->rules)) {
            throw new ValidationConfigurationException('Validation rules defined must be provided as an array.');
        }

        return $this->rules;
    }
}
