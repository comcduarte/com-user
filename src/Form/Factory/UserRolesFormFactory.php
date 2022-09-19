<?php
namespace User\Form\Factory;

use Psr\Container\ContainerInterface;
use User\Form\UserRolesForm;

class UserRolesFormFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $form = new UserRolesForm();
        $adapter = $container->get('user-model-adapter');
        $form->setDbAdapter($adapter);
        return $form;
    }
}