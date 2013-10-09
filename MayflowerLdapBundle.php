<?php

namespace Mayflower\LdapBundle;

use Mayflower\LdapBundle\DependencyInjection\Security\Factory\FormLoginLdapFactory;
use Mayflower\LdapBundle\DependencyInjection\Security\Factory\HttpBasicLdapFactory;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MayflowerLdapBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new FormLoginLdapFactory());
        $extension->addSecurityListenerFactory(new HttpBasicLdapFactory());
    }

}
