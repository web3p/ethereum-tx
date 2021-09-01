# ethereum-tx
[![PHP](https://github.com/web3p/ethereum-tx/actions/workflows/php.yml/badge.svg)](https://github.com/web3p/ethereum-tx/actions/workflows/php.yml)
[![codecov](https://codecov.io/gh/web3p/ethereum-tx/branch/master/graph/badge.svg)](https://codecov.io/gh/web3p/ethereum-tx)

Ethereum transaction library in PHP.

# Install

```
composer require web3p/ethereum-tx
```

# Usage

## Create a transaction
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
    'data' => ''
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

// hex encoded transaction
$transaction = new Transaction('0xf86c098504a817c800825208943535353535353535353535353535353535353535880de0b6b3a76400008025a028ef61340bd939bc2195fe537567866003e1a15d3c71ff63e1590620aa636276a067cbe9d8997f761aecb703304b3800ccf555c9f3dc64214b297fb1966a3b6d83');
```

## Create a EIP1559 transaction
```php
use Web3p\EthereumTx\EIP1559Transaction;

// generate transaction instance with transaction parameters
$transaction = new EIP1559Transaction([
    'nonce' => '0x01',
    'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
    'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
    'maxPriorityFeePerGas' => '0x9184e72a000',
    'maxFeePerGas' => '0x9184e72a000',
    'gas' => '0x76c0',
    'value' => '0x9184e72a',
    'chainId' => 1, // required
    'accessList' => [],
    'data' => ''
]);
```

## Create a EIP2930 transaction:
```php
use Web3p\EthereumTx\EIP2930Transaction;

// generate transaction instance with transaction parameters
$transaction = new EIP2930Transaction([
    'nonce' => '0x01',
    'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
    'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
    'gas' => '0x76c0',
    'value' => '0x9184e72a',
    'chainId' => 1, // required
    'accessList' => [],
    'data' => ''
]);
```

## Sign a transaction:
```php
use Web3p\EthereumTx\Transaction;

$signedTransaction = $transaction->sign('your private key');
```

# API

https://www.web3p.xyz/ethereumtx.html

# License
MIT


