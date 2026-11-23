<?php

declare(strict_types=1);

namespace IdealPostcodes\Symfony;

use IdealPostcodes\Client;
use IdealPostcodes\Contracts\ClientContract;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Registers an `IdealPostcodes\Client` built from the `ideal_postcodes`
 * configuration, autowirable as `Client` or `ClientContract`.
 *
 * Named for Symfony Flex: without a recipe, Flex registers a bundle only
 * when its class name is one it derives from the PSR-4 namespace, and for
 * `IdealPostcodes\Symfony\` that is `IdealPostcodesSymfonyBundle`.
 */
final class IdealPostcodesSymfonyBundle extends AbstractBundle
{
    public const CLIENT_SERVICE = 'ideal_postcodes.client';

    protected string $extensionAlias = 'ideal_postcodes';

    public function configure(DefinitionConfigurator $definition): void
    {
        /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $root */
        $root = $definition->rootNode();
        // One statement per node rather than one fluent chain: Symfony 6.4's
        // `end()` is typed loosely, which would leave the chain as `mixed`.
        $children = $root->children();

        $children->scalarNode('api_key')
            ->info('Your API key (ak_…) or licensee key (sl_…). Use %env(IDPC_API_KEY)%.')
            ->isRequired()
            ->cannotBeEmpty();

        $children->scalarNode('management_key')
            ->info('Your Management Key (uk_…) for key, licensee and config operations. Keep it in an environment variable.')
            ->defaultNull();

        $children->scalarNode('base_uri')
            ->info('The API base URI. Defaults to https://api.ideal-postcodes.co.uk/v1.')
            ->defaultNull();

        $children->scalarNode('http_client')
            ->info('Service id of the PSR-18 client to send requests with. Defaults to Psr\Http\Client\ClientInterface when that service exists, otherwise discovery.')
            ->defaultNull();

        $children->arrayNode('headers')
            ->info('Headers sent with every request, such as a User-Agent naming your application.')
            ->normalizeKeys(false)
            ->useAttributeAsKey('name')
            ->scalarPrototype();
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $services = $container->services();
        $httpClient = is_string($config['http_client'] ?? null)
            ? service($config['http_client'])
            : service(ClientInterface::class)->nullOnInvalid();

        $services->set(self::CLIENT_SERVICE, Client::class)
            ->factory([ClientFactory::class, 'create'])
            ->args([
                $config['api_key'],
                $config['management_key'] ?? null,
                $config['base_uri'] ?? null,
                $config['headers'] ?? [],
                $httpClient,
                service(RequestFactoryInterface::class)->nullOnInvalid(),
                service(StreamFactoryInterface::class)->nullOnInvalid(),
            ]);

        $services->alias(Client::class, self::CLIENT_SERVICE);
        $services->alias(ClientContract::class, self::CLIENT_SERVICE);
    }
}
