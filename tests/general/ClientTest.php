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
use Goracash\Config as Config;

class ClientTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Goracash\Client
     */
    public $Client;

    public function setUp()
    {
        $this->Client = new Client();
    }

    public function testConstruct()
    {
        // Empty
        $Client = new Client();
        $result = $Client->getApplicationName();
        $this->assertEquals('', $result);

        // String
        $configFile = dirname(__FILE__) . '/../testdata/test.ini';
        $Client = new Client($configFile);
        $result = $Client->getApplicationName();
        $this->assertEquals('My Test application', $result);

        // Config
        $Config = new Config();
        $Config->setApplicationName('myNewApplication');
        $Client = new Client($Config);
        $result = $Client->getApplicationName();
        $this->assertEquals('myNewApplication', $result);
    }

    public function testSetClientID()
    {
        $this->Client->setClientId('myClientId');
        $result = $this->Client->getClassConfig('Goracash\Auth\OAuth2', 'client_id');
        $this->assertEquals('myClientId', $result);
    }

    public function testSetClientSecret()
    {
        $this->Client->setClientSecret('myClientSecret');
        $result = $this->Client->getClassConfig('Goracash\Auth\OAuth2', 'client_secret');
        $this->assertEquals('myClientSecret', $result);
    }

    public function testGetApplicationName()
    {
        $result = $this->Client->getApplicationName();
        $this->assertEquals('', $result);
    }

    public function testSetApplicationName()
    {
        $this->Client->setApplicationName('myApplicationName');
        $result = $this->Client->getApplicationName();
        $this->assertEquals('myApplicationName', $result);
    }


    public function testGetClassConfig()
    {
        $result = $this->Client->getClassConfig('Not existed class');
        $this->assertNull($result);

        $result = $this->Client->getClassConfig('Goracash\Auth\OAuth2');
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('client_id', $result);
        $this->assertEquals('', $result['client_id']);
        $this->assertArrayHasKey('client_secret', $result);
        $this->assertEquals('', $result['client_secret']);

        $result = $this->Client->getClassConfig('Goracash\Auth\OAuth2', 'client_id');
        $this->assertEquals('', $result);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testGetClassConfigInvalidKey()
    {
        $this->Client->getClassConfig('Goracash\Auth\OAuth2', 'Not existed key');
    }

    public function testSetClassConfigStringEmptyValue()
    {
        $this->Client->setClassConfig('myNewClass', 'myKey');
        $result = $this->Client->getClassConfig('myNewClass');
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('myKey', $result);
        $this->assertNull($result['myKey']);

        $result = $this->Client->getClassConfig('myNewClass', 'myKey');
        $this->assertNull($result);
    }

    public function testSetClassConfigStringNotEmptyValue()
    {
        $this->Client->setClassConfig('myNewClass', 'myKey', 'myValue');
        $result = $this->Client->getClassConfig('myNewClass');
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('myKey', $result);

        $result = $this->Client->getClassConfig('myNewClass', 'myKey');
        $this->assertEquals('myValue', $result);
    }

    public function testSetClassConfigArray()
    {
        $params = array(
            'myKey1' => 'myValue1',
            'myKey2' => 'myValue2',
        );
        $this->Client->setClassConfig('myNewClass', $params);
        $result = $this->Client->getClassConfig('myNewClass');
        $this->assertInternalType('array', $result);
        foreach ($params as $key => $value) {
            $this->assertArrayHasKey($key, $result);
            $this->assertEquals($value, $result[$key]);

            $value_result = $this->Client->getClassConfig('myNewClass', $key);
            $this->assertEquals($value, $value_result );
        }
    }

    public function testGetBasePath()
    {
        $result = $this->Client->getBasePath();
        $this->assertEquals('https://ws.goracash.com', $result);
    }

    public function testGetLibrary()
    {
        $Client = new Client();
        $result = $Client->getLibraryVersion();
        $this->assertEquals(Client::LIBVER, $result);
    }

}