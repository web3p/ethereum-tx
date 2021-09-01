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

/**
 * It's a base transaction for generating/serializing ethereum type transaction (EIP1559/EIP2930).
 * Only use this class to generate new type transaction
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @link https://www.web3p.xyz
 * @filesource https://github.com/web3p/ethereum-tx
 */
class TypeTransaction implements ArrayAccess
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
        'v' => [
            'key' => 7,
            'allowZero' => true
        ],
        'r' => [
            'key' => 8,
            'length' => 32,
            'allowZero' => true
        ],
        's' => [
            'key' => 9,
            'length' => 32,
            'allowZero' => true
        ]
    ];

    /**
     * Raw transaction data
     * 
     * @var array
     */
    protected $txData = [];

    /**
     * RLP encoding instance
     * 
     * @var \Web3p\RLP\RLP
     */
    protected $rlp;

    /**
     * secp256k1 elliptic curve instance
     * 
     * @var \Elliptic\EC
     */
    protected $secp256k1;

    /**
     * Private key instance
     * 
     * @var \Elliptic\EC\KeyPair
     */
    protected $privateKey;

    /**
     * Ethereum util instance
     * 
     * @var \Web3p\EthereumUtil\Util
     */
    protected $util;

    /**
     * Transaction type
     * 
     * @var string
     */
    protected $transactionType = '00';

    /**
     * construct
     * 
     * @param array|string $txData
     * @return void
     */
    public function __construct($txData=[])
    {
        $this->rlp = new RLP;
        $this->secp256k1 = new EC('secp256k1');
        $this->util = new Util;

        if (is_array($txData)) {
            foreach ($txData as $key => $data) {
                $this->offsetSet($key, $data);
            }
        } elseif (is_string($txData)) {
            $tx = [];

            if ($this->util->isHex($txData)) {
                // check first byte
                $txData = $this->util->stripZero($txData);
                $firstByteStr = substr($txData, 0, 2);
                $firstByte = hexdec($firstByteStr);
                if ($this->isTransactionTypeValid($firstByte)) {
                    $txData = substr($txData, 2);
                }
                $txData = $this->rlp->decode($txData);

                foreach ($txData as $txKey => $data) {
                    if (is_int($txKey)) {
                        if (is_string($data) && strlen($data) > 0) {
                            $tx[$txKey] = '0x' . $data;
                        } else {
                            $tx[$txKey] = $data;
                        }
                    }
                }
            }
            $this->txData = $tx;
        }
    }

    /**
     * Return the value in the transaction with given key or return the protected property value if get(property_name} function is existed.
     * 
     * @param string $name key or protected property name
     * @return mixed
     */
    public function __get(string $name)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], []);
        }
        return $this->offsetGet($name);
    }

    /**
     * Set the value in the transaction with given key or return the protected value if set(property_name} function is existed.
     * 
     * @param string $name key, eg: to
     * @param mixed value
     * @return void
     */
    public function __set(string $name, $value)
    {
        $method = 'set' . ucfirst($name);

        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], [$value]);
        }
        return $this->offsetSet($name, $value);
    }

    /**
     * Return hash of the ethereum transaction without signature.
     * 
     * @return string hex encoded of the transaction
     */
    public function __toString()
    {
        return $this->hash(false);
    }

    /**
     * Set the value in the transaction with given key.
     * 
     * @param string $offset key, eg: to
     * @param string value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $txKey = isset($this->attributeMap[$offset]) ? $this->attributeMap[$offset] : null;

        if (is_array($txKey)) {
            if (is_array($value)) {
                if (!isset($txKey['allowArray']) || (isset($txKey['allowArray']) && $txKey['allowArray'] === false)) {
                    throw new InvalidArgumentException($offset . ' should\'t be array.');
                }
                if (!isset($txKey['allowLess']) || (isset($txKey['allowLess']) && $txKey['allowLess'] === false)) {
                    // check length
                    if (isset($txKey['length'])) {
                        if (count($value) > $txKey['length'] * 2) {
                            throw new InvalidArgumentException($offset . ' exceeds the length limit.');
                        }
                    }
                }
                if (!isset($txKey['allowZero']) || (isset($txKey['allowZero']) && $txKey['allowZero'] === false)) {
                    // check zero
                    foreach ($value as $key => $v) {
                        $checkedV = $v ? (string) $v : '';
                        if (preg_match('/^0*$/', $checkedV) === 1) {
                            // set value to empty string
                            $checkedV = '';
                            $value[$key] = $checkedV;
                        }
                    }
                }
            } else {
                $checkedValue = ($value) ? (string) $value : '';
                $isHex = $this->util->isHex($checkedValue);
                $checkedValue = $this->util->stripZero($checkedValue);

                if (!isset($txKey['allowLess']) || (isset($txKey['allowLess']) && $txKey['allowLess'] === false)) {
                    // check length
                    if (isset($txKey['length'])) {
                        if ($isHex) {
                            if (strlen($checkedValue) > $txKey['length'] * 2) {
                                throw new InvalidArgumentException($offset . ' exceeds the length limit.');
                            }
                        } else {
                            if (strlen($checkedValue) > $txKey['length']) {
                                throw new InvalidArgumentException($offset . ' exceeds the length limit.');
                            }
                        }
                    }
                }
                if (!isset($txKey['allowZero']) || (isset($txKey['allowZero']) && $txKey['allowZero'] === false)) {
                    // check zero
                    if (preg_match('/^0*$/', $checkedValue) === 1) {
                        // set value to empty string
                        $value = '';
                    }
                }
            }
            $this->txData[$txKey['key']] = $value;
        }
    }

    /**
     * Return whether the value is in the transaction with given key.
     * 
     * @param string $offset key, eg: to
     * @return bool
     */
    public function offsetExists($offset)
    {
        $txKey = isset($this->attributeMap[$offset]) ? $this->attributeMap[$offset] : null;

        if (is_array($txKey)) {
            return isset($this->txData[$txKey['key']]);
        }
        return false;
    }

    /**
     * Unset the value in the transaction with given key.
     * 
     * @param string $offset key, eg: to
     * @return void
     */
    public function offsetUnset($offset)
    {
        $txKey = isset($this->attributeMap[$offset]) ? $this->attributeMap[$offset] : null;

        if (is_array($txKey) && isset($this->txData[$txKey['key']])) {
            unset($this->txData[$txKey['key']]);
        }
    }

    /**
     * Return the value in the transaction with given key.
     * 
     * @param string $offset key, eg: to 
     * @return mixed value of the transaction
     */
    public function offsetGet($offset)
    {
        $txKey = isset($this->attributeMap[$offset]) ? $this->attributeMap[$offset] : null;

        if (is_array($txKey) && isset($this->txData[$txKey['key']])) {
            return $this->txData[$txKey['key']];
        }
        return null;
    }

    /**
     * Return raw ethereum transaction data.
     * 
     * @return array raw ethereum transaction data
     */
    public function getTxData()
    {
        return $this->txData;
    }

    /**
     * Return whether transaction type is valid (0x0 <= $transactionType <= 0x7f).
     * 
     * @param integer $transactionType
     * @return boolean is transaction valid
     */
    protected function isTransactionTypeValid(int $transactionType)
    {
        return $transactionType >= 0 && $transactionType <= 127;
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
        $txData = array_fill(0, 10, '');
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
        $rawTxData = array_fill(0, 7, '');
        foreach ($this->txData as $key => $data) {
            if ($key >= 0 && $key < 8) {
                $rawTxData[$key] = $data;
            }
        }
        $serializedTx = $this->rlp->encode($rawTxData);
        $transactionType = $this->transactionType;
        return $this->util->sha3(hex2bin($transactionType . $serializedTx));
    }

    /**
     * Recover from address with given signature (r, s, v) if didn't set from.
     * 
     * @return string hex encoded ethereum address
     */
    public function getFromAddress()
    {
        $from = $this->offsetGet('from');

        if ($from) {
            return $from;
        }
        if (!isset($this->privateKey) || !($this->privateKey instanceof KeyPair)) {
            // recover from hash
            $r = $this->offsetGet('r');
            $s = $this->offsetGet('s');
            $v = $this->offsetGet('v');

            if (!$r || !$s) {
                throw new RuntimeException('Invalid signature r and s.');
            }
            $txHash = $this->hash();
            $publicKey = $this->secp256k1->recoverPubKey($txHash, [
                'r' => $r,
                's' => $s
            ], $v);
            $publicKey = $publicKey->encode('hex');
        } else {
            $publicKey = $this->privateKey->getPublic(false, 'hex');
        }
        $from = '0x' . substr($this->util->sha3(substr(hex2bin($publicKey), 1)), 24);

        $this->offsetSet('from', $from);
        return $from;
    }
}