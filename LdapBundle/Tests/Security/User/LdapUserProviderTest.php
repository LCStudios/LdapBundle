<?php

namespace Daps\LdapBundle\Tests\Seucrity\User;

use Daps\LdapBundle\Security\User\LdapUserProvider;
use Daps\LdapBundle\Security\Ldap\Exception\ConnectionException;
use Symfony\Component\Security\Core\User\User;


class LdapUserProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException BadMethodCallException
     */
    public function testLoadUserByUsername()
    {
        $ldap = $this->getMock('Symfony\Component\Security\Ldap\LdapInterface');

        $provider = new LdapUserProvider($ldap);
        $user = $provider->loadUserByUsername('foo');
    }

    public function testLoadUserByUsernameAndPasswordOk()
    {
        $ldap = $this->getMock('Symfony\Component\Security\Ldap\LdapInterface');
        $ldap
            ->expects($this->once())
            ->method('setUsername')
        ;
        $ldap
            ->expects($this->once())
            ->method('setPassword')
        ;
        $ldap
            ->expects($this->once())
            ->method('getConnection')
        ;

        $provider = new LdapUserProvider($ldap);
        $user = $provider->loadUserByUsernameAndPassword('foo', 'bar');

        $this->assertInstanceOf('Symfony\Component\Security\Core\User\User', $user);
        $this->assertEquals('foo', $user->getUsername());
        $this->assertEquals('bar', $user->getPassword());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByUsernameAndPasswordNOk()
    {
        $ldap = $this->getMock('Symfony\Component\Security\Ldap\LdapInterface');
        $ldap
            ->expects($this->once())
            ->method('setUsername')
        ;
        $ldap
            ->expects($this->once())
            ->method('setPassword')
        ;
        $ldap
            ->expects($this->once())
            ->method('getConnection')
            ->will($this->throwException(new ConnectionException('baz')))
        ;

        $provider = new LdapUserProvider($ldap);
        $provider->loadUserByUsernameAndPassword('foo', 'bar');
    }
}
