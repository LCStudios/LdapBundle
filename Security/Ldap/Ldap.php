<?php

namespace LCStudios\LdapBundle\Security\Ldap;

use LCStudios\LdapBundle\Security\Ldap\Exception\ConnectionException;
use LCStudios\LdapBundle\Security\Ldap\Exception\LdapException;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 * @author Francis Besset <francis.besset@gmail.com>
 * @author Robin Gloster <robin@loc-com.de>
 * @author Markus Handschuh <markus.handschuh@mayflower.de>
 */
class Ldap implements LdapInterface
{

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $dn;

    /**
     * @var string
     */
    private $usernameSuffix;

    /**
     * @var int
     */
    private $version;

    /**
     * @var bool
     */
    private $useSsl;

    /**
     * @var bool
     */
    private $useStartTls;

    /**
     * @var bool
     */
    private $optReferrals;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $adminDn;

    /**
     * @var string
     */
    private $adminPassword;

    /**
     * @var string
     */
    private $authenticatedRole;

    /**
     * @var array
     */
    private $boundListing;

    /**
     * @var resource
     */
    private $connection;

    /**
     * contructor
     *
     * @param string $host
     * @param integer $port
     * @param string $dn
     * @param string $usernameSuffix
     * @param string $authenticatedRole
     * @param string $adminDn
     * @param string $adminPassword
     * @param integer $version
     * @param boolean $useSsl
     * @param boolean $useStartTls
     * @param boolean $optReferrals
     * @throws Exception\LdapException
     */
    public function __construct(
        $host              = null,
        $port              = 389,
        $dn                = null,
        $usernameSuffix    = null,
        $authenticatedRole = 'ROLE_USER',
        $adminDn           = null,
        $adminPassword     = null,
        $version           = 3,
        $useSsl            = false,
        $useStartTls       = false,
        $optReferrals      = false
    )
    {
        if (!extension_loaded('ldap')) {
            throw new LdapException('Ldap module is needed.');
        }

        $this->host              = $host;
        $this->port              = $port;
        $this->dn                = $dn;
        $this->usernameSuffix    = $usernameSuffix;
        $this->authenticatedRole = $authenticatedRole;
        $this->adminDn           = $adminDn;
        $this->adminPassword     = $adminPassword;
        $this->version           = $version;
        $this->useSsl            = (boolean) $useSsl;
        $this->useStartTls       = (boolean) $useStartTls;
        $this->optReferrals      = (boolean) $optReferrals;
        $this->connection        = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        if (!$this->connection) {
            $this->connect();
        }

        return $this->connection;
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDn()
    {
        return $this->dn;
    }

    /**
     * {@inheritdoc}
     */
    public function setDn($dn)
    {
        $this->dn = $dn;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsernameSuffix()
    {
        return $this->usernameSuffix;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsernameSuffix($usernameSuffix)
    {
        $this->usernameSuffix = $usernameSuffix;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUseSsl()
    {
        return $this->useSsl;
    }

    /**
     * {@inheritdoc}
     */
    public function setUseSsl($useSsl)
    {
        $this->useSsl = (boolean) $useSsl;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUseStartTls()
    {
        return $this->useStartTls;
    }

    /**
     * {@inheritdoc}
     */
    public function setUseStartTls($useStartTls)
    {
        $this->useStartTls = (boolean) $useStartTls;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptReferrals()
    {
        return $this->optReferrals;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptReferrals($optReferrals)
    {
        $this->optReferrals = (boolean) $optReferrals;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function findByUsername($username, $query, $filter = '*')
    {
        $listings = $this->findListingsByUsername($username, $query, $filter);
        if (0 === $listings['count']) {
            return null;
        }
        return $listings[0];
    }

    public function findListingsByUsername($username, $query, $filter = '*')
    {
        if (!$this->connection) {
            $this->connect();
        }

        if (!is_array($filter)) {
            $filter = array($filter);
        }

        $query    = $this->getDnAndValue($username);
        $search   = ldap_search($this->connection, $this->usernameSuffix, $query, $filter);
        $listings = ldap_get_entries($this->connection, $search);

        return $listings;
    }

    private function connect()
    {
        if (!$this->connection) {
            $host = $this->getHost();
            if ($this->getUseSsl()) {
                $host = 'ldaps://' . $host;
            }
            $this->connection = ldap_connect($host, $this->getPort());

            ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, $this->getVersion());
            ldap_set_option($this->connection, LDAP_OPT_REFERRALS, $this->getOptReferrals());

            if ($this->getUseStartTls()) {
                $tlsResult = ldap_start_tls($this->connection);
                if (!$tlsResult) throw new ConnectionException('TLS initialization failed!');
            }

            if ($this->adminDn) {
                if ( ($this->adminDn === null) || ($this->adminPassword === null) ) {
                    throw new ConnectionException('Bind user required but credentials not provided. Please add it to your config.');
                }
                if (false === @ldap_bind($this->connection, $this->adminDn, $this->adminPassword)) {
                    throw new ConnectionException('Bind user credentials incorrect. Please review your LDAP configurations.');
                }
            }
        }
        return $this;
    }

    public function bind()
    {
        if (!$this->connection) {
            $this->connect();
        }

        $usernameListings = $this->findListingsByUsername($this->username, $this->usernameSuffix);

        for ( $i=0; $i < $usernameListings['count']; $i++ )
        {
            $listing = $usernameListings[$i];
            if (false !== @ldap_bind($this->connection, $listing['dn'], $this->password)) {
                // we are now bound
                $this->boundListing = $listing;
                return $this;
            }
        }
        // if we got here, we couldn't bind to any of the listings that were provided
        throw new ConnectionException(sprintf('Username or password invalid to connect on Ldap server %s:%s', $this->host, $this->port));
    }

    public function unbind()
    {
        if (is_resource($this->connection)) {
            ldap_unbind($this->connection);
        }

        return $this;
    }

    private function disconnect()
    {
        if ($this->connection && is_resource($this->connection)) {
            $this->unbind();
        }

        $this->boundListing = null;
        $this->connection   = null;

        return $this;
    }

    /**
     * Escapes the given VALUES according to RFC 2254 so that they can be safely used in LDAP filters.
     *
     * Any control characters with an ASCII code < 32 as well as the characters with special meaning in
     * LDAP filters "*", "(", ")", and "\" (the backslash) are converted into the representation of a
     * backslash followed by two hex digits representing the hexadecimal value of the character.
     *
     * @see Net_LDAP2_Util::escape_filter_value() from Benedikt Hallinger <beni@php.net>
     * @link http://pear.php.net/package/Net_LDAP2
     * @author Benedikt Hallinger <beni@php.net>
     *
     * @param $value
     * @return array Array $values, but escaped
     */
    private function escapeValue($value)
    {
        // Escaping of filter meta characters
        $value = str_replace(array('\\', '*', '(', ')'), array('\5c', '\2a', '\28', '\29'), $value);
        // ASCII < 32 escaping
        for ($i = 0; $i < strlen($value); $i++) {
            $char = substr($value, $i, 1);
            if (ord($char) < 32) {
                $hex = dechex(ord($char));
                if (strlen($hex) == 1)
                    $hex = '0' . $hex;
                $value = str_replace($char, '\\' . $hex, $value);
            }
        }
        if (null === $value) {
            $value = '\0'; // apply escaped "null" if string is empty
        }

        return $value;
    }

    public function usernameHasListing($username, $key, $value)
    {
        if (!$this->connection) {
            $this->connect();
        }
        $search = ldap_search($this->connection, $this->usernameSuffix, $this->getDnAndValue($username));
        $infos  = ldap_get_entries($this->connection, $search);

        return (   $infos['count'] > 0
                && isset($infos[0][$key])
                && ($infos[0][$key]['count'] > 0)
                && $value == $infos[0][$key][0]
        );
    }

    private function getFullyQualifiedDN($username = null)
    {
        $username = $username ?: $this->username;
        return $this->getUsernameWithSuffix($this->getDnAndValue($username));
    }

    private function getDnAndValue($username)
    {
        return sprintf('%s=%s', $this->dn, $username);
    }

    public function getUsernameWithSuffix($username = null)
    {
        if (null === $username) {
            $username = $this->username;
        }

        return $username.','.$this->usernameSuffix;
    }

    /**
     * @return array
     */
    public function getBoundRolesByOrgs()
    {
        $boundListing = $this->getBoundListing();
        if ($boundListing === null) {
            $this->bind();
        }

        $memberOfListing    = array_key_exists('memberof', $boundListing) ? $boundListing['memberof'] : array();
        $groupMemberListing = array_key_exists('groupmembership', $boundListing) ? $boundListing['groupmembership'] : array();

        $roles = array_merge(
            array($this->authenticatedRole),
            $this->matchRolesFromGroupListing($memberOfListing),
            $this->matchRolesFromGroupListing($groupMemberListing)
        );

        return array_unique($roles);
    }

    /**
     * @param array $groupListing
     * @return array
     */
    public function matchRolesFromGroupListing(array $groupListing)
    {
        $roles = array();
        foreach ($groupListing as $fullOuGroupListing) {
            $matches = array();
            preg_match_all('/(cn=[^,]+|ou=[^,]+)/i', $fullOuGroupListing, $matches);

            foreach ( $matches[0] as $membership )
            {
                $roles[] = 'ROLE_'.strtoupper(preg_replace('/.*=/', '', $membership));
            }
        }

        return $roles;
    }

    /**
     * @return array
     */
    public function getBoundListing()
    {
        return $this->boundListing;
    }
}
