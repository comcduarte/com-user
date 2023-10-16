<?php
namespace User\Form\Factory;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Form\UserLoginForm;

class UserLoginFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $form = new UserLoginForm();
        return $form;
    }
}