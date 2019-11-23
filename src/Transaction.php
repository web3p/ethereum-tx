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

class Transaction implements ArrayAccess
{
    /**
     * attributeMap
     * 
     * @var array
     */
    protected $attributeMap = [
        'from' => [
            'key' => -1
        ],
        'chainId' => [
            'key' => -2
        ],
        'nonce' => [
            'key' => 0,
            'length' => 32,
            'allowLess' => true,
            'allowZero' => false
        ],
        'gasPrice' => [
            'key' => 1,
            'length' => 32,
            'allowLess' => true,
            'allowZero' => false
        ],
        'gasLimit' => [
            'key' => 2,
            'length' => 32,
            'allowLess' => true,
            'allowZero' => false
        ],
        'gas' => [
            'key' => 2,
            'length' => 32,
            'allowLess' => true,
            'allowZero' => false
        ],
        'to' => [
            'key' => 3,
            'length' => 20,
            'allowZero' => true,
        ],
        'value' => [
            'key' => 4,
            'length' => 32,
            'allowLess' => true,
            'allowZero' => false
        ],
        'data' => [
            'key' => 5,
            'allowLess' => true,
            'allowZero' => true
        ],
        'v' => [
            'key' => 6,
            'allowZero' => true
        ],
        'r' => [
            'key' => 7,
            'length' => 32,
            'allowZero' => true
        ],
        's' => [
            'key' => 8,
            'length' => 32,
            'allowZero' => true
        ]
    ];

    /**
     * txData
     * 
     * @var array
     */
    protected $txData;

    /**
     * rlp
     * 
     * @var \Web3p\RLP\RLP
     */
    protected $rlp;

    /**
     * secp256k1
     * 
     * @var \Elliptic\EC
     */
    protected $secp256k1;

    /**
     * privateKey
     * 
     * @var \Elliptic\EC\KeyPair
     */
    protected $privateKey;

    /**
     * util
     * 
     * @var \Web3p\EthereumUtil\Util
     */
    protected $util;

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
                $txData = $this->rlp->decode($txData);

                foreach ($txData as $txKey => $data) {
                    if (is_int($txKey)) {
                        $hexData = $data;

                        if (strlen($hexData) > 0) {
                            $tx[$txKey] = '0x' . $hexData;
                        } else {
                            $tx[$txKey] = $hexData;
                        }
                    }
                }
            }
            $this->txData = $tx;
        }
    }

    /**
     * get
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], []);
        }
        return $this->offsetGet($name);
    }

    /**
     * set
     * 
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $method = 'set' . ucfirst($name);

        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], [$value]);
        }
        return $this->offsetSet($name, $value);
    }

    /**
     * toString
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->hash(false);
    }

    /**
     * offsetSet
     * 
     * @param string $offset
     * @param string $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $txKey = isset($this->attributeMap[$offset]) ? $this->attributeMap[$offset] : null;

        if (is_array($txKey)) {
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
            $this->txData[$txKey['key']] = $value;
        }
    }

    /**
     * offsetExists
     * 
     * @param string $offset
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
     * offsetUnset
     * 
     * @param string $offset
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
     * offsetGet
     * 
     * @param string $offset
     * @return mixed
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
     * getTxData
     * 
     * @return array
     */
    public function getTxData()
    {
        return $this->txData;
    }

    /**
     * serialize
     * 
     * @return \Web3p\RLP\RLP\Buffer
     */
    public function serialize()
    {
        $chainId = $this->offsetGet('chainId');

        // sort tx data
        if (ksort($this->txData) !== true) {
            throw new RuntimeException('Cannot sort tx data by keys.');
        }
        if ($chainId && $chainId > 0) {
            $txData = array_fill(0, 9, '');
        } else {
            $txData = array_fill(0, 6, '');
        }
        foreach ($this->txData as $key => $data) {
            if ($key >= 0) {
                $txData[$key] = $data;
            }
        }
        return $this->rlp->encode($txData);
    }

    /**
     * sign
     * 
     * @param string $privateKey
     * @return string
     */
    public function sign(string $privateKey)
    {
        $txHash = $this->hash(false);
        $privateKey = $this->secp256k1->keyFromPrivate($privateKey, 'hex');
        $signature = $privateKey->sign($txHash, [
            'canonical' => true
        ]);
        $r = $signature->r;
        $s = $signature->s;
        $v = $signature->recoveryParam + 35;

        $chainId = $this->offsetGet('chainId');

        if ($chainId && $chainId > 0) {
            $v += (int) $chainId * 2;
        }

        $this->offsetSet('r', '0x' . $r->toString(16));
        $this->offsetSet('s', '0x' . $s->toString(16));
        $this->offsetSet('v', $v);
        $this->privateKey = $privateKey;

        return $this->serialize();
    }

    /**
     * hash
     *
     * @param bool $includeSignature
     * @return string
     */
    public function hash($includeSignature=false)
    {
        $chainId = $this->offsetGet('chainId');

        // sort tx data
        if (ksort($this->txData) !== true) {
            throw new RuntimeException('Cannot sort tx data by keys.');
        }
        if ($includeSignature) {
            $txData = $this->txData;
        } else {
            $rawTxData = $this->txData;

            if ($chainId && $chainId > 0) {
                $v = (int) $chainId;
                $this->offsetSet('r', '');
                $this->offsetSet('s', '');
                $this->offsetSet('v', $v);
                $txData = array_fill(0, 9, '');
            } else {
                $txData = array_fill(0, 6, '');
            }

            foreach ($this->txData as $key => $data) {
                if ($key >= 0) {
                    $txData[$key] = $data;
                }
            }
            $this->txData = $rawTxData;
        }
        $serializedTx = $this->rlp->encode($txData);

        return $this->util->sha3(hex2bin($serializedTx));
    }

    /**
     * getFromAddress
     * 
     * @return string
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
            $chainId = $this->offsetGet('chainId');

            if (!$r || !$s) {
                throw new RuntimeException('Invalid signature r and s.');
            }
            $txHash = $this->hash(false);

            if ($chainId && $chainId > 0) {
                $v -= ($chainId * 2);
            }
            $v -= 35;
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