<?php

/**
 * This file is part of ethereum-tx package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace EthereumTx;

use InvalidArgumentException;
use RuntimeException;
use RLP\RLP;
use Secp256k1\Secp256k1;
use ArrayAccess;

class Transaction implements ArrayAccess
{
    /**
     * txData
     * 
     * @var array
     */
    protected $map = [
        'from' => -1,
        'nonce' => 0,
        'gasPrice' => 1,
        'gasLimit' => 2,
        'gas' => 2,
        'to' => 3,
        'value' => 4,
        'data' => 5,
        'chainId' => 6,
        // 'v' => 6,
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
     * sign
     * 
     * @param string $privateKey
     * @return string
     */
    public function sign($privateKey)
    {}

    /**
     * hash
     * 
     * @return string
     */
    protected function hash()
    {}
}