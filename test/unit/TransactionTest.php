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

        $this->assertEquals('f852018609184e72a0008276c094d46e8dd67c5d32be8058bb8eb970870f07244567849184e72aa9d46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f072445675258080', $transaction->serialize()->toString('hex'));

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

        $this->assertEquals('f852018609184e72a0008276c094d46e8dd67c5d32be8058bb8eb970870f07244567849184e72aa9d46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f0724456752b8080', $transaction->serialize()->toString('hex'));
    }

    /**
     * testSha3
     * 
     * @return void
     */
    public function testSha3()
    {
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

        $this->assertNull($transaction->sha3(''));
        $this->assertEquals('47173285a8d7341e5e972fc677286384f802f8ef42a5ec5f03bbfa254cb01fad', $transaction->sha3('hello world'));
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

        $this->assertEquals('710aee292b0f1749aaa0cfef67111e2f716afbdb475e7f250bdb80c6655b0a66', $transaction->hash());

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

        $this->assertEquals('dd4238507087c6d354079ea5a4c8da7abde43252f0accf79601d0fa1a10300d8', (string) $transaction);
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
        $this->assertEquals('f892018609184e72a0008276c094d46e8dd67c5d32be8058bb8eb970870f07244567849184e72aa9d46e8dd67c5d32be8d46e8dd67c5d32be8058bb8eb970870f072445675058bb8eb970870f07244567525a08d8bfd01c48454b5b3fed2361cbd0e8c3282d5bd2e26762e4c9dfbe1ef35f325a06d6a5dc397934b5544835f34ff24263cbc00bdd516b6f0df3f29cdf6c779ccfb', $transaction->sign($this->testPrivateKey));
    }
}