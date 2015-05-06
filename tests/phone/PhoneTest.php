<?php
/**
 * Copyright 2015 Goracash
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use Goracash\Client as Client;
use Goracash\Service\Phone as Phone;

class PhoneTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Client
     */
    public $Client;

    /**
     * @var Phone
     */
    public $Service;

    public function setUp()
    {
        $configPath = dirname(__FILE__) . '/../testdata/test.ini';
        $this->Client = new Client($configPath);
        $this->Client->authenticate();
        $this->Service = new Phone($this->Client);
    }

    public function testGetThematics()
    {
        $thematics = $this->Service->getAvailableThematics();
        $this->assertInternalType('array', $thematics);
        $this->assertGreaterThan(0, count($thematics));
        foreach ($thematics as $thematic) {
            $this->assertArrayHasKey('id', $thematic);
            $this->assertArrayHasKey('key', $thematic);
            $this->assertArrayHasKey('label', $thematic);
        }
    }

    public function testGetMarkets()
    {
        $markets = $this->Service->getAvailableMarkets();
        $this->assertInternalType('array', $markets);
        $this->assertGreaterThan(0, count($markets));
        foreach ($markets as $market) {
            $this->assertArrayHasKey('id', $market);
            $this->assertArrayHasKey('key', $market);
            $this->assertArrayHasKey('label', $market);
        }
    }

    public function testGetTypes()
    {
        $types = $this->Service->getAvailableTypes();
        $this->assertInternalType('array', $types);
        $this->assertGreaterThan(0, count($types));
        foreach ($types as $type) {
            $this->assertArrayHasKey('id', $type);
            $this->assertArrayHasKey('key', $type);
            $this->assertArrayHasKey('label', $type);
        }
    }

    public function testGetCountries()
    {
        $types = $this->Service->getAvailableCountries();
        $this->assertInternalType('array', $types);
        $this->assertGreaterThan(0, count($types));
        foreach ($types as $type) {
            $this->assertArrayHasKey('id', $type);
            $this->assertArrayHasKey('key', $type);
            $this->assertArrayHasKey('label', $type);
        }
    }

    /**
     * @expectedException Goracash\Service\Exception
     */
    public function testGetAttachedNumbers_invalidDate()
    {
        $params = array(
            'date' => 'invalid format',
        );
        $this->Service->getAttachedNumbers($params);
    }

    public function testGetAttachedNumbers()
    {
        $numbers = $this->Service->getAttachedNumbers();
        $this->assertInternalType('array', $numbers);
        $this->assertGreaterThan(0, count($numbers));
        foreach ($numbers as $number) {
            $this->assertArrayHasKey('id', $number);
            $this->assertArrayHasKey('type', $number);
            $this->assertArrayHasKey('thematic', $number);
            $this->assertArrayHasKey('market', $number);
            $this->assertArrayHasKey('value', $number);
        }
    }

    public function testGetAttachedNumbers_withParams()
    {
        $params = array(
            'type' => 'PAID'
        );
        $numbers = $this->Service->getAttachedNumbers($params);
        $this->assertInternalType('array', $numbers);
        $this->assertGreaterThan(0, count($numbers));
        foreach ($numbers as $number) {
            $this->assertArrayHasKey('id', $number);
            $this->assertArrayHasKey('type', $number);
            $this->assertEquals('Payant', $number['type']);
            $this->assertArrayHasKey('thematic', $number);
            $this->assertArrayHasKey('market', $number);
            $this->assertArrayHasKey('value', $number);
        }
    }

    /**
     * @expectedException Goracash\Service\Exception
     */
    public function testPushCallback_invalidCaller()
    {
        $this->Service->pushCallback('invalidNumber', '0033601010101');
    }

    /**
     * @expectedException Goracash\Service\Exception
     */
    public function testPushCallback_invalidNumber()
    {
        $this->Service->pushCallback('0033175752585', 'invalidNumber');
    }

    public function testPushCallback()
    {
        $num = '';
        for ($a = 0; $a < 8; $a++) {
            $num .= rand(0, 9);
        }

        $data = $this->Service->pushCallback('0033175752585', '00336' . $num);
        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('ok', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals('', $data['message']);

        $data = $this->Service->pushCallback('0033175752585', '00336' . $num);
        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('ok', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals('already_exist', $data['message']);
    }


}
