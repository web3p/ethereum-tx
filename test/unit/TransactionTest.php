<?php

namespace Test\Unit;

use Test\TestCase;
use EthereumTx\Transaction;

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
     * testSign
     * 
     * @return void
     */
    public function testSign()
    {
        $this->assertTrue(true);
    }
}