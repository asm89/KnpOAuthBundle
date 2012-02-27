<?php

/*
 * This file is part of the KnpOAuthBundle package.
 *
 * (c) KnpLabs <hello@knplabs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\OAuthBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\DefinitionDecorator,
    Symfony\Component\DependencyInjection\Reference,
    Symfony\Component\Config\Definition\Builder\NodeDefinition;

/**
 * OAuthFactory
 *
 * @author Geoffrey Bachelet <geoffrey.bachelet@gmail.com>
 * @author Alexander <iam.asm89@gmail.com>
 */
class OAuthFactory extends AbstractFactory
{
    /**
     * Gets the reference to the appropriate resource owner service.
     *
     * @param array $id
     *
     * @return Reference
     */
    protected function getResourceOwnerId($id)
    {
        if (false !== strpos($id, '.')) {
            return $id;
        }

        return 'knp_oauth.resource_owner.'.$id;
    }

    /**
     * Creates a resource owner map for the given configuration.
     *
     * @param ContainerBuilder $container Container to build for
     * @param string           $id        Firewall id
     * @param array            $config    Configuration
     */
    protected function createResourceOwnerMap(ContainerBuilder $container, $id, array $config)
    {
        $ownerMapDefinition = $container
            ->register($this->getResourceOwnerMapReference($id), '%knp_oauth.resource_ownermap.class%')
            ->addArgument(new Reference('service_container'))
            ->addArgument(new Reference('security.http_utils'));

        foreach ($config['resource_owners'] as $resourceOwner) {
            $ownerMapDefinition->addMethodCall('addResourceOwner', array($this->getResourceOwnerId($resourceOwner['service']), $resourceOwner));
        }
    }

    /**
     * Gets a reference to the resource owner map.
     *
     * @param string $id
     *
     * @return Reference
     */
    protected function getResourceOwnerMapReference($id)
    {
        return new Reference('knp_oauth.resource_ownermap.'.$id);
    }

    /**
     * {@inheritDoc}
     */
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $providerId = 'knp_oauth.authentication.provider.oauth.'.$id;

        $this->createResourceOwnerMap($container, $id, $config);

        $container
            ->setDefinition($providerId, new DefinitionDecorator('knp_oauth.authentication.provider.oauth'))
            ->addArgument(new Reference($userProviderId))
            ->addArgument($this->getResourceOwnerMapReference($id));

        return $providerId;
    }

    /**
     * {@inheritDoc}
     */
    protected function createEntryPoint($container, $id, $config, $defaultEntryPoint)
    {
        $entryPointId = 'knp_oauth.authentication.entry_point.oauth.'.$id;

        $entryPointDefinition = $container
            ->setDefinition($entryPointId, new DefinitionDecorator('knp_oauth.authentication.entry_point.oauth'))
            ->addArgument(new Reference('security.http_utils'))
            ->addArgument($config['login_path']);

        // Inject the resource owners directly if there is only one
        if (1 === count($config['resource_owners'])) {
            $entryPointDefinition
                ->addArgument(new Reference($this->getResourceOwnerId($config['resource_owners'][0]['service'])))
                ->addArgument($config['resource_owners'][0]['check_path']);
        }

        return $entryPointId;
    }

    /**
     * {@inheritDoc}
     */
    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = parent::createListener($container, $id, $config, $userProvider);

        $checkPaths = array();
        foreach ($config['resource_owners'] as $resourceOwner) {
            $checkPaths[] = $resourceOwner['check_path'];
        }

        $container->getDefinition($listenerId)
            ->addMethodCall('setResourceOwnerMap', array($this->getResourceOwnerMapReference($id)))
            ->addMethodCall('setCheckPaths', array($checkPaths));

        return $listenerId;
    }

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
        parent::addConfiguration($node);

        $builder = $node->children();

        $builder
            ->arrayNode('resource_owners')
                ->isRequired()
                ->prototype('array')
                    ->children()
                        ->scalarNode('service')
                            ->isRequired()
                        ->end()
                        ->scalarNode('check_path')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
                ->validate()
                    ->ifTrue(function($c) {
                        $checkPaths = array();
                        foreach ($c as $resourceOwner) {
                            if (in_array($resourceOwner['check_path'], $checkPaths)) {

                                return true;
                            }

                            $checkPaths[] = $resourceOwner['check_path'];
                        }

                        return false;
                    })
                    ->thenInvalid("Each resource owner should have a unique check_path.")
                ->end()
            ->end()
            ->scalarNode('login_path')
                ->cannotBeEmpty()
                ->isRequired()
            ->end();
    }

    /**
     * {@inheritDoc}
     */
    protected function getListenerId()
    {
      return 'knp_oauth.authentication.listener.oauth';
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return 'oauth';
    }

    /**
     * {@inheritDoc}
     */
    public function getPosition()
    {
        return 'http';
    }
}
