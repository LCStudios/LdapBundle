<?php

namespace LCStudios\LdapBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * Class LdapUserToken
 *
 * @author Markus Handschuh <markus.handschuh@mayflower.de>
 */
class LdapUserToken extends AbstractToken
{

    /**
     * @var string
     */
    private $created;

    /**
     * @var string
     */
    private $digest;

    /**
     * @var string
     */
    private $nonce;

    public function __construct(array $roles = array())
    {
        parent::__construct($roles);

        // If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);
    }

    /**
     * @return string
     */
    public function getCredentials()
    {
        return '';
    }

    /**
     * @param string $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $digest
     */
    public function setDigest($digest)
    {
        $this->digest = $digest;
    }

    /**
     * @return string
     */
    public function getDigest()
    {
        return $this->digest;
    }

    /**
     * @param string $nonce
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
    }

    /**
     * @return string
     */
    public function getNonce()
    {
        return $this->nonce;
    }

}
