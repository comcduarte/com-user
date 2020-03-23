<?php
namespace User\Authentication\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Authentication\AuthAdapter;

class AuthAdapterFactory implements FactoryInterface
{
    public function __invoke (ContainerInterface $container, $requestedName, array $options = null)
    {
        $dbAdapter = $container->get('user-model-adapter');
        
        $adapter = new AuthAdapter();
        $adapter->setDbAdapter($dbAdapter);
        
        return $adapter;
    }
}