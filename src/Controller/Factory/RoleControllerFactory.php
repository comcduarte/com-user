<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Controller\RoleController;
use User\Form\RoleForm;
use User\Model\RoleModel;

class RoleControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new RoleController();
        
        $adapter = $container->get('user-model-adapter');
        
        $model = new RoleModel($adapter);
        $form = $container->get('FormElementManager')->get(RoleForm::class);
        
        $controller->setModel($model);
        $controller->setForm($form);
        $controller->setDbAdapter($adapter);
        return $controller;
    }
}