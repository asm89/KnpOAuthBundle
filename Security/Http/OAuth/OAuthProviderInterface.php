<?php

/*
 * This file is part of the KnpOAuthBundle package.
 *
 * (c) KnpLabs <hello@knplabs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\OAuthBundle\Security\Http\OAuth;

use Symfony\Component\HttpFoundation\Request;

/**
 * OAuthProviderInterface
 *
 * @author Geoffrey Bachelet <geoffrey.bachelet@gmail.com>
 */
interface OAuthProviderInterface
{
    /**
     * Retrieves the user's username from an access_token
     *
     * @param string $accessToken
     * @return string The username
     */
    function getUsername($accessToken);

    /**
     * Returns the provider's authorization url
     *
     * @param array $extraParameters An array of parameters to add to the url
     * @param string $redirectUri Optional redirect uri
     *
     * @return string The authorization url
     */
    function getAuthorizationUrl(array $extraParameters = array(), $redirectUri = null, )

    /**
     * Retrieve an access token for a given code
     *
     * @param string $code The code
     * @param array $extraParameters An array of parameters to add to the url
     * @param string $redirectUri Optional redirect uri
     *
     * @return string The access token
     */
    function getAccessToken($code, array $extraParameters = array(), $redirectUri = null)
}
