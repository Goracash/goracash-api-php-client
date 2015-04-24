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
use Goracash\Service\LeadAcademic as LeadAcademic;

class LeadAcademicTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Client
     */
    public $Client;

    /**
     * @var LeadAcademic
     */
    public $Service;

    public function setUp()
    {
        $configPath = dirname(__FILE__) . '/../testdata/test.ini';
        $this->Client = new Client($configPath);
        $this->Client->authenticate();
        $this->Service = new LeadAcademic($this->Client);
    }

    public function testGetLevels()
    {
        $levels = $this->Service->getAvailableLevels();
        $this->assertInternalType('array', $levels);
        $this->assertGreaterThan(0, count($levels));
        foreach ($levels as $level) {
            $this->assertArrayHasKey('id', $level);
            $this->assertArrayHasKey('key', $level);
            $this->assertArrayHasKey('label', $level);
        }
    }

    public function testGetSubjects()
    {
        $subjects = $this->Service->getAvailableSubjects();
        $this->assertInternalType('array', $subjects);
        $this->assertGreaterThan(0, count($subjects));
        foreach ($subjects as $subject) {
            $this->assertArrayHasKey('id', $subject);
            $this->assertArrayHasKey('key', $subject);
            $this->assertArrayHasKey('label', $subject);
        }
    }

    public function testGetLeads()
    {
        $leads = $this->Service->getLeads('2013-12-20 00:00:00', '2013-12-25 00:00:00');
        $this->assertInternalType('array', $leads);
        $this->assertGreaterThan(0, count($leads));
        foreach ($leads as $lead) {
            $this->assertInternalType('array', $lead);
            $this->assertArrayHasKey('id', $lead);
            $this->assertArrayHasKey('status', $lead);
            $this->assertArrayHasKey('status_date', $lead);
            $this->assertArrayHasKey('date', $lead);
            $this->assertArrayHasKey('level', $lead);
            $this->assertArrayHasKey('subject', $lead);
            $this->assertArrayHasKey('payout', $lead);
            $this->assertArrayHasKey('payout_date', $lead);
            $this->assertArrayHasKey('trackers', $lead);
        }
    }

    public function testGetLead()
    {
        $lead = $this->Service->getLead(1574660);
        $this->assertInternalType('array', $lead);
        $this->assertArrayHasKey('id', $lead);
        $this->assertArrayHasKey('status', $lead);
        $this->assertArrayHasKey('status_date', $lead);
        $this->assertArrayHasKey('date', $lead);
        $this->assertArrayHasKey('level', $lead);
        $this->assertArrayHasKey('subject', $lead);
        $this->assertArrayHasKey('payout', $lead);
        $this->assertArrayHasKey('payout_date', $lead);
        $this->assertArrayHasKey('trackers', $lead);
    }

}