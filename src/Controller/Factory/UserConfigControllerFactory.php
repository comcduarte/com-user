<?php 
namespace User\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use User\Controller\UserConfigController;

class UserConfigControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new UserConfigController();
        $adapter = $container->get('user-model-adapter');
        $controller->setDbAdapter($adapter);
        return $controller;
    }
}