<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace LCStudios\LdapBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\FormLoginFactory;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * FormLoginLdapFactory creates services for form login ldap authentication.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 * @author Markus Handschuh <markus.handschuh@mayflower.de>
 */
class FormLoginLdapFactory extends FormLoginFactory
{

    /**
     * @param ContainerBuilder $container
     * @param string $id
     * @param array $config
     * @param string $userProviderId
     * @return string
     */
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $provider = 'lcstudios_ldap.security.authentication.provider.'.$id;
        $container
            ->setDefinition($provider, new DefinitionDecorator('lcstudios_ldap.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProviderId))
            ->replaceArgument(2, $id)
        ;

        return $provider;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return 'form-login-ldap';
    }
}
