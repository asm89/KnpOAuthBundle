<?php

/*
 * This file is part of the KnpOAuthBundle package.
 *
 * (c) KnpLabs <hello@knplabs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\OAuthBundle\Security\Http\OAuth\Response;

/**
 * UserResponse
 */
abstract class AbstractUserResponse implements UserResponseInterface
{
    protected $response;

    public function getResponse()
    {
        return $this->Response;
    }

    public function setResponse($response)
    {
        $this->response = json_decode($response, true);
    }
}
