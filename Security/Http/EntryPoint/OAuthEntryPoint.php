<?php

/*
 * This file is part of the KnpOAuthBundle package.
 *
 * (c) KnpLabs <hello@knplabs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\OAuthBundle\Security\Http\EntryPoint;

use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Http\HttpUtils,
    Symfony\Component\HttpFoundation\Request;

use Knp\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface;

/**
 * OAuthEntryPoint redirects the user to the appropriate login url if there is
 * only one resource owner. Otherwise the user will be redirected to a login
 * page.
 *
 * @author Geoffrey Bachelet <geoffrey.bachelet@gmail.com>
 * @author Alexander <iam.asm89@gmail.com>
 */
class OAuthEntryPoint implements AuthenticationEntryPointInterface
{
    /**
     * @var Symfony\Component\Security\Http\HttpUtils
     */
    private $httpUtils;

    /**
     * @var Knp\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface
     */
    private $resourceOwner;

    /**
     * @var string
     */
    private $checkPath;

    /**
     * @var string
     */
    private $loginPath;

    /**
     * Constructor
     *
     * @param HttpUtils              $httpUtils
     * @param string                 $loginPath
     * @param ResourceOwnerInterface $resourceOwner
     * @param string                 $checkPath
     */
    public function __construct(HttpUtils $httpUtils, $loginPath, ResourceOwnerInterface $resourceOwner = null, $checkPath = null)
    {
        $this->httpUtils     = $httpUtils;
        $this->loginPath     = $loginPath;
        $this->resourceOwner = $resourceOwner;
        $this->checkPath     = $checkPath;
    }

    /**
     * {@inheritDoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        // redirect to the login url if there are several resource owners
        if (null === $this->resourceOwner) {

            return $this->httpUtils->createRedirectResponse($request, $this->loginPath);
        }

        // otherwise start authentication
        $authorizationUrl = $this->resourceOwner->getAuthorizationUrl(
            $this->httpUtils->createRequest($request, $this->checkPath)->getUri()
        );

        return $this->httpUtils->createRedirectResponse($request, $authorizationUrl);
    }
}
