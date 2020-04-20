<?php
namespace User\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Storage\Session;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AuthenticationServiceFactory implements FactoryInterface
{
    public function __invoke (ContainerInterface $container, $requestedName, array $options = null)
    {
        $storage = new Session();
        $adapter = $container->get('auth-adapter');

        return new AuthenticationService($storage, $adapter);
    }
}