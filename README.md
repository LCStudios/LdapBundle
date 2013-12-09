====
LCStudiosLdapBundle
====

This bundle is forked from [DapsBundle](https://github.com/relwell/DapsBundle) which is based on the pull request accepted by Symfony2 for LDAP support.
While it has been accepted, some of us would like to use this code sooner than
the version of Symfony natively supporting it will provide.

This project is intended to take these code changes and silo them into a bundle so that
we can easily extend these changes for our own purposes.

The fork fixes some issues in DapsBundle, which needs to be copied into the src folder and does not allow being configured from the central config.
Role handling has been changed, too.

* [Pull request](https://github.com/symfony/symfony/pull/5189/files)
* [Full branch from lyrixx](https://github.com/lyrixx/symfony/compare/master...feat-security-ldap)

Documentation
=============

LdapBundle Setup Instructions
----

To setup the LdapBundle, follow these steps:

1. Install via composer
2. Modify ``app/config/security.yml`` and add your ldap user provider

        security:
            providers:
                lc_studios_ldap:
                    id: lc_studios_ldap_user_provider

    also tell Symfony how to encode passwords. For example

        security:
            encoders:
                LCStudios\LdapBundle\Security\User\LdapUser: plaintext

    You can now also ensure that you define the parts of your app that will be under LDAP protection. e.g

        lc_studios_ldap:
            host: 'ldap://example.com'
            port: 389
            uid: 'uid'
            authenticated_role: 'ROLE_USER'
            base_dn: 'cn=users,dc=example,dc=com'
            bind_user:
                dn: 'cn=ldapbind,cn=serviceusers,dc=example,dc=com'
                password: ldapbinduserpw


    Add your LDAP server specific configs. e.g

        secured_area:
            pattern:    ^/
            form-login-ldap: true

3. Setup your ``SecurityController``, routes and templates as detailed in the [Security Chapter](http://symfony.com/doc/current/book/security.html) of the Symfony Documentation.

4. Add Bundle to AppKernel.

Every authenticated user gets the role defined as 'authenticated_role'. Additionally they get roles determined by OUs and groups, e.g.:
 - ROLE_ADMIN if they are in the OU 'admin'
 - ROLE_MAILUSER if they are in the group mailuser
