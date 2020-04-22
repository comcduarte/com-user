<?php 
namespace User\Form\Factory;

use Psr\Container\ContainerInterface;
use User\Form\RoleForm;

class RoleFormFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $adapter = $container->get('user-model-adapter');
        
        $form = new RoleForm();
        $form->setDbAdapter($adapter);
        return $form;
    }
}