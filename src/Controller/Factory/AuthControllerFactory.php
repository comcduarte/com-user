<?php 
namespace User\Controller\Factory;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Controller\AuthController;
use User\Form\UserLoginForm;

class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new AuthController();
        $authentication_form = $container->get('FormElementManager')->get(UserLoginForm::class);
        
        $logger = $container->get('syslogger');
        $controller->setLogger($logger);
        
        $controller->setAuthentication_service($container->get('auth-service'));
        $controller->setAuthentication_form($authentication_form);
        return $controller;
    }
}