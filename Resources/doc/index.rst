=============
Documentation
=============

LdapBundle Setup Instructions
=============================

To setup the LdapBundle, follow these steps:

1. Install via composer
2. Ensure you create the ``ldapcredentials.yml`` configuration file and place it in the ``src\Mayflower\LdapBundle\Resources\config`` directory. Use the ``ldapcredentials.example.yml`` file in the same directory as your starting point.
3. Modify ``app/config/security.yml`` and add your ldap user provider
    ::

        security:
            providers:
                mayflower_ldap:
                    id: mayflower_ldap_user_provider

    also tell Symfony how to encode passwords. For example
    ::

        security:
            encoders:
                Mayflower\LdapBundle\Security\User\LdapUser: plaintext

    You can now also ensure that you define the parts of your app that will be under LDAP protection. e.g
    ::

        secured_area:
            pattern:    ^/
            form-login-ldap: true

4. Next, in your ``apps/config/config.yml`` file, import the service
    ::

        imports:
            - { resource: parameters.yml }
            - { resource: security.yml }
            - { resource: "@MayflowerLdapBundle/Resources/config/services.yml"}

5. Setup your ``SecurityController``, routes and templates as detailed in the `Security Chapter <http://symfony.com/doc/current/book/security.html>`_ of the Symfony Documentation.
