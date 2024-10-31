<?php 

namespace App\Exceptions;

interface AppException 
{
    /**
     * Get error title
     * @return string
     */
    public function getTitle();

     /**
     * Get error messages
     * @return string
     */
    public function getErrors();
}
