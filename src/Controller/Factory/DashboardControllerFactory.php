<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Controller\DashboardController;
use Files\Model\FilesModel;

class DashboardControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new DashboardController();
        $adapter = $container->get('user-model-adapter');
        $controller->setFiles($container->get(FilesModel::class));
        $controller->setDbAdapter($adapter);
        return $controller;
    }
}