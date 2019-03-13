<?php

namespace backend\exceptions;

use backend\common\model\AbstractFormModel;
use yii\web\HttpException;

/**
 * Class FormValidationException
 * @package backend\exceptions
 */
class FormValidationException extends HttpException
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * FormValidationException constructor.
     * @param AbstractFormModel $form
     * @param string $message
     * @param int $code
     * @param \Throwable $previous
     */
    public function __construct(AbstractFormModel $form, string $message = 'An error occurred while validating the form data.', $code = 0, \Throwable $previous = null)
    {
        $this->errors = $form->getFirstErrors();

        parent::__construct(418, $message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
