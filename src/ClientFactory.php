<?php

declare(strict_types=1);

namespace IdealPostcodes\Symfony;

use IdealPostcodes\Client;
use IdealPostcodes\IdealPostcodes;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Builds the SDK client from the bundle configuration. Anything not
 * configured falls back to the SDK's own defaults and discovery.
 *
 * @internal
 */
final class ClientFactory
{
    public const VERSION = '0.0.0'; // x-release-please-version

    /**
     * @param  array<string, scalar|null>  $headers
     */
    public static function create(
        string $apiKey,
        ?string $managementKey = null,
        ?string $baseUri = null,
        array $headers = [],
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
    ): Client {
        $factory = IdealPostcodes::factory()
            ->withApiKey($apiKey)
            ->withManagementKey($managementKey)
            ->withHttpHeader('User-Agent', sprintf('ideal-postcodes-symfony-bundle/%s ideal-postcodes-php-sdk/%s', self::VERSION, IdealPostcodes::VERSION));

        if ($baseUri !== null) {
            $factory->withBaseUri($baseUri);
        }

        if ($httpClient !== null) {
            $factory->withHttpClient($httpClient);
        }

        if ($requestFactory !== null) {
            $factory->withRequestFactory($requestFactory);
        }

        if ($streamFactory !== null) {
            $factory->withStreamFactory($streamFactory);
        }

        foreach ($headers as $name => $value) {
            if ($value !== null) {
                $factory->withHttpHeader($name, (string) $value);
            }
        }

        return $factory->make();
    }
}
