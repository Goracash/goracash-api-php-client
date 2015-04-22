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

use Goracash\Config as Config;

class ConfigtTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Goracash\Config
     */
    public $Config;

    public function setUp()
    {
        $this->Config = new Config();
    }

    public function testConstruct()
    {
        $configPath = dirname(__FILE__) . '/../testdata/test.ini';
        $Config = new Config($configPath);

        $result = $Config->getApplicationName();
        $this->assertEquals('My Test application', $result);

        $result = $Config->getAuthClass();
        $this->assertEquals('Goracash\Auth\Other', $result);

        $result = $Config->getClassConfig('Goracash\Auth\OAuth2', 'client_id');
        $this->assertEquals('1234.testClientId', $result);

        $result = $Config->getClassConfig('Goracash\Auth\OAuth2', 'client_secret');
        $this->assertEquals('1234.testClientSecret', $result);

        $result = $Config->getBasePath();
        $this->assertEquals('https://ws.goracash.com', $result);
    }

    public function testGetClassConfig()
    {
        $result = $this->Config->getClassConfig('Not existed class');
        $this->assertNull($result);

        $result = $this->Config->getClassConfig('Goracash\Auth\OAuth2');
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('client_id', $result);
        $this->assertEquals('', $result['client_id']);
        $this->assertArrayHasKey('client_secret', $result);
        $this->assertEquals('', $result['client_secret']);

        $result = $this->Config->getClassConfig('Goracash\Auth\OAuth2', 'client_id');
        $this->assertEquals('', $result);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testGetClassConfigInvalidKey()
    {
        $this->Config->getClassConfig('Goracash\Auth\OAuth2', 'Not existed key');
    }

    public function testSetClassConfigStringEmptyValue()
    {
        $this->Config->setClassConfig('myNewClass', 'myKey');
        $result = $this->Config->getClassConfig('myNewClass');
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('myKey', $result);
        $this->assertNull($result['myKey']);

        $result = $this->Config->getClassConfig('myNewClass', 'myKey');
        $this->assertNull($result);
    }

    public function testSetClassConfigStringNotEmptyValue()
    {
        $this->Config->setClassConfig('myNewClass', 'myKey', 'myValue');
        $result = $this->Config->getClassConfig('myNewClass');
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('myKey', $result);

        $result = $this->Config->getClassConfig('myNewClass', 'myKey');
        $this->assertEquals('myValue', $result);
    }

    public function testSetClassConfigArray()
    {
        $params = array(
            'myKey1' => 'myValue1',
            'myKey2' => 'myValue2',
        );
        $this->Config->setClassConfig('myNewClass', $params);
        $result = $this->Config->getClassConfig('myNewClass');
        $this->assertInternalType('array', $result);
        foreach ($params as $key => $value) {
            $this->assertArrayHasKey($key, $result);
            $this->assertEquals($value, $result[$key]);

            $value_result = $this->Config->getClassConfig('myNewClass', $key);
            $this->assertEquals($value, $value_result );
        }
    }

    public function testGetAuthClass()
    {
        $result = $this->Config->getAuthClass();
        $this->assertEquals('Goracash\Auth\OAuth2', $result);
    }

    public function testSetAuthClass()
    {
        $this->Config->setAuthClass('myPersonalClass');
        $result = $this->Config->getAuthClass();
        $this->assertEquals('myPersonalClass', $result);
    }

    public function testGetIoClass()
    {
        $result = $this->Config->getIoClass();
        $this->assertEquals(Config::USE_AUTO_IO_SELECTION, $result);
    }

    public function testSetIoClass()
    {
        $this->Config->setIoClass('myPersonalClass');
        $result = $this->Config->getIoClass();
        $this->assertEquals('myPersonalClass', $result);
    }

    public function testGetApplicationName()
    {
        $result = $this->Config->getApplicationName();
        $this->assertEquals('', $result);
    }

    public function testSetApplicationName()
    {
        $this->Config->setApplicationName('myApplicationName');
        $result = $this->Config->getApplicationName();
        $this->assertEquals('myApplicationName', $result);
    }

    public function testSetClientId()
    {
        $this->Config->setClientId('myClientId');
        $result = $this->Config->getClassConfig('Goracash\Auth\OAuth2', 'client_id');
        $this->assertEquals('myClientId', $result);
    }

    public function testSetClientSecret()
    {
        $this->Config->setClientSecret('myClientSecret');
        $result = $this->Config->getClassConfig('Goracash\Auth\OAuth2', 'client_secret');
        $this->assertEquals('myClientSecret', $result);
    }

    public function testGetBasePath()
    {
        $result = $this->Config->getBasePath();
        $this->assertEquals('https://ws.goracash.com', $result);
    }

}