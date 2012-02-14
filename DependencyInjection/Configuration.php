<?php

/*
 * This file is part of the FOSOAuthServerBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\OAuthServerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fos_oauth_server');

        $rootNode
            ->children()
                ->scalarNode('db_driver')
                    ->cannotBeOverwritten()
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->validate()->ifNotInArray(array('doctrine'))->thenInvalid('The following values are allowed: doctrine')->end()
                ->end()
                ->arrayNode('doctrine')
                    ->children()
                        ->arrayNode('classes')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('access_token')->defaultValue('FOS\OAuthServerBundle\Model\AccessToken')->end()
                                ->scalarNode('authorization_code')->defaultValue('FOS\OAuthServerBundle\Model\AuthorizationCode')->end()
                                ->scalarNode('client')->defaultValue('FOS\OAuthServerBundle\Model\Client')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('oauth2')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('access_ranges')
                            ->beforeNormalization()
                                ->ifString()
                                ->then(function($v) { return explode(' ', $v); })
                            ->end()
                            ->prototype('scalar')
                                ->validate()->always(function($v) {
                                    if (false !== strpos($v, ' ')) {
                                        throw new \Exception('Access Ranges MUST NOT contain spaces.');
                                    }

                                    return $v;
                                })->end()
                            ->end()
                        ->end()
                        ->scalarNode('access_token_lifetime')->defaultNull()->end()
                        ->scalarNode('authorization_code_lifetime')->defaultValue(30)->end()
                        ->scalarNode('access_denied_lifetime')->defaultValue(30)->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

