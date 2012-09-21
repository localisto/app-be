<?php

class LocalistoException extends Exception
{
  private $status;
  
  public function __construct($message, $status = 'error')
  {
    $this->message = $message;
    $this->status = $status;
  }
  
  public function getStatus()
  {
    return $this->status;
  }
}