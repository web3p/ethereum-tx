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
use kornrunner\Keccak;
use Web3p\RLP\RLP;
use Elliptic\EC;
use Elliptic\EC\KeyPair;
use ArrayAccess;

class Transaction implements ArrayAccess
{
    /**
     * SHA3_NULL_HASH
     * 
     * @const string
     */
    const SHA3_NULL_HASH = 'c5d2460186f7233c927e7db2dcc703c0e500b653ca82273b7bfad8045d85a470';

    /**
     * txData
     * 
     * @var array
     */
    protected $map = [
        'from' => -1,
        'chainId' => -2,
        'nonce' => 0,
        'gasPrice' => 1,
        'gasLimit' => 2,
        'gas' => 2,
        'to' => 3,
        'value' => 4,
        'data' => 5,
        'v' => 6,
        'r' => 7,
        's' => 8
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
     * construct
     * 
     * @param array $txData
     * @return void
     */
    public function __construct(array $txData=[])
    {
        foreach ($txData as $key => $data) {
            $txKey = isset($this->map[$key]) ? $this->map[$key] : null;

            if (is_int($txKey)) {
                $this->txData[$txKey] = $data;
            }
        }
        $this->rlp = new RLP;
        $this->secp256k1 = new EC('secp256k1');
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
        $txKey = isset($this->map[$offset]) ? $this->map[$offset] : null;

        if (is_int($txKey)) {
            $this->txData[$txKey] = $value;
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
        $txKey = isset($this->map[$offset]) ? $this->map[$offset] : null;

        if (is_int($txKey)) {
            return isset($this->txData[$txKey]);
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
        $txKey = isset($this->map[$offset]) ? $this->map[$offset] : null;

        if (is_int($txKey) && isset($this->txData[$txKey])) {
            unset($this->txData[$txKey]);
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
        $txKey = isset($this->map[$offset]) ? $this->map[$offset] : null;

        if (is_int($txKey) && isset($this->txData[$txKey])) {
            return $this->txData[$txKey];
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
     * sha3
     * keccak256
     * 
     * @param string $value
     * @return string
     */
    public function sha3(string $value)
    {
        $hash = Keccak::hash($value, 256);

        if ($hash === $this::SHA3_NULL_HASH) {
            return null;
        }
        return $hash;
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
        $signature = $privateKey->sign($txHash);
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

        return $this->serialize()->toString('hex');
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
        }
        $serializedTx = $this->rlp->encode($txData)->toString('utf8');

        return $this->sha3($serializedTx);
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
        $from = '0x' . substr($this->sha3(substr(hex2bin($publicKey), 1)), 24);

        $this->offsetSet('from', $from);
        return $from;
    }
}