<?php

ini_set('display_errors', 'Off');

// remeber to enable always_populate_raw_post_data in order for the php://input line to work

include('config.php');
include('functions.php');
include('Localisto.php');
include('LocalistoException.php');

// make sure we're displaying date in PST
date_default_timezone_set('America/Los_Angeles');

$pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);

$encoded_input = file_get_contents("php://input");
$input = json_decode($encoded_input, true);

if (isset($input['action']) && isset($input['data']))
{
  $localisto = new Localisto($pdo);
  $action = 'action_' . $input['action']; 
  if (method_exists('Localisto', $action))
  {
    try
    {
      if (!in_array($action, array('action_account_create', 'action_login', 'action_fb_account_create_or_login')))
      {
        $localisto->check_login_token($input['data']);
      }
      $output = $localisto->$action($input['data']);
    }
    catch (Exception $e)
    {
      trigger_error($e->getMessage(), E_USER_WARNING);
      if ($e->getStatus() == 'error')
      {
        // if specific status wasn't specified, we display a generic message instead
        $output = array('status' => 'error', 'error_message' => 'Unknown error.');
      }
      else
      {
        $output = array('status' => $e->getStatus(), 'error_message' => $e->getMessage());        
      }
    }
  }
  else
  {
    $output = error_response('Invalid action.');
  }
}
else
{
  $output = error_response($input['action'] . 'Invalid request.');
}
echo json_encode($output);
exit;