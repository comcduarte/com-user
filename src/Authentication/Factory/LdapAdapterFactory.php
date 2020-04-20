<?php
namespace User\Authentication\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Authentication\LdapAdapter;

class LdapAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $ldap_adapter = new LdapAdapter();
        $adapter = $container->get('user-model-adapter');
        $ldap_adapter->setDbAdapter($adapter);
        return $ldap_adapter;
    }
}