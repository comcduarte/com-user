<?php
namespace User\View\Helper;

use Laminas\Db\Adapter\AdapterAwareTrait;
use Laminas\View\Helper\AbstractHelper;
use User\Model\UserModel;
use Exception;

/**
 * This view helper is used for retrieving the User entity of currently logged in user.
 */
class CurrentUser extends AbstractHelper 
{
    use AdapterAwareTrait;
    
    /**
     * Authentication service.
     */
    private $authService;
    
    /**
     * Previously fetched User entity.
     */
    private $user = null;
    
    /**
     * Constructor. 
     */
    public function __construct($authService) 
    {
        $this->authService = $authService;
    }
    
    /**
     * Returns the current User or null if not logged in.
     * @param bool $useCachedUser If true, the User entity is fetched only on the first call (and cached on subsequent calls).
     */
    public function __invoke($useCachedUser = true)
    {
        // Check if User is already fetched previously.
        if ($useCachedUser && $this->user!==null)
            return $this->user;
        
        // Check if user is logged in.
        if ($this->authService->hasIdentity()) {
            
            // Fetch User entity from database.
            $this->user = new UserModel($this->adapter);
            $this->user->read(['USERNAME' => $this->authService->getIdentity()]);
                        
            if ($this->user==null) {
                // Oops.. the identity presents in session, but there is no such user in database.
                // We throw an exception, because this is a possible security problem. 
                throw new Exception('Not found user with such ID');
            }
            
            // Return the User entity we found.
            return $this->user;
        }
        
        return null;
    }
}