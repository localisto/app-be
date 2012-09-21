<?php

function error_response($message)
{
  return array('status' => 'error', 'error_message' => $message);
}

function validate_email($email)
{
  return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_string($string)
{
  return $string;
}

function validate_int($int)
{
  return filter_var($int, FILTER_VALIDATE_INT);
}

function validate_int_array($arr)
{
  $filtered_array = array();
  foreach ($arr as $key => $val)
  {
    $new_val = validate_int($val);
    if ($new_val === FALSE)
    {
      throw new LocalistoException('Invalid integer');
    }
    $filtered_array[$key] = $new_val;
  }
  return $filtered_array;
}

function create_salt()
{
  return base64_encode(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
}

function hash_password($password, $salt)
{
  return base64_encode(hash('sha512', $password . ':' . $salt));
}

// for now it can be just the same as the salt
function create_login_token()
{
  return create_salt();
}

function localisto_date_format($timestamp)
{
  return date('D, M j, g:ia', $timestamp);
}

function ios_date_format($timestamp)
{
  return date('Y-m-d\TH:i:sO', $timestamp);
  // return date('Y-m-dTG:i:sO', $timestamp);
}