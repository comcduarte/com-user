<?php
namespace User\Controller\Plugin;

use Laminas\Db\Adapter\AdapterAwareTrait;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use User\Model\UserModel;
use Exception;

class CurrentUser extends AbstractPlugin
{
    use AdapterAwareTrait;
    
    private $authService;
    private $user;
    
    public function __construct()
    {
        
    }
    
    public function __invoke($useCachedUser = TRUE)
    {
        // Check if User is already fetched previously.
        if ($useCachedUser && $this->user !== NULL)
            return $this->user;
            
            // Check if user is logged in.
            if ($this->authService->hasIdentity()) {
                
                // Fetch User entity from database.
                $this->user = new UserModel($this->adapter);
                $this->user->read(['USERNAME' => $this->authService->getIdentity()]);
                
                if ($this->user === NULL) {
                    // Oops.. the identity presents in session, but there is no such user in database.
                    // We throw an exception, because this is a possible security problem.
                    throw new Exception('Not found user with such ID');
                }
                
                // Return the User entity we found.
                return $this->user;
            }
            
            return 'N/A';
    }
    
    public function getAuthService()
    {
        return $this->authService;
    }

    public function setAuthService($authService)
    {
        $this->authService = $authService;
    }

    
    
}