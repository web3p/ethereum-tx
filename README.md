# ethereum-tx
[![Build Status](https://travis-ci.org/web3p/ethereum-tx.svg?branch=master)](https://travis-ci.org/web3p/ethereum-tx)
[![codecov](https://codecov.io/gh/web3p/ethereum-tx/branch/master/graph/badge.svg)](https://codecov.io/gh/web3p/ethereum-tx)

Ethereum transaction library in PHP.

# Install

```
composer require web3p/ethereum-tx
```

# Usage

Create a transaction:
```php
use Web3p\EthereumTx\Transaction;

// without chainId
$transaction = new Transaction([
    'nonce' => '0x01',
    'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
    'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
    'gas' => '0x76c0',
    'gasPrice' => '0x9184e72a000',
    'value' => '0x9184e72a',
    'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
]);

// with chainId
$transaction = new Transaction([
    'nonce' => '0x01',
    'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
    'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
    'gas' => '0x76c0',
    'gasPrice' => '0x9184e72a000',
    'value' => '0x9184e72a',
    'chainId' => 1,
    'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
]);
```

Sign a transaction:
```php
use Web3p\EthereumTx\Transaction;

$signedTransaction = $transaction->sign('your private key');
```

# API

### Web3p\EthereumTx\Transaction

#### sha3

Returns keccak256 encoding of given data.

> It will be removed in the next version.

`sha3(string $input)`

String input

###### Example

* Encode string.

```php
use Web3p\EthereumTx\Transaction;

$transaction = new Transaction([
    'nonce' => '0x01',
    'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
    'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
    'gas' => '0x76c0',
    'gasPrice' => '0x9184e72a000',
    'value' => '0x9184e72a',
    'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
]);
$hashedString = $transaction->sha3('web3p');
```

#### serialize

Returns recursive length prefix encoding of transaction data.

`serialize()`

###### Example

* Serialize the transaction data.

```php
use Web3p\EthereumTx\Transaction;

$transaction = new Transaction([
    'nonce' => '0x01',
    'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
    'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
    'gas' => '0x76c0',
    'gasPrice' => '0x9184e72a000',
    'value' => '0x9184e72a',
    'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
]);
$serializedTx = $transaction->serialize();
```

#### sign

Returns signed of transaction data.

`sign(string $privateKey)`

String privateKey - hexed private key with zero prefixed.

###### Example

* Sign the transaction data.

```php
use Web3p\EthereumTx\Transaction;

$transaction = new Transaction([
    'nonce' => '0x01',
    'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
    'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
    'gas' => '0x76c0',
    'gasPrice' => '0x9184e72a000',
    'value' => '0x9184e72a',
    'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
]);
$signedTx = $transaction->sign($stringPrivateKey);
```

#### hash

Returns keccak256 encoding of serialized transaction data.

`hash()`

###### Example

* Hash serialized transaction data.

```php
use Web3p\EthereumTx\Transaction;

$transaction = new Transaction([
    'nonce' => '0x01',
    'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
    'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
    'gas' => '0x76c0',
    'gasPrice' => '0x9184e72a000',
    'value' => '0x9184e72a',
    'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
]);
$hashedTx = $transaction->serialize();
```

# License
MIT


