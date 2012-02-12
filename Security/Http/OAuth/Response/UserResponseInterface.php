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
 * UserResponseInterface
 */
interface UserResponseInterface
{
    public function getUsername();
    public function getResponse();
    public function setResponse($response);
}
