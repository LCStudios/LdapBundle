<?php

namespace LCStudios\LdapBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use LCStudios\LdapBundle\Security\Authentication\Token\LdapUserToken;

/**
 * Class LdapListener
 *
 * @author Markus Handschuh <markus.handschuh@mayflower.de>
 */
class LdapListener implements ListenerInterface
{

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * @param GetResponseEvent $event
     * @return null
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        //@todo
        if ($request->headers->has('x-wsse')) {
            $wsseRegex = '/UsernameToken Username="([^"]+)", PasswordDigest="([^"]+)", Nonce="([^"]+)", Created="([^"]+)"/';

            if (preg_match($wsseRegex, $request->headers->get('x-wsse'), $matches)) {
                $token = new LdapUserToken();

                $token->setUser($matches[1]);
                $token->setDigest($matches[2]);
                $token->setNonce($matches[3]);
                $token->setCreated($matches[4]);

                try {
                    $returnValue = $this->authenticationManager->authenticate($token);

                    if ($returnValue instanceof TokenInterface) {
                        $this->securityContext->setToken($returnValue);
                    } elseif ($returnValue instanceof Response) {
                        $event->setResponse($returnValue);
                    }

                    return null;
                } catch (AuthenticationException $e) {
                    // you might log something here
                }
            }
        }

        $response = new Response();
        $response->setStatusCode(403);
        $event->setResponse($response);
    }
}
