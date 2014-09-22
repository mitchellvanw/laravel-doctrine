<?php namespace Mitch\LaravelDoctrine\Validation;

use Illuminate\Support\Contracts\JsonableInterface;

class ValidationException extends \Exception implements JsonableInterface
{
    /**
     * Stores the errors that were found during validation.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * The default message for all validation. This gets returned along with the errors.
     *
     * @var string
     */
    protected $message = 'There is something wrong with the input provided. Please check the information you have entered and try again.';

    /**
     * Stores an array of the fields that failed validation.
     *
     * @var array
     */
    protected $failedFields = [];

    /**
     * Returns the validation errors that were generated at validation time.
     *
     * @return array
     */
    public function getValidationErrors()
    {
        return $this->errors;
    }
    
    /**
     * Returns an array of the field names that failed validation.
     *
     * @return array
     */
    public function getFailedFields()
    {
        return $this->failedFields;
    }

    /**
     * Set the validation errors that occurred.
     *
     * @param array $errors
     */
    public function setValidationErrors(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Similar to messages but this is just the failed fields.
     *
     * @param array $fields
     */
    public function setFailedFields(array $fields)
    {
        $this->failedFields = $fields;
    }

    /**
     * Required for the JsonableInterface implementation.
     *
     * @param integer $options
     * @return array
     */
    public function toJson($options = 0)
    {
        return json_encode($this->errors);
    }
}
