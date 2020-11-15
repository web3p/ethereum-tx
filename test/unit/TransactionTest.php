<?php

namespace Test\Unit;

use Test\TestCase;
use Web3p\EthereumTx\Transaction;

class TransactionTest extends TestCase
{
    /**
     * testGet
     * 
     * @return void
     */
    public function testGet()
    {
        $transaction = new Transaction([
            'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
            'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'gas' => '0x76c0',
            'gasPrice' => '0x9184e72a000',
            'value' => '0x9184e72a',
            'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
        ]);

        $this->assertEquals('0xb60e8dd61c5d32be8058bb8eb970870f07233155', $transaction['from']);
        $this->assertEquals('0xd46e8dd67c5d32be8058bb8eb970870f07244567', $transaction['to']);
        $this->assertEquals('0x76c0', $transaction['gas']);
        $this->assertEquals('0x9184e72a000', $transaction['gasPrice']);
        $this->assertEquals('0x9184e72a', $transaction['value']);
        $this->assertEquals('0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675', $transaction['data']);
        $this->assertEquals(null, $transaction['chainId']);

        $this->assertEquals('0xb60e8dd61c5d32be8058bb8eb970870f07233155', $transaction->from);
        $this->assertEquals('0xd46e8dd67c5d32be8058bb8eb970870f07244567', $transaction->to);
        $this->assertEquals('0x76c0', $transaction->gas);
        $this->assertEquals('0x9184e72a000', $transaction->gasPrice);
        $this->assertEquals('0x9184e72a', $transaction->value);
        $this->assertEquals('0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675', $transaction->data);
        $this->assertEquals(null, $transaction->chainId);
    }

    /**
     * testSet
     * 
     * @return void
     */
    public function testSet()
    {
        $transaction = new Transaction([
            'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
            'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'gas' => '0x76c0',
            'gasPrice' => '0x9184e72a000',
            'value' => '0x9184e72a',
            'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
        ]);

        $transaction['from'] = '0xb60e8dd61c5d32be8058bb8eb970870f07231234';
        $this->assertEquals('0xb60e8dd61c5d32be8058bb8eb970870f07231234', $transaction['from']);

        $transaction['to'] = '0xb60e8dd61c5d32be8058bb8eb970870f07233155';
        $this->assertEquals('0xb60e8dd61c5d32be8058bb8eb970870f07233155', $transaction['to']);

        $transaction['gas'] = '0x76';
        $this->assertEquals('0x76', $transaction['gas']);

        $transaction['gasPrice'] = '0x12';
        $this->assertEquals('0x12', $transaction['gasPrice']);

        $transaction['value'] = '0x01';
        $this->assertEquals('0x01', $transaction['value']);

        $transaction['data'] = '';
        $this->assertEquals('', $transaction['data']);

        $transaction['chainId'] = 4;
        $this->assertEquals(4, $transaction['chainId']);

        $transaction->from = '0xb60e8dd61c5d32be8058bb8eb970870f07233155';
        $this->assertEquals('0xb60e8dd61c5d32be8058bb8eb970870f07233155', $transaction->from);

        $transaction->to = '0xd46e8dd67c5d32be8058bb8eb970870f07244567';
        $this->assertEquals('0xd46e8dd67c5d32be8058bb8eb970870f07244567', $transaction->to);

        $transaction->gas = '0x76c0';
        $this->assertEquals('0x76c0', $transaction->gas);

        $transaction->gasPrice = '0x9184e72a000';
        $this->assertEquals('0x9184e72a000', $transaction->gasPrice);

        $transaction->value = '0x9184e72a';
        $this->assertEquals('0x9184e72a', $transaction->value);

        $transaction->data = '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675';
        $this->assertEquals('0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675', $transaction->data);

        $transaction->chainId = null;
        $this->assertEquals(null, $transaction->chainId);
    }

    /**
     * testHash
     * 
     * @return void
     */
    public function testHash()
    {
        $transaction = new Transaction([
            'nonce' => '0x01',
            'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
            'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'gas' => '0x76c0',
            'gasPrice' => '0x9184e72a000',
            'value' => '0x9184e72a',
            'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
        ]);

        $this->assertEquals('79617051b33e38636c12fb761abf62c20a9dd5a743ca5f338f04f2cf5f2ec6bd', $transaction->hash());

        $transaction = new Transaction([
            'nonce' => '0x01',
            'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
            'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'gas' => '0x76c0',
            'gasPrice' => '0x9184e72a000',
            'value' => '0x9184e72a',
            'chainId' => 4,
            'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
        ]);

        $this->assertEquals('8aace0c8df439c9cc9f313b116f1db03e0811ca07e582d351aad1c9d6542c23d', (string) $transaction);
    }

    /**
     * testSign
     * 
     * @return void
     */
    public function testSign()
    {
        $transaction = new Transaction([
            'nonce' => '0x01',
            'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
            'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'gas' => '0x76c0',
            'gasPrice' => '0x9184e72a000',
            'value' => '0x9184e72a',
            'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
        ]);
        $this->assertEquals('f892018609184e72a0008276c094d46e8dd67c5d32be8058bb8eb970870f07244567849184e72aa9d46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f07244567523a0a48d3ce9c68bb49825aea5335bd07432823e858e8a504767d08290c28aafddf8a0416c7abc3a67080db0ad07c42de82db4e05518f99595119677398c68d431ab37', $transaction->sign($this->testPrivateKey));

        // test different private keys
        $tests = [
            'fake private key', '0xd0459987fdde1f41e524fddbf4b646cd9d3bea7fd7d63feead3f5dfce6174a3d', 'd0459987fdde1f41e524fddbf4b646cd9d3bea7fd7d63feead3f5dfce6174a3d', 'd0459987fdde1f41e524fddbf4b646cd9d3bea7fd7d63feead3f5dfce6174a'
        ];
        for ($i=0; $i<count($tests); $i++) {
            try {
                $transaction->sign($tests[$i]);
            } catch (\InvalidArgumentException $e) {
                $this->assertEquals('Private key should be hex encoded string', $e->getMessage());
            }
        }
    }

    /**
     * testSerialize
     * 
     * @return void
     */
    public function testSerialize()
    {
        $transaction = new Transaction([
            'nonce' => '0x01',
            'from' => '0xb60e8dd61c5d32be8058bb8eb970870f07233155',
            'to' => '0xd46e8dd67c5d32be8058bb8eb970870f07244567',
            'gas' => '0x76c0',
            'gasPrice' => '0x9184e72a000',
            'value' => '0x9184e72a',
            'data' => '0xd46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675'
        ]);

        $this->assertEquals('f84f018609184e72a0008276c094d46e8dd67c5d32be8058bb8eb970870f07244567849184e72aa9d46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675', $transaction->serialize());

        // sign tx
        $transaction->sign($this->testPrivateKey);

        $this->assertEquals('f892018609184e72a0008276c094d46e8dd67c5d32be8058bb8eb970870f07244567849184e72aa9d46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f07244567523a0a48d3ce9c68bb49825aea5335bd07432823e858e8a504767d08290c28aafddf8a0416c7abc3a67080db0ad07c42de82db4e05518f99595119677398c68d431ab37', $transaction->serialize());
    }

    /**
     * testEIP155
     * you can find test case here: https://github.com/ethereum/EIPs/blob/master/EIPS/eip-155.md
     * 
     * @return void
     */
    public function testEIP155()
    {
        // test signing data
        $transaction = new Transaction([
            'nonce' => '0x09',
            'to' => '0x3535353535353535353535353535353535353535',
            'gas' => '0x5208',
            'gasPrice' => '0x4a817c800',
            'value' => '0xde0b6b3a7640000',
            'chainId' => 1,
            'data' => ''
        ]);
        $transaction['r'] = '';
        $transaction['s'] = '';
        $transaction['v'] = 1;
        $this->assertEquals('ec098504a817c800825208943535353535353535353535353535353535353535880de0b6b3a764000080018080', $transaction->serialize());

        $transaction = new Transaction([
            'nonce' => '0x09',
            'to' => '0x3535353535353535353535353535353535353535',
            'gas' => '0x5208',
            'gasPrice' => '0x4a817c800',
            'value' => '0xde0b6b3a7640000',
            'chainId' => 1,
            'data' => ''
        ]);
        $this->assertEquals('daf5a779ae972f972197303d7b574746c7ef83eadac0f2791ad23db92e4c8e53', $transaction->hash(false));
        $this->assertEquals('f86c098504a817c800825208943535353535353535353535353535353535353535880de0b6b3a76400008025a028ef61340bd939bc2195fe537567866003e1a15d3c71ff63e1590620aa636276a067cbe9d8997f761aecb703304b3800ccf555c9f3dc64214b297fb1966a3b6d83', $transaction->sign('0x4646464646464646464646464646464646464646464646464646464646464646'));

        $transaction = new Transaction([
            'nonce' => '0x09',
            'to' => '0x3535353535353535353535353535353535353535',
            'gas' => '0x5208',
            'gasPrice' => '0x4a817c800',
            'value' => '0x0',
            'chainId' => 1,
            'data' => ''
        ]);
        $this->assertEquals('f864098504a817c800825208943535353535353535353535353535353535353535808025a0855ec9b7d4fcabf535fe4ac4a7c31a9e521214d05bc6efbc058d4757c35e92bba0043d7df30c8a79e5522b3de8fc169df5fa7145714100ee8ec413292d97ce4d3a', $transaction->sign('0x4646464646464646464646464646464646464646464646464646464646464646'));

        $transaction = new Transaction('0xf86c098504a817c800825208943535353535353535353535353535353535353535880de0b6b3a76400008025a028ef61340bd939bc2195fe537567866003e1a15d3c71ff63e1590620aa636276a067cbe9d8997f761aecb703304b3800ccf555c9f3dc64214b297fb1966a3b6d83');
        $this->assertEquals('f86c098504a817c800825208943535353535353535353535353535353535353535880de0b6b3a76400008025a028ef61340bd939bc2195fe537567866003e1a15d3c71ff63e1590620aa636276a067cbe9d8997f761aecb703304b3800ccf555c9f3dc64214b297fb1966a3b6d83', $transaction->serialize());
    }

    /**
     * testGetFromAddress
     * 0x9d8a62f656a8d1615c1294fd71e9cfb3e4855a4f
     * 
     * @return void
     */
    public function testGetFromAddress()
    {
        $transaction = new Transaction([
            'nonce' => '0x09',
            'to' => '0x3535353535353535353535353535353535353535',
            'gas' => '0x5208',
            'gasPrice' => '0x4a817c800',
            'value' => '0xde0b6b3a7640000',
            'chainId' => 1,
            'data' => ''
        ]);
        // sign tx
        $transaction->sign('0x4646464646464646464646464646464646464646464646464646464646464646');
        $r = $transaction['r'];
        $s = $transaction['s'];
        $v = $transaction['v'];

        // get from privatekey
        $fromA = $transaction->getFromAddress();

        $transaction = new Transaction([
            'nonce' => '0x09',
            'to' => '0x3535353535353535353535353535353535353535',
            'gas' => '0x5208',
            'gasPrice' => '0x4a817c800',
            'value' => '0xde0b6b3a7640000',
            'chainId' => 1,
            'data' => ''
        ]);
        $transaction['r'] = $r;
        $transaction['s'] = $s;
        $transaction['v'] = $v;

        // get from r, s, v
        $fromB = $transaction->getFromAddress();

        $transaction = new Transaction([
            'from' => '0x9d8a62f656a8d1615c1294fd71e9cfb3e4855a4f',
            'nonce' => '0x09',
            'to' => '0x3535353535353535353535353535353535353535',
            'gas' => '0x5208',
            'gasPrice' => '0x4a817c800',
            'value' => '0xde0b6b3a7640000',
            'chainId' => 1,
            'data' => ''
        ]);

        // get from transaction
        $fromC = $transaction->getFromAddress();

        $this->assertEquals('0x9d8a62f656a8d1615c1294fd71e9cfb3e4855a4f', $fromA);
        $this->assertEquals($fromA, $fromB);
        $this->assertEquals($fromB, $fromC);
    }

    /**
     * testIssue15
     * 
     * @return void
     */
    public function testIssue15()
    {
        $signedTransactions = [];
        $nonces = [
            '0x00', '0x0', 0, '0x000', '0'
        ];

        // push signed transaction
        for ($i=0; $i<count($nonces); $i++) {
            $transaction = new Transaction([
                'nonce' => $nonces[$i],
                'to' => '0x3535353535353535353535353535353535353535',
                'gas' => '0x5208',
                'gasPrice' => '0x4a817c800',
                'value' => '0x0',
                'chainId' => 1,
                'data' => ''
            ]);
            $signedTransactions[] = $transaction->sign('0x4646464646464646464646464646464646464646464646464646464646464646');
        }

        // compare each signed transaction
        for ($i=1; $i<count($signedTransactions); $i++) {
            $this->assertEquals($signedTransactions[0], $signedTransactions[$i]);
        }
    }

    /**
     * testIssue26
     * default $txData should be empty array
     * 
     * @return void
     */
    public function testIssue26()
    {
        $tests = [
            null, [], [null]
        ];
        for ($i=0; $i<count($tests); $i++) {
            $transaction = new Transaction($tests[$i]);
            $this->assertEquals($transaction->txData, []);
        }
    }
}
