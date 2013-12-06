<?php

namespace Mayflower\LdapBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @author Robin Gloster <robin.gloster@mayflower.de>
 */
class MayflowerLdapExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('mayflower_ldap.host', $config['host']);
        $container->setParameter('mayflower_ldap.port', $config['port']);
        $container->setParameter('mayflower_ldap.uid', $config['uid']);
        $container->setParameter('mayflower_ldap.base_dn', $config['base_dn']);
        $container->setParameter('mayflower_ldap.authenticated_role', $config['authenticated_role']);
        $container->setParameter('mayflower_ldap.bind_user.dn', $config['bind_user']['dn']);
        $container->setParameter('mayflower_ldap.bind_user.password', $config['bind_user']['password']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
