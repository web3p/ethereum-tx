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
 * It's a instance for generating/serializing ethereum eip1559 transaction.
 * 
 * ```php
 * use Web3p\EthereumTx\EIP1559Transaction;
 * 
 * // generate transaction instance with transaction parameters
 * $transaction = new EIP1559Transaction([
 *     'nonce' => '0x01',
 *     'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
 *     'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
 *     'maxPriorityFeePerGas' => '0x9184e72a000',
 *     'maxFeePerGas' => '0x9184e72a000',
 *     'gas' => '0x76c0',
 *     'value' => '0x9184e72a',
 *     'chainId' => 1, // required
 *     'accessList' => [],
 *     'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
 * ]);
 * 
 * // generate transaction instance with hex encoded transaction
 * $transaction = new EIP1559Transaction('0x02f86c04158504a817c8008504a817c8008252089435353535353535353535353535353535353535358080c080a03fd48c8a173e9669c33cb5271f03b1af4f030dc8315be8ec9442b7fbdde893c8a010af381dab1df3e7012a3c8421d65a810859a5dd9d58991ad7c07f12d0c651c7');
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
class EIP1559Transaction extends TypeTransaction
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
        'maxPriorityFeePerGas' => [
            'key' => 2,
            'length' => 32,
            'allowLess' => true,
            'allowZero' => false
        ],
        'maxFeePerGas' => [
            'key' => 3,
            'length' => 32,
            'allowLess' => true,
            'allowZero' => false
        ],
        'gasLimit' => [
            'key' => 4,
            'length' => 32,
            'allowLess' => true,
            'allowZero' => false
        ],
        'gas' => [
            'key' => 4,
            'length' => 32,
            'allowLess' => true,
            'allowZero' => false
        ],
        'to' => [
            'key' => 5,
            'length' => 20,
            'allowZero' => true,
        ],
        'value' => [
            'key' => 6,
            'length' => 32,
            'allowLess' => true,
            'allowZero' => false
        ],
        'data' => [
            'key' => 7,
            'allowLess' => true,
            'allowZero' => true
        ],
        'accessList' => [
            'key' => 8,
            'allowLess' => true,
            'allowZero' => true,
            'allowArray' => true
        ],
        'v' => [
            'key' => 9,
            'allowZero' => true
        ],
        'r' => [
            'key' => 10,
            'length' => 32,
            'allowZero' => true
        ],
        's' => [
            'key' => 11,
            'length' => 32,
            'allowZero' => true
        ]
    ];

    /**
     * Transaction type
     * 
     * @var string
     */
    protected $transactionType = '02';

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
        $txData = array_fill(0, 12, '');
        foreach ($this->txData as $key => $data) {
            if ($key >= 0) {
                $txData[$key] = $data;
            }
        }
        $transactionType = $this->transactionType;
        return $transactionType . $this->rlp->encode($txData);
    }

    /**
     * Sign the transaction with given hex encoded private key.
     * 
     * @param string $privateKey hex encoded private key
     * @return string hex encoded signed ethereum transaction
     */
    public function sign(string $privateKey)
    {
        if ($this->util->isHex($privateKey)) {
            $privateKey = $this->util->stripZero($privateKey);
            $ecPrivateKey = $this->secp256k1->keyFromPrivate($privateKey, 'hex');
        } else {
            throw new InvalidArgumentException('Private key should be hex encoded string');
        }
        $txHash = $this->hash();
        $signature = $ecPrivateKey->sign($txHash, [
            'canonical' => true
        ]);
        $r = $signature->r;
        $s = $signature->s;
        $v = $signature->recoveryParam;

        $this->offsetSet('r', '0x' . $r->toString(16));
        $this->offsetSet('s', '0x' . $s->toString(16));
        $this->offsetSet('v', $v);
        $this->privateKey = $ecPrivateKey;

        return $this->serialize();
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
        $rawTxData = array_fill(0, 9, '');
        foreach ($this->txData as $key => $data) {
            if ($key >= 0 && $key < 9) {
                $rawTxData[$key] = $data;
            }
        }
        $serializedTx = $this->rlp->encode($rawTxData);
        $transactionType = $this->transactionType;
        return $this->util->sha3(hex2bin($transactionType . $serializedTx));
    }
}