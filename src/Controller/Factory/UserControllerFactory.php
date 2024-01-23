<?php
namespace User\Controller\Factory;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Controller\UserController;
use User\Form\UserForm;
use User\Form\UserRolesForm;
use User\Model\UserModel;

class UserControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new UserController();
        
        $adapter = $container->get('user-model-adapter');
        
        $model = new UserModel($adapter);
        $form = $container->get('FormElementManager')->get(UserForm::class);
        
        $logger = $container->get('syslogger');
        $controller->setLogger($logger);
        
        $user_roles_form = $container->get('FormElementManager')->get(UserRolesForm::class);
        $user_roles_form->setDbAdapter($adapter);
        $controller->user_roles_form = $user_roles_form;
        
        $controller->setModel($model);
        $controller->setForm($form);
        $controller->setDbAdapter($adapter);
        return $controller;
    }
}