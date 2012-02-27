<?php

namespace Knp\Bundle\OAuthBundle\Security\Exception;

/**
 * OAuthAwareExceptionInterface
 *
 * @author Geoffrey Bachelet <geoffrey.bachelet@gmail.com>
 * @author Alexander <iam.asm89@gmail.com>
 */
interface OAuthAwareExceptionInterface
{
  /**
   * Set the access token of the failed authentication request.
   *
   * @param string $accessToken
   */
  public function setAccessToken($accessToken);

  /**
   * @return string
   */
  public function getAccessToken();

  /**
   * Set the id of the resource owner responsible for the oauth authentication.
   *
   * @param string $resourceOwnerId
   */
  public function setResourceOwnerId($resourceOwnerId);

  /**
   * @return string
   */
  public function getResourceOwnerId();
}
