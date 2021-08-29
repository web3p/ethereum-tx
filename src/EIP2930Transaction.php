<?php

/**
 * This file is part of ethereum-tx package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3p\EthereumTx;

use InvalidArgumentException;
use RuntimeException;
use Web3p\RLP\RLP;
use Elliptic\EC;
use Elliptic\EC\KeyPair;
use ArrayAccess;
use Web3p\EthereumUtil\Util;
use Web3p\EthereumTx\TypeTransaction;

/**
 * It's a instance for generating/serializing ethereum eip2930 transaction.
 * 
 * ```php
 * use Web3p\EthereumTx\EIP2930Transaction;
 * 
 * // generate transaction instance with transaction parameters
 * $transaction = new EIP2930Transaction([
 *     'nonce' => '0x01',
 *     'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
 *     'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
 *     'gas' => '0x76c0',
 *     'gasPrice' => '0x9184e72a000',
 *     'value' => '0x9184e72a',
 *     'chainId' => 1, // required
 *     'accessList' => [],
 *     'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
 * ]);
 * 
 * // generate transaction instance with hex encoded transaction
 * $transaction = new EIP2930Transaction('0x01f86604158504a817c8008252089435353535353535353535353535353535353535358080c001a09753969d39f6a5109095d5082d67fc99a05fd66a339ba80934504ff79474e77aa07a907eb764b72b3088a331e7b97c2bad5fd43f1d574ddc80edeb022476454adb');
 * ```
 * 
 * ```php
 * After generate transaction instance, you can sign transaction with your private key.
 * <code>
 * $signedTransaction = $transaction->sign('your private key');
 * ```
 * 
 * Then you can send serialized transaction to ethereum through http rpc with web3.php.
 * ```php
 * $hashedTx = $transaction->serialize();
 * ```
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @link https://www.web3p.xyz
 * @filesource https://github.com/web3p/ethereum-tx
 */
class EIP2930Transaction extends TypeTransaction
{
    /**
     * Attribute map for keeping order of transaction key/value
     * 
     * @var array
     */
    protected $attributeMap = [
        'from' => [
            'key' => -1
        ],
        'chainId' => [
            'key' => 0
        ],
        'nonce' => [
            'key' => 1,
            'length' => 32,
            'allowLess' => true,
            'allowZero' => false
        ],
        'gasPrice' => [
            'key' => 2,
            'length' => 32,
            'allowLess' => true,
            'allowZero' => false
        ],
        'gasLimit' => [
            'key' => 3,
            'length' => 32,
            'allowLess' => true,
            'allowZero' => false
        ],
        'gas' => [
            'key' => 3,
            'length' => 32,
            'allowLess' => true,
            'allowZero' => false
        ],
        'to' => [
            'key' => 4,
            'length' => 20,
            'allowZero' => true,
        ],
        'value' => [
            'key' => 5,
            'length' => 32,
            'allowLess' => true,
            'allowZero' => false
        ],
        'data' => [
            'key' => 6,
            'allowLess' => true,
            'allowZero' => true
        ],
        'accessList' => [
            'key' => 7,
            'allowLess' => true,
            'allowZero' => true,
            'allowArray' => true
        ],
        'v' => [
            'key' => 8,
            'allowZero' => true
        ],
        'r' => [
            'key' => 9,
            'length' => 32,
            'allowZero' => true
        ],
        's' => [
            'key' => 10,
            'length' => 32,
            'allowZero' => true
        ]
    ];

    /**
     * Transaction type
     * 
     * @var string
     */
    protected $transactionType = '01';

    /**
     * construct
     * 
     * @param array|string $txData
     * @return void
     */
    public function __construct($txData=[])
    {
        parent::__construct($txData);
    }

    /**
     * RLP serialize the ethereum transaction.
     * 
     * @return \Web3p\RLP\RLP\Buffer serialized ethereum transaction
     */
    public function serialize()
    {
        // sort tx data
        if (ksort($this->txData) !== true) {
            throw new RuntimeException('Cannot sort tx data by keys.');
        }
        $txData = array_fill(0, 11, '');
        foreach ($this->txData as $key => $data) {
            if ($key >= 0) {
                $txData[$key] = $data;
            }
        }
        $transactionType = $this->transactionType;
        return $transactionType . $this->rlp->encode($txData);
    }

    /**
     * Return hash of the ethereum transaction with/without signature.
     *
     * @return string hex encoded hash of the ethereum transaction
     */
    public function hash()
    {
        // sort tx data
        if (ksort($this->txData) !== true) {
            throw new RuntimeException('Cannot sort tx data by keys.');
        }
        $rawTxData = array_fill(0, 8, '');
        foreach ($this->txData as $key => $data) {
            if ($key >= 0 && $key < 8) {
                $rawTxData[$key] = $data;
            }
        }
        $serializedTx = $this->rlp->encode($rawTxData);
        $transactionType = $this->transactionType;
        return $this->util->sha3(hex2bin($transactionType . $serializedTx));
    }
}