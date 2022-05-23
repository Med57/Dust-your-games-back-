<?php

namespace App\Models;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Custom object for errors in my API
 */
class CustomJsonError
{
    /**
     * Error code
     *
     * @var int
     */
    public $errorCode;

    /**
     * Error message
     *
     * @var string
     */
    public $message;

    /**
     * List of error messages during a validation
     *
     * @var array
     */
    public $messagesValidation = [];

    public function setErrorValidation(ConstraintViolationListInterface $errors)
    {

        foreach ($errors as $error) {
            $this->messagesValidation[] = "La valeur ". $error->getInvalidValue() . " ne respecte pas les règles de validation de la propriété '" . $error->getPropertyPath() . "'";
        }
    }
}