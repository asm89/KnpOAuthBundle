<?php

/*
 * This file is part of the KnpOAuthBundle package.
 *
 * (c) KnpLabs <hello@knplabs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\OAuthBundle\Security\Core\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\Security\Core\User\UserProviderInterface;

use Knp\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken,
    Knp\Bundle\OAuthBundle\Security\Exception\OAuthAwareExceptionInterface,
    Knp\Bundle\OAuthBundle\Security\Http\ResourceOwnerMap;

/**
 * OAuthProvider
 *
 * @author Geoffrey Bachelet <geoffrey.bachelet@gmail.com>
 * @author Alexander <iam.asm89@gmail.com>
 */
class OAuthProvider implements AuthenticationProviderInterface
{
    /**
     * @var ResourceOwnerMap
     */
    private $resourceOwnerMap;

    /**
     * @var Symfony\Component\Security\Core\User\UserProviderInterface
     */
    private $userProvider;

    /**
     * @param UserProviderInterface $userProvider     User provider
     * @param ResourceOwnerMap      $resourceOwnerMap Resource owner map
     */
    public function __construct(UserProviderInterface $userProvider, ResourceOwnerMap $resourceOwnerMap)
    {
        $this->userProvider  = $userProvider;
        $this->resourceOwnerMap = $resourceOwnerMap;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof OAuthToken;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(TokenInterface $token)
    {
        $resourceOwner = $this->resourceOwnerMap->getResourceOwnerById($token->getResourceOwnerId());

        $username = $resourceOwner
            ->getUserInformation($token->getCredentials())
            ->getUsername();

        try {
            $user = $this->userProvider->loadUserByUsername($username);
        } catch (OAuthAwareExceptionInterface $e) {
            $e->setAccessToken($token->getCredentials());
            $e->setResourceOwnerId($token->getResourceOwnerId());
            throw $e;
        }

        $token = new OAuthToken($token->getCredentials(), $user->getRoles());
        $token->setUser($user);
        $token->setAuthenticated(true);

        return $token;
    }
}
