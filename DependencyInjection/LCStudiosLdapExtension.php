<?php

namespace LCStudios\LdapBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @author Robin Gloster <robin.gloster@lcstudios.de>
 */
class LCStudiosLdapExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('lcstudios_ldap.host', $config['host']);
        $container->setParameter('lcstudios_ldap.port', $config['port']);
        $container->setParameter('lcstudios_ldap.uid', $config['uid']);
        $container->setParameter('lcstudios_ldap.base_dn', $config['base_dn']);
        $container->setParameter('lcstudios_ldap.authenticated_role', $config['authenticated_role']);
        $container->setParameter('lcstudios_ldap.bind_user.dn', $config['bind_user']['dn']);
        $container->setParameter('lcstudios_ldap.bind_user.password', $config['bind_user']['password']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
