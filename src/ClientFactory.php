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
    public const VERSION = '0.1.0'; // x-release-please-version

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
            ->withManagementKey($managementKey);

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

        // A configured User-Agent names the application. The bundle's
        // identifier follows it and the SDK appends its own after that.
        $userAgent = '';

        foreach ($headers as $name => $value) {
            if ($value === null) {
                continue;
            }

            if (strcasecmp($name, 'User-Agent') === 0) {
                $userAgent = trim((string) $value);

                continue;
            }

            $factory->withHttpHeader($name, (string) $value);
        }

        $factory->withHttpHeader('User-Agent', ltrim($userAgent.' ideal-postcodes-symfony-bundle/'.self::VERSION));

        return $factory->make();
    }
}
