<?php
namespace User\Controller\Plugin\Factory;

use Psr\Container\ContainerInterface;
use User\Controller\Plugin\CurrentUser;

class CurrentUserFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $authService = $container->get('auth-service');
        $adapter = $container->get('user-model-adapter');
        
        $plugin = new CurrentUser();
        $plugin->setAuthService($authService);
        $plugin->setDbAdapter($adapter);
        
        return $plugin;
    }
}