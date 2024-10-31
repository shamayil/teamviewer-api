<?php 

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NotFoundException extends HttpException implements AppException
{
    /**
     * Error title
     * @var string
     */
    protected $message;

    /**
     * Error message
     * @var string
     */
    protected $errors;

    /**
     * Create exception instance
     * @param int $code
     * @param string $message
     * @param string $errors
     */
    public function __construct(string $message, string $errors)
    {
        parent::__construct(404, $message);

        $this->message = $message;
        $this->errors = $errors;
    }

    /**
     * Get error title
     * @return string
     */
    public function getTitle()
    {
        return $this->message;
    }

     /**
     * Get error messages
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
