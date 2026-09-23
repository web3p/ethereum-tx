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

use Web3p\RLP\RLP;

/**
 * RLP encoder that keeps leading zero bytes of byte-string fields.
 *
 * web3p/rlp strips leading zeros from every 0x-prefixed string, which is only correct for integers.
 * Byte-string fields (to, data, access list) must be encoded as-is.
 *
 * @internal
 * @author Peter Lai <alk03073135@gmail.com>
 * @link https://www.web3p.xyz
 * @filesource https://github.com/web3p/ethereum-tx
 */
class RLPEncoder extends RLP
{
    /**
     * Return RLP encoded list of the given fields, fields with the given keys are encoded as byte strings.
     *
     * @param array $inputs fields of the list
     * @param array $byteKeys keys of byte-string fields
     * @return string RLP encoded hex string of inputs
     */
    public function encodeList(array $inputs, array $byteKeys)
    {
        $output = '';
        foreach ($inputs as $key => $input) {
            if (in_array($key, $byteKeys, true)) {
                $output .= $this->encodeBytes($input);
            } else {
                $output .= $this->encode($input);
            }
        }
        return $this->encodeLength(mb_strlen($output) / 2, 192) . $output;
    }

    /**
     * Return RLP encoded of the given byte string (or nested list of byte strings) without stripping leading zeros.
     *
     * @param mixed $input 0x-prefixed hex string or array of them
     * @return string RLP encoded hex string of input
     */
    public function encodeBytes($input)
    {
        if (is_array($input)) {
            $output = '';
            foreach ($input as $item) {
                $output .= $this->encodeBytes($item);
            }
            return $this->encodeLength(mb_strlen($output) / 2, 192) . $output;
        }
        if (!is_string($input) || strpos($input, '0x') !== 0) {
            return $this->encode($input);
        }
        $input = $this->padToEven(substr($input, 2));
        $length = strlen($input) / 2;

        // first byte < 0x80
        if ($length === 1 && hexdec($input) < 0x80) {
            return $input;
        }
        return $this->encodeLength($length, 128) . $input;
    }
}
