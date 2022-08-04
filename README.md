# Solana PHP SDK

FORKED FROM [https://github.com/verze-app/solana-php-sdk](https://github.com/verze-app/solana-php-sdk)

---

:warning: **This project moved maintainers and will be actively worked on again. See [discussion for details](https://github.com/verze-app/solana-php-sdk/discussions/36)** Use at your own risk. Verze will *not* provide any support for this package as it exists right now; please don't email us expecting any support.

---

Simple PHP SDK for Solana.

## Installation

You can install the package via composer:

```bash
composer require ultainfinity/solana-php-sdk
```

## Usage

### Using the Solana simple client

You can use the `Connection` class for convenient access to API methods. Some are defined in the code:

```php
use Ultainfinity\SolanaPhpSdk\Connection;
use Ultainfinity\SolanaPhpSdk\SolanaRpcClient;

// Using a defined method
$sdk = new Connection(new SolanaRpcClient(SolanaRpcClient::MAINNET_ENDPOINT));
$accountInfo = $sdk->getAccountInfo('4fYNw3dojWmQ4dXtSGE9epjRGy9pFSx62YypT7avPYvA');
var_dump($accountInfo);
```

For all the possible methods, see the [API documentation](https://docs.solana.com/developing/clients/jsonrpc-api).

### Directly using the RPC client

The `Connection` class is just a light convenience layer on top of the RPC client. You can, if you want, use the client directly, which allows you to work with the full `Response` object:

```php
use Ultainfinity\SolanaPhpSdk\SolanaRpcClient;

$client = new SolanaRpcClient(SolanaRpcClient::MAINNET_ENDPOINT);
$accountInfoResponse = $client->call('getAccountInfo', ['4fYNw3dojWmQ4dXtSGE9epjRGy9pFSx62YypT7avPYvA']);
$accountInfoBody = $accountInfoResponse->json();
$accountInfoStatusCode = $accountInfoResponse->getStatusCode();
``````

### Transactions

Here is working example of sending a transfer instruction to the Solana blockchain:

```php
$client = new SolanaRpcClient(SolanaRpcClient::DEVNET_ENDPOINT);
$connection = new Connection($client);
$fromPublicKey = KeyPair::fromSecretKey([...]);
$toPublicKey = new PublicKey('J3dxNj7nDRRqRRXuEMynDG57DkZK4jYRuv3Garmb1i99');
$instruction = SystemProgram::transfer(
    $fromPublicKey->getPublicKey(),
    $toPublicKey,
    6
);

$transaction = new Transaction(null, null, $fromPublicKey->getPublicKey());
$transaction->add($instruction);

$txHash = $connection->sendTransaction($transaction, $fromPublicKey);
```

Note: This project is in alpha, the code to generate instructions is still being worked on `$instruction = SystemProgram::abc()`

## Roadmap

1. Borsh serialize and deserialize.
2. Improved documentation.
3. Build out more of the Connection, SystemProgram, TokenProgram, MetaplexProgram classes.
4. Improve abstractions around working with binary data.
5. Optimizations:
   1. Leverage PHP more.
   2. Better cache `$recentBlockhash` when sending transactions.
6. Suggestions? Open an issue or PR :D

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@verze.app instead of using the issue tracker.

## Credits

- [Matt Stauffer](https://github.com/mattstauffer) (Original creator)
- [Zach Vander Velden](https://github.com/exzachlyvv) (Metadata wizard)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
