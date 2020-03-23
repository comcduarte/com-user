<?php 
namespace User\Authentication;

use Laminas\Authentication\Result;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\Adapter\AdapterAwareTrait;
use User\Model\UserModel;

class AuthAdapter implements AdapterInterface
{
    use AdapterAwareTrait;
    
    private $username;
    private $password;
    
    public function authenticate()
    {
        $user = new UserModel($this->adapter);
        $user->read(['USERNAME' => $this->username]);
        
        /**
         * Return error if user is not found
         */
        if ($user == null) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null, ['Invalid Credentials']);
        }
        
        /**
         * Return error if user has be deactivated
         */
        if ($user->STATUS == UserModel::INACTIVE_STATUS) {
            return new Result(Result::FAILURE, null, ['User is inactive']);
        }
        
        /**
         * Check password for active users
         */
        $bcrypt = new Bcrypt();
        $passwordHash = $user->PASSWORD;
        
        if ($bcrypt->verify($this->password, $passwordHash)) {
            return new Result(Result::SUCCESS, $user->USERNAME, ['Authenticated Successfully']);
        }
        
        /**
         * Return error if password did not verify
         */
        return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['Invalid Credentials']);
    }
    
    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }
}