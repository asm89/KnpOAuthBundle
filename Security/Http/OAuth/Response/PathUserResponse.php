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
 * Class parsing the properties by given path options.
 */
class PathUserResponse extends AbstractUserResponse
{
    protected $paths;

    public function getUsername()
    {
        $usernamePath = explode('.', $this->paths['username_path']);

        $username     = $this->response;
        foreach ($usernamePath as $path) {
            if (!array_key_exists($path, $username)) {
                throw new AuthenticationException(sprintf('Could not follow username path "%s" in OAuth provider response: %s', $this->getOption('username_path'), var_export($userInfos, true)));
            }
            $username = $username[$path];
        }

        return $username;
    }

    public function getPaths()
    {
        return $this->paths;
    }

    public function setPaths(array $paths)
    {
        $this->paths = $paths;
    }
}
