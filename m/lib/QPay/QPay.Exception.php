<?php

class QPayException extends Exception
{
    public function errorMessage()
    {
        return $this->getMessage();
    }
}