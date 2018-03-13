# ethereum-tx
Ethereum transaction library in PHP.

# Install

```
composer require web3p/ethereum-tx
```

# Usage

Create a transaction:
```php
use EthereumTx\Transaction;

$transaction = new Transaction([
    'nonce' => '0x01',
    'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
    'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
    'gas' => '0x76c0',
    'gasPrice' => '0x9184e72a000',
    'value' => '0x9184e72a',
    'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
]);
```

Sign a transaction:
```php
use EthereumTx\Transaction;

$signedTransaction = $transaction->sign('your private key');
```

# API

Todo.

# License
MIT


