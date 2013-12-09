<?php

namespace LCStudios\LdapBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @author Robin Gloster <robin@loc-com.de>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lc_studios_ldap');

        $rootNode
            ->children()
                ->arrayNode('bind_user')
                    ->children()
                        ->scalarNode('dn')
                            ->defaultNull()
                            ->info('DN of user to bind to the LDAP server. null if anonymous binding')
                            ->example('cn=ldapread,cn=serviceusers,dc=example,dc=com')
                        ->end()
                        ->scalarNode('password')
                            ->defaultNull()
                            ->info('Password of user to bind to the LDAP server')
                            ->example('readuserpw')
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('host')
                    ->defaultValue('localhost')
                    ->info('LDAP server host')
                ->end()
                ->integerNode('port')
                    ->defaultValue(389)
                    ->info('LDAP server port')
                ->end()
                ->scalarNode('base_dn')
                    ->defaultNull()
                    ->info('Base DN for Users')
                    ->example('cn=users,dc=example,dc=com')
                ->end()
                ->scalarNode('authenticated_role')
                    ->defaultValue('ROLE_USER')
                    ->info('role of all authenticated users additionally to their OUs and groups')
                ->end()
                ->scalarNode('uid')
                    ->defaultValue('uid')
                    ->info('Attribute to use to find user')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
