<?php

class Localisto
{
  private $pdo;
  private $user_id;
  const SESSION_LIFETIME = 7200; // lifetime of a session in seconds, 7200 is two hours

  /**
   *
   */
  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  /**
   *
   */
  public function check_login_token($data)
  {
    if (!isset($data['login_token']))
    {
      throw new LocalistoException('No login token sent.');
    }

    $stmt = $this->pdo->prepare('select user_id, enabled, unix_timestamp(last_accessed) as last_accessed from session where login_token = :login_token limit 1');
    $stmt->execute(array('login_token' => $data['login_token']));
    
    $results = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($results === FALSE)
    {
      throw new LocalistoException('Unknown session.');
    }
/*    elseif ($results['last_accessed'] + self::SESSION_LIFETIME < time())
    {
      if ($this->pdo->prepare('update session set enabled = 0 where login_token = :login_token limit 1')->execute(array('login_token' => $data['login_token'])) === FALSE)
      {
        throw new LocalistoException('Unknown error updating session.');        
      }
      throw new LocalistoException('Session timed out. Please login again.', 'session_timeout');
    }*/
    elseif ($results['enabled'] != 1)
    {
      throw new LocalistoException('Session timed out. Please login again.', 'session_timeout');
    }
    else
    {
      $this->user_id = $results['user_id'];
      if ($this->pdo->prepare('update session set last_accessed = now() where login_token = :login_token limit 1')->execute(array('login_token' => $data['login_token'])) === FALSE)
      {
        throw new LocalistoException('Unknown error updating session.');        
      }
    }
  }
 
  // begin of actions
 
  /**
   *
   */ 
  public function action_account_create($data)
  {
    if (!isset($data['email']) || !isset($data['password']))
    {
      throw new LocalistoException('Invalid request');
    }
    
    $email = validate_email($data['email']);
    $password = validate_string($data['password']);
  
    if (!$email)
    {
      return array('status' => 'error', 'error_message' => 'Invalid email.', 'affected_fields' => array('email'));
    }
  
    $stmt = $this->pdo->prepare('select count(*) from user where email = :email and fb_account = 0');
    $stmt->execute(array('email' => $email));
    if ($stmt->fetchColumn() != 0)
    {
      return array('status' => 'error', 'error_message' => 'This email is already registered. Please login instead.', 'affected_fields' => array('email'));
    }
  
    // if we got here, it's legit to create an account
    $stmt = $this->pdo->prepare('insert into user (username, email, password, salt) values (:email, :email, :password, :salt)');
    $salt = create_salt();
    $status = $stmt->execute(array('email' => $email, 'password' => hash_password($password, $salt), 'salt' => $salt));
    if ($status !== TRUE)
    {
      $error = $stmt->errorInfo();
      throw new LocalistoException($error[2]);
    }
    
    $this->user_id = $this->pdo->lastInsertId();
    
    $this->populate_agencies($this->user_id);
  
    $login_token = $this->create_session($email, 0);
  
    $this->sendConfirmationEmail($email);
  
    return array('status' => 'ok', 'login_token' => $login_token);
  }

  /**
   *
   */
  public function action_logout($data)
  {
    if ($this->pdo->prepare('update session set last_accessed = now(), enabled = 0 where login_token = :login_token limit 1')->execute(array('login_token' => $data['login_token'])) === FALSE)
    {
      throw new LocalistoException('Unknown error logging out.');
    }
    return array('status' => 'ok');
  }
    
  /**
   *
   */ 
  public function action_login($data)
  {
    $email = validate_email($data['email']);
    $password = validate_string($data['password']);
  
    if (!$email)
    {
      return array('status' => 'error', 'error_message' => 'The email or password you entered is invalid. Please try again.', 'affected_fields' => array('email', 'password'));
    }
  
    $stmt = $this->pdo->prepare('select * from user where email = :email');
    $stmt->execute(array('email' => $email));
    
    $results = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($results === FALSE)
    {
      return array('status' => 'error', 'error_message' => 'The email or password you entered is invalid. Please try again.', 'affected_fields' => array('email', 'password'));
    }
    
    $salt = $results['salt'];
    $hashed_password = hash_password($password, $salt);
    
    if ($hashed_password != $results['password'])
    {
      return array('status' => 'error', 'error_message' => 'The email or password you entered is invalid. Please try again.', 'affected_fields' => array('email', 'password'));
    }
      
    // if we got here, it's legit to login
    $login_token = $this->create_session($email, 0);
  
    return array('status' => 'ok', 'login_token' => $login_token);
  }

  /**
   *
   */ 
  public function action_user_agency_list($data)
  {
    $stmt = $this->pdo->prepare('select ua.agency_id, ua.position, a.name from user_agency ua join agency a on (a.id = ua.agency_id) where ua.user_id = :user_id order by ua.position asc');
    $stmt->execute(array('user_id' => $this->user_id));

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results === FALSE)
    {
      $error = $stmt->errorInfo();
      throw new LocalistoException($error[2]);
    }

    $agencies = array();
    foreach ($results as $result)
    {
      $sort_order = $result['position'];
      $agencies[$sort_order] = array('id' => $result['agency_id'], 'name' => $result['name']);
    }
    return array('status' => 'ok', 'agencies' => $agencies);
  }

  /**
   *
   */ 
  public function action_agency_project_list($data)
  {
    if (!isset($data['agency_id']))
    {
      throw new LocalistoException('Invalid request');
    }
    
    $agency_id = validate_int($data['agency_id']);

    $stmt = $this->pdo->prepare('select id, title, meeting_starts, survey_closes, grid_image from project where agency_id = :agency_id and disabled = 0 order by meeting_starts asc, survey_closes asc');
    $stmt->execute(array('agency_id' => $agency_id));

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results === FALSE)
    {
      $error = $stmt->errorInfo();
      throw new LocalistoException($error[2]);
    }

    $projects = array();
    foreach ($results as $result)
    {
      $date = isset($result['meeting_starts']) ? $result['meeting_starts'] : $result['survey_closes'];
      $formatted_date = localisto_date_format(strtotime($date));
      $projects[] = array('id' => $result['id'], 'name' => $result['title'], 'image' => $result['grid_image'], 'date' => $formatted_date);
    }
    return array('status' => 'ok', 'projects' => $projects);
  }

  /**
   *
   */ 
  public function action_all_agency_list($data)
  {
    $stmt = $this->pdo->prepare('select id, position, name from agency order by position asc');
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results === FALSE)
    {
      $error = $stmt->errorInfo();
      throw new LocalistoException($error[2]);
    }

    $agencies = array();
    foreach ($results as $result)
    {
      $sort_order = $result['position'];
      $agencies[$sort_order] = array('id' => $result['id'], 'name' => $result['name']);
    }
    return array('status' => 'ok', 'agencies' => $agencies);
  }

  /**
   *
   */ 
  public function action_user_follow_agency($data)
  {
    if (!isset($data['agency_id']))
    {
      throw new LocalistoException('Invalid request');
    }
    
    $agency_id = validate_int($data['agency_id']);

    $stmt = $this->pdo->prepare('select max(position) from user_agency where user_id = :user_id');
    $result = $stmt->execute(array('user_id' => $this->user_id));

    if ($result === FALSE)
    {
      $error = $stmt->errorInfo();
      throw new LocalistoException($error[2]);
    }
    
    $sort_order = $stmt->fetchColumn();
    
    $sort_order += 10; // for the new agency
    
    $stmt = $this->pdo->prepare('insert into user_agency (user_id, agency_id, position) values (:user_id, :agency_id, :sort_order)');
    $result = $stmt->execute(array('agency_id' => $agency_id, 'user_id' => $this->user_id, 'sort_order' => $sort_order));

    if ($result === FALSE)
    {
      $error = $stmt->errorInfo();
      throw new LocalistoException($error[2]);
    }

    return array('status' => 'ok', 'new_sort_order' => $sort_order);
  }

  /**
   *
   */ 
  public function action_user_remove_agency($data)
  {
    if (!isset($data['agency_id']))
    {
      throw new LocalistoException('Invalid request');
    }
    
    $agency_id = validate_int($data['agency_id']);

    $stmt = $this->pdo->prepare('delete from user_agency where user_id = :user_id and agency_id = :agency_id limit 1');
    $result = $stmt->execute(array('agency_id' => $agency_id, 'user_id' => $this->user_id));

    if ($result === FALSE)
    {
      $error = $stmt->errorInfo();
      throw new LocalistoException($error[2]);
    }

    return array('status' => 'ok');
  }

  /**
   *
   */ 
  public function action_user_set_agency_order($data)
  {
    if (!isset($data['agency_list']))
    {
      throw new LocalistoException('Invalid request');
    }
    
    $agency_list = validate_int_array($data['agency_list']);
    
    $this->pdo->beginTransaction();

    // first prune the old data

    $stmt = $this->pdo->prepare('delete from user_agency where user_id = :user_id');
    $result = $stmt->execute(array('user_id' => $this->user_id));

    if ($result === FALSE)
    {
      $error = $stmt->errorInfo();
      throw new LocalistoException($error[2]);
    }
    
    // and now add each one of them again
    
    $sort_order = 10;    
    
    foreach ($agency_list as $agency_id)
    {
      $stmt = $this->pdo->prepare('insert into user_agency (user_id, agency_id, position) values (:user_id, :agency_id, :sort_order)');
      $result = $stmt->execute(array('agency_id' => $agency_id, 'user_id' => $this->user_id, 'sort_order' => $sort_order));

      if ($result === FALSE)
      {
        $error = $stmt->errorInfo();
        throw new LocalistoException($error[2]);
      }
      $sort_order += 10; // for the new agency
    }

    $this->pdo->commit();
    return array('status' => 'ok');
  }

  /**
   *
   */
  public function action_user_project_map_list($data)
  {
    $stmt = $this->pdo->prepare('select id, title, coordinates, location, meeting_time from project where coordinates is not null and meeting_starts is not null');
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results === FALSE)
    {
      $error = $stmt->errorInfo();
      throw new LocalistoException($error[2]);
    }

    $projects = array();
    foreach ($results as $result)
    {
      $projects[] = array('id' => $result['id'],
                          'name' => $result['title'],
                          'coords' => $result['coordinates'],
                          'location' => $result['location'],
                          'meeting_datetime' => $result['meeting_time']
                        );
    }
    return array('status' => 'ok', 'projects' => $projects);
  }
  
  /**
   *
   */
  public function action_user_project_detail($data)
  {
    if (!isset($data['id']))
    {
      throw new LocalistoException('Invalid request');
    }
    
    $project_id = validate_int($data['id']);

    $stmt = $this->pdo->prepare('select id, title, location, coordinates, meeting_time, survey_closes, description, fb_page_url from project where id = :project_id limit 1');
    $stmt->execute(array('project_id' => $project_id));

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result === FALSE)
    {
      $error = $stmt->errorInfo();
      throw new LocalistoException($error[2]);
    }

    $project = array('id' => $result['id'],
                      'name' => $result['title'],
                      'meeting_datetime' => $result['meeting_time'],
                      'survey_closes' => $result['survey_closes'] ? ios_date_format(strtotime($result['survey_closes'])) : null,
		      'coords' => $result['coordinates'],
                      'location' => $result['location'],
                      'description' => $result['description'],
                      'fb_page_url' => $result['fb_page_url'],
                      'user_took_poll' => $this->user_answered_poll($project_id),
                    );
                    
    // step 2: get images
    $stmt = $this->pdo->prepare('select position, image_url from project_image where project_id = :project_id order by position');
    $stmt->execute(array('project_id' => $project_id));

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results === FALSE)
    {
      $error = $stmt->errorInfo();
      throw new LocalistoException($error[2]);
    }

    $images = array();
    foreach ($results as $result)
    {
      $sort_order = $result['position'];
      $images[$sort_order] = $result['image_url'];
    }
    $project['images'] = $images;

    return array('status' => 'ok', 'project' => $project);
  }  

  /**
   *
   */ 
  public function action_fb_account_create_or_login($data)
  {
    if (!isset($data['email']) || !isset($data['gender']) || !isset($data['location']) || !isset($data['hometown']) || !isset($data['birthday']) || !isset($data['education']))
    {
      throw new LocalistoException('Invalid request');
    }

    $email = validate_email($data['email']);
    $gender = validate_string($data['gender']);
    $location = validate_string($data['location']);
    $hometown = validate_string($data['hometown']);
    $birthday = validate_string($data['birthday']);
    $education = validate_string($data['education']);
    $fb_access_token = validate_string($data['fb_access_token']);
    $fb_id = validate_string($data['fb_id']);

    if (!$email)
    {
      return array('status' => 'error', 'error_message' => 'Invalid email.', 'affected_fields' => array('email'));
    }

    $this->validate_fb_token($fb_access_token, $fb_id);

    $stmt = $this->pdo->prepare('select count(*) from user where email = :email and fb_account = 1');
    $stmt->execute(array('email' => $email));
    if ($stmt->fetchColumn() == 0)
    {
      // account needs to be created
      $stmt = $this->pdo->prepare('insert into user (username, email, fb_account, fb_gender, fb_location, fb_hometown, fb_birthday, fb_education) values (:email, :email, 1, :fb_gender, :fb_location, :fb_hometown, :fb_birthday, :fb_education)');
      $status = $stmt->execute(array('email' => $email,
                                      'fb_gender' => $gender,
                                      'fb_location' => $location,
                                      'fb_hometown' => $hometown,
                                      'fb_birthday' => $birthday,
                                      'fb_education' => $education,
                                      ));
      if ($status !== TRUE)
      {
        $error = $stmt->errorInfo();
        throw new LocalistoException($error[2]);
      }

      $this->user_id = $this->pdo->lastInsertId();

      $this->populate_agencies($this->user_id);
      
      $this->sendConfirmationEmail($email);
    }
    else
    {
      // update account details
      $stmt = $this->pdo->prepare('update user set fb_gender = :fb_gender, fb_location = :fb_location, fb_hometown = :fb_hometown, fb_birthday = :fb_birthday, fb_education = :fb_education where email = :email and fb_account = 1');
      $status = $stmt->execute(array('email' => $email,
                                      'fb_gender' => $gender,
                                      'fb_location' => $location,
                                      'fb_hometown' => $hometown,
                                      'fb_birthday' => $birthday,
                                      'fb_education' => $education,
                                      ));
      if ($status !== TRUE)
      {
        $error = $stmt->errorInfo();
        throw new LocalistoException($error[2]);
      }
    }
    $login_token = $this->create_session($email, 1);
 
    return array('status' => 'ok', 'login_token' => $login_token);
  }

  /**
   *
   */
  public function action_project_poll_questions($data)
  {
    if (!isset($data['id']))
    {
      throw new LocalistoException('Invalid request');
    }
   
    $project_id = validate_int($data['id']);

    $stmt = $this->pdo->prepare('select id, qtype, image_url, description, aoi_id, position from question where project_id = :project_id order by position');
    $stmt->execute(array('project_id' => $project_id));

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results === FALSE)
    {
      $error = $stmt->errorInfo();
      throw new LocalistoException($error[2]);
    }

    $questions = array();
    foreach ($results as $result)
    {
      $type = $result['qtype'];
      $question = array('type' => $type);
      
      if (in_array($type, array(1,2)))
      {
        $question['id'] = $result['id'];
        $question['question'] = $result['description'];
        
        $stmt = $this->pdo->prepare('select id, image_url, description, position from answer where question_id = :question_id order by position');
        $stmt->execute(array('question_id' => $result['id']));
        $answer_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($answer_results === FALSE)
        {
          $error = $stmt->errorInfo();
          throw new LocalistoException($error[2]);
        }
        
        $answers = array();
        foreach ($answer_results as $answer_result)
        {
          if ($type == 1)
          {
            $answers[$answer_result['id']] = $answer_result['description'];
          }
          else
          {
            $answers[$answer_result['id']] = $answer_result['image_url'];            
          }
        }
        $question['answers'] = $answers;
      }
      
      if ($type == 1)
      {
        $question['image'] = $result['image_url'];
      }
      
      if ($type == 3)
      {
        $question['aoi_question_id'] = $result['aoi_id'];
      }
      
      $questions[$result['position']] = $question;
    }

    return array('status' => 'ok', 'poll_details' => $questions);
  }

  /**
   *
   */
  public function action_user_poll_submit($data)
  {
    if (!isset($data['id']) || !isset($data['answers']))
    {
      throw new LocalistoException('Invalid request');
    }
   
    $project_id = validate_int($data['id']);

    if ($this->user_answered_poll($project_id))
    {
      throw new LocalistoException('User already answered this poll', 'poll_error');
    }

    $answers = $this->validate_answers($data['answers'], $project_id);

    $this->pdo->beginTransaction();

    foreach ($answers as $answer)
    {
      $stmt = $this->pdo->prepare('insert into user_answer (user_id, answer_id) values (:user_id, :answer_id)');
      $status = $stmt->execute(array('user_id' => $this->user_id, 'answer_id' => $answer));
      
      if ($status !== TRUE)
      {
        $error = $stmt->errorInfo();
        throw new LocalistoException($error[2]);
      }
    }
    
    $this->pdo->commit();
    
    return array('status' => 'ok');
  }
  
  /**
   *
   */
  public function action_user_clear_polls($data)
  {
    $stmt = $this->pdo->prepare('delete from user_answer where user_id = :user_id');
    $status = $stmt->execute(array('user_id' => $this->user_id));
    
    if ($status !== TRUE)
    {
      $error = $stmt->errorInfo();
      throw new LocalistoException($error[2]);
    }
    
    return array('status' => 'ok');
  }
  
  /**
   *
   */
  public function action_project_answers($data)
  {
    if (!isset($data['id']))
    {
      throw new LocalistoException('Invalid request');
    }
   
    $project_id = validate_int($data['id']);

    $stmt = $this->pdo->prepare('select id, qtype, image_url, description, aoi_id, position from question where project_id = :project_id order by position');
    $stmt->execute(array('project_id' => $project_id));

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results === FALSE)
    {
      $error = $stmt->errorInfo();
      throw new LocalistoException($error[2]);
    }

    $questions = array();
    foreach ($results as $result)
    {
      $type = $result['qtype'];
      $question = array('type' => $type);
      
      if (in_array($type, array(1,2)))
      {
        $question['id'] = $result['id'];
        $question['question'] = $result['description'];
        
        $stmt = $this->pdo->prepare('select a.id, a.image_url, a.description, a.position, count(ua.id) as score from answer a left join user_answer ua on (ua.answer_id = a.id) where a.question_id = :question_id group by a.id order by score desc');
        $stmt->execute(array('question_id' => $result['id']));
        $answer_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($answer_results === FALSE)
        {
          $error = $stmt->errorInfo();
          throw new LocalistoException($error[2]);
        }
        
        $total_score = 0;
        foreach ($answer_results as $answer_result)
        {
          $total_score += $answer_result['score'];
        }
        
        $answers = array();
        foreach ($answer_results as $answer_result)
        {
          $answer = array('score' => round($answer_result['score'] / $total_score * 100, 1));
          if ($type == 1)
          {
            $answer['answer'] = $answer_result['description'];
          }
          else
          {
            $answer['answer'] = $answer_result['image_url'];            
          }
          $answers[$answer_result['id']] = $answer;
        }
        $question['answers'] = $answers;
      }
      
      if ($type == 1)
      {
        $question['image'] = $result['image_url'];
      }
      
      if ($type == 3)
      {
        $question['aoi_question_id'] = $result['aoi_id'];
      }
      
      $questions[$result['position']] = $question;
    }

    return array('status' => 'ok', 'poll_answers' => $questions);
  }
  
  // end of actions
  
  /**
   *
   */
  private function create_session($email, $fb_account)
  {
    $stmt = $this->pdo->prepare('insert into session (user_id, login_token, created_at) values ((select id from user where email = :email and fb_account = :fb_account), :login_token, now())');
    $login_token = create_login_token();
    $status = $stmt->execute(array('email' => $email, 'login_token' => $login_token, 'fb_account' => $fb_account));

    if ($status !== TRUE)
    {
      $error = $stmt->errorInfo();
      throw new LocalistoException($error[2]);
    }

    return $login_token;
  }
  
  /**
   *
   */
  private function populate_agencies($user_id)
  {
    $stmt = $this->pdo->prepare('select * from agency where included_by_default = 1');
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results === FALSE)
    {
      $error = $stmt->errorInfo();
      throw new LocalistoException($error[2]);
    }
    
    foreach ($results as $result)
    {
      $stmt = $this->pdo->prepare('insert into user_agency (user_id, agency_id, position) values (:user_id, :agency_id, :sort_order)');
      if ($stmt->execute(array('user_id' => $user_id,
                              'agency_id' => $result['id'],
                              'sort_order' => $result['position'])) === FALSE)
      {
        $error = $stmt->errorInfo();
        throw new LocalistoException($error[2]);
      }
    }
  }
  
  /**
   *
   */
  private function validate_fb_token($fb_access_token, $fb_id)
  {
    $data = json_decode(file_get_contents('https://graph.facebook.com/me?access_token=' . $fb_access_token), true);
    
    if (empty($data))
    {
      throw new LocalistoException('No response when communicating with Facebook\'s API');
    }
    elseif (isset($data['error']))
    {
      throw new LocalistoException($data['error']['message']);
    }
    elseif (isset($data['id']))
    {
      if ($data['id'] != $fb_id)
      {
        throw new LocalistoException('The fb_id returned by the token and the id passed by the app don\'t match');
      }
      else
      {
        return true;
      }
    }
    else
    {
      throw new LocalistoException('Unknown error while contacting FB API');
    }
  }
  
  /**
   * modified version of validate_int_array
   */
  private function validate_answers($answers, $project_id)
  {
    $filtered_array = array();
    foreach ($answers as $question_id => $answer_id)
    {
      $new_question_id = validate_int($question_id);
      $new_answer_id = validate_int($answer_id);
      
      if ($new_question_id === FALSE || $new_answer_id === FALSE)
      {
        throw new LocalistoException('Invalid integer');
      }

      
      // make sure each question belongs to the given project...
      $stmt = $this->pdo->prepare('select count(*) from question where id = :question_id and project_id = :project_id and qtype in (1,2)');
      $stmt->execute(array('project_id' => $project_id, 'question_id' => $new_question_id));
      if ($stmt->fetchColumn() == 0)
      {
        throw new LocalistoException('Invalid question', 'poll_error');
      }

      
      // ...and each answer is for the given question
      $stmt = $this->pdo->prepare('select count(*) from answer where id = :answer_id and question_id = :question_id');
      $stmt->execute(array('answer_id' => $new_answer_id, 'question_id' => $new_question_id));

      if ($stmt->fetchColumn() == 0)
      {
        throw new LocalistoException('Invalid answer', 'poll_error');
      }
      
      $filtered_array[$new_question_id] = $new_answer_id;
    }
    return $filtered_array;    
  }
  
  /**
   *
   */
  private function user_answered_poll($project_id)
  {
    $stmt = $this->pdo->prepare('
    select
      count(*)
    from
      user_answer ua
      join answer a on (ua.answer_id = a.id)
      join question q on (a.question_id = q.id)
    where
      q.project_id = :project_id
      and ua.user_id = :user_id');
      
    $stmt->execute(array('project_id' => $project_id, 'user_id' => $this->user_id));
    
    return ($stmt->fetchColumn() > 0);
  }
  
  /**
   *
   */
  private function sendConfirmationEmail($email)
  {
    require_once('phpmailer/class.phpmailer.php');
    require_once('phpmailer/class.smtp.php');

    global $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_from;

    $mail = new PHPMailer();

    $mail->IsSMTP();  // telling the class to use SMTP
    $mail->SMTPAuth = true;
    $mail->Host     = $smtp_host; // SMTP server
    $mail->Port = $smtp_port;
    $mail->Username = $smtp_username;
    $mail->Password = $smtp_password;
    $mail->SMTPSecure = 'tls';

    $mail->FromName     = 'Localisto';
    $mail->From     = $smtp_from;
    $mail->AddAddress($email);

    $mail->Subject  = "Welcome to Localisto!";
    $mail->Body     = "Hello " . $email . ",
    
Welcome to Localisto where you can find, follow and contribute to local projects.

We hope you enjoy making your voice heard on projects in your community, from anywhere.

Do you have fresh ideas on how to improve our app? Simply reply to this email and share your brilliance! Additionally, we'd love to include any community-based events important to you, so just shoot us an email with details.

Enjoy!

Jackie Gow
Co-founder of Localisto
Localisto.org";
    // $mail->WordWrap = 80;

    if (!$mail->Send())
    {
      throw new LocalistoException('Mailer error: ' . $mail->ErrorInfo);
    }
    
    return true;
  }
}