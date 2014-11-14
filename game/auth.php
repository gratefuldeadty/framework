<?php

use core\functions\PasswordSecurity;

class Auth
{
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
        
        elseif (!checkBrute($ip))
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
            
            // 
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
            
            $query = $this->dbh->prepare('INSERT INTO `failed_logins` (`ip`,`time`) VALUES (?,?)');
            $query->execute(array($ip, $time));
            Session::setArr('feedback_negative', 'Error: The password you entered was incorrect.');
            return false;
        }
    }

    /**
     * Checks for failed login attempts bases off IP & time (10 minutes)
     * @param string $ip
     * @return bool
     */
    public function checkBrute($ip, $time)
    {
        //$time = time() - 1 * 60;
        $query = $this->dbh->prepare('SELECT `ip` FROM `failed_logins` WHERE `ip` = ?
            AND `time` > ?');
        $query->execute(array($ip, $time));
        return ($query->rowCount() >= 3) ? false : true;
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


    public function userSession(array $data)
    {
        foreach ($data as $key => $value)
        {
            Session::set($key, $value);
        }
    }
