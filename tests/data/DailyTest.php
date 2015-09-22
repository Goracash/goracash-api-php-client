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
use Goracash\Service\DataDaily as Daily;

class DailyTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Client
     */
    public $Client;

    /**
     * @var Daily
     */
    public $Service;

    public function setUp()
    {
        $configPath = dirname(__FILE__) . '/../testdata/test.ini';
        $this->Client = new Client($configPath);
        $this->Client->authenticate();
        $this->Service = new Daily($this->Client);
    }

    public function testGetSigns()
    {
        $signs = $this->Service->getAvailableSigns();
        $this->assertInternalType('array', $signs);
        $this->assertGreaterThan(0, count($signs));
        foreach ($signs as $sign) {
            $this->assertArrayHasKey('id', $sign);
            $this->assertArrayHasKey('key', $sign);
            $this->assertArrayHasKey('label', $sign);
        }
    }
}