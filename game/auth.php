<?php

class Auth
{
    protected $verify = false;
    public function construct(Database $dbh) 
    {
        $this->dbh = $dbh; // Database handler.
    }
    
    /**
     * Checks if a username exists in the database.
     * @param string $username
     * @return fetch : false
     */
    public function checkUsername($username)
    {
        $query = $this->dbh->prepare('SELECT `username`,`id`,`password` FROM `users`
            WHERE `username` = ?');
        $query->execute(array(
            $username
            ));
        return ($query->rowCount() > 0) ? $query->fetch() : false;
    }
    
    /**
     * Perform the login.
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function login($username, $password, $ip)
    {
        $time = time() - 1 * 60;
        if (!isset($username, $password, $ip))
        {
            Session::setArr('feedback_negative', 'Error: You must fill all fields of the form!');
            return false;
        }
        
        elseif (!checkUsername($username))
        {
            Session::setArr('feedback_negative', 'Error: The username you entered does not exist.');
            return false;
        }
        
        elseif (!checkBrute($ip, $time, 'check'))
        {
            Session::setArr('feedback_negative', 'Error: Too many failed login attempts.');
            return false;
        }
 
        elseif (password_verify($userPass, $password))
        {
            $user = $this->checkUsername($username);
            $userid = $user['id'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            session_regenerate_id(true);
    
            // Set the users sessions.
            Session::userSession([
                    'logged_in' => true,
                    'userid' => $userid,
                    'userAgent' => $userAgent,
                    'count' => 5,
                ]);
                return true;
        }
 
        else
        {
            $this->checkBrute($ip, $time, 'log');
            Session::setArr('feedback_negative', 'Error: The password you entered was incorrect.');
            return false;
        }
    }

    /**
     * Checks for failed login attempts bases off IP & time (10 minutes)
     * @param string $ip
     * @return bool
     */
    public function checkBrute($ip, $time, $type)
    {
        if ($type == 'check')
        {
            $query = $this->dbh->prepare('SELECT `ip` FROM `failed_logins` WHERE `ip` = ?
                AND `time` > ?');
            $query->execute(array($ip, $time));
            return ($query->rowCount() >= 3) ? false : true;
        }
        elseif ($type == 'log')
        {
            $query = $this->dbh->prepare('INSERT INTO `failed_logins` (`ip`,`time`) VALUES (?,?)');
            $query->execute(array($ip, $time));
        }
    }
    
    public function register($username, $password, $verifyPass, $email, $ip)
    {

        if (!isset($username, $password, $verifyPass, $email, $ip))
        {
            Session::setArr('feedback_negative', 'Error: You must fill out all fields of the form.');
            return false;
        }
        
        elseif (strlen($username) < 2 OR strlen($username) > 40)
        {
            Session::setArr('feedback_negative', 'Error: Username may be 2-40 characters in length.')
            return false;
        }

        elseif (checkUsername($username) == true)
        {
            Session::setArr('feedback_negative', 'Error: The username you entered already exists.');
            return false;
        }
        
        elseif (!preg_match('/^[\w-]+$/', $username)) // 'word' characters only ('/^[a-zA-Z0-9]+$/') ('/[^\w-.]/')
        {
            Session::setArr('feedback_negative', 'Error: There was invalid characters in the username you entered.');
            return false;
        }
        
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            Session::setArr('feedback_negative', 'Error: Invalid email format.');
            return false;
        }
        
        elseif ($password === $verifyPass)
        {
            $password = password_hash($password);
            $verified = ($this->verify === false) ? 1 : 0;
            $query = $this->dbh->prepare('INSERT INTO `users` 
                (`username`,`password`,`email`,`ip`, `verified`) VALUES (?,?,?,?,?)');
            $query->execute(array($username, $password, $email, $ip, $verified));
            return true;
        }
        else
        {
            Session::setArr('feedback_negative', 'Error: The passwords you entered did not verify.');
            return false;
        }
    }
    
    
    public function urlEscape($url)
    {
        if ($url == '')
        {
            return $url;
        }
        $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
        $strip = ['%0d', '%0a', '%0D', '%0A'];
        $url = (string)$url;
        $count = 1;
        while ($count)
        {
            $url = str_replace($strip, '', $url, $count);
        }
        $url = str_replace(';//', '://', $url);
        $url = htmlentities($url);
        $url = str_replace('&amp;', '&#038;', $url);
        $url = str_replace("'", '&#039', $url);
        if ($url[0] !== '/')
        {
            return '';
        }
        else
        {
            return $url;
        }
    }
}

    
// Registration form submit
if (isset($_POST['register']))
{
    $username = isset($_POST['username']);
    $password = isset($_POST['password']);
    $verifyPass = isset($_POST['verifyPassword']);
    $email = isset($_POST['email']);
    $ip = $_SERVER['REMOTE_ADDR'];
    if ($auth->register($username, $password, $verifyPass, $email, $ip))
    {
        $register_complete = 1;
        header('Location: register.php?success=true');
    }
    else
    {
        foreach (Session::get('feedback_negative') as $feedback)
        {
            echo '<div class="feedback negative">'.$feedback.'</div>';
            Session::set('feedback_negative', null);
        }
    }
}

// Registration 
if (isset($_GET['success']))
{
    if ($_GET['success'] == 'true')
    {
        if ($register_complete === 1)
        {
            echo 'You have successfully registered and may now login!!';
        }
        else
        {
            echo 'You must register!';
        }
    }
    else
    {
        echo '404.';
    }
}
