# Ideal Postcodes Symfony Bundle

Configure your Ideal Postcodes API key once and inject the [PHP SDK](https://packagist.org/packages/ideal-postcodes/sdk) client into your services. Symfony 6.4, 7 or 8, PHP 8.2 or later.

```bash
composer require ideal-postcodes/symfony-bundle
```

Symfony Flex registers the bundle. Without Flex, add it to `config/bundles.php`:

```php
IdealPostcodes\Symfony\IdealPostcodesSymfonyBundle::class => ['all' => true],
```

```yaml
# config/packages/ideal_postcodes.yaml
ideal_postcodes:
    api_key: '%env(IDPC_API_KEY)%'
    management_key: '%env(IDPC_MANAGEMENT_KEY)%' # only for key, licensee and config operations
```

```php
use IdealPostcodes\Client;

final class CheckoutController
{
    public function __construct(private readonly Client $client) {}

    public function addresses(string $postcode): array
    {
        return $this->client->lookup($postcode)->addresses;
    }
}
```

Inject `IdealPostcodes\Client`, or `IdealPostcodes\Contracts\ClientContract` to swap in a test double. Each API operation is one method on it, such as `$client->addressCleanse($body)`.

Requests go through the `Psr\Http\Client\ClientInterface` service when your application defines one, which `symfony/http-client` does once `nyholm/psr7` is installed, so `framework.http_client` timeouts and retries apply. Otherwise the SDK discovers an installed PSR-18 client.

| Option | Purpose |
| --- | --- |
| `api_key` | Required. Your API key or a licensee key |
| `management_key` | Your Management Key for account operations |
| `base_uri` | Defaults to `https://api.ideal-postcodes.co.uk/v1` |
| `http_client` | Service id of a PSR-18 client to use instead of the default |
| `headers` | Headers sent with every request |

[Documentation](https://docs.ideal-postcodes.co.uk/docs/sdks/php/symfony) covers configuration, testing with a mock client and the SDK itself.

## License

SEE LICENSE IN LICENSE
