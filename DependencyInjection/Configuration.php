<?php

namespace Openwide\Bundle\ZendCaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('openwide_zend_captcha');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('img_dir')->defaultValue('%kernel.root_dir%/../web/captcha')->end()
            ->scalarNode('font')->defaultValue(__DIR__.'/../Resources/fonts/arial.ttf')->end()
            ->scalarNode('img_url')->defaultValue('/captcha')->end()
            ->scalarNode('suffix')->end()//default Value .png in Zend
            ->scalarNode('width')->end()//default Value 200 in Zend
            ->scalarNode('height')->end()//default Value 50 in Zend
            ->scalarNode('font_size')->end()//default Value 24 in Zend
            ->scalarNode('dot_noise_level')->end()//default Value 100 in Zend
            ->scalarNode('line_noise_level')->end()//default Value 5 in Zend
            ->scalarNode('word_len')->end()//default Value 8 in Zend
            ->scalarNode('expiration')->end()//default Value 600 in Zend
            ->scalarNode('gc_freq')->end()//default Value 10 in Zend
            ->scalarNode('bypass_code')->defaultValue(null)->end()
            ->end();

        return $treeBuilder;
    }
}
