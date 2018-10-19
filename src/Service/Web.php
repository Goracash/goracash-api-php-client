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

namespace Goracash\Service;

use Goracash\Service as Service;

class Web extends Service
{
    public $serviceName = 'Web';

    public $servicePath = '/v1/web/';

    const LIMIT_PERIOD = '1 month';

    /**
     * @return array
     */
    public function getAvailableThematics()
    {
        $response = $this->execute('/thematics');
        $data = $this->normalize($response);
        return $data['thematics'];
    }

    /**
     * @return array
     */
    public function getAvailableMarkets()
    {
        $response = $this->execute('/markets');
        $data = $this->normalize($response);
        return $data['markets'];
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function getWebCBStats($startDate, $endDate, $params = [])
    {
        $this->checkPeriod($startDate, $endDate);

        $params['date_lbound'] = $startDate;
        $params['date_ubound'] = $endDate;

        $response = $this->execute('/cbStats', $params);
        $data = $this->normalize($response);
        return $data['stats'];
    }
    /**
     * @param $startDate
     * @param $endDate
     * @throws InvalidArgumentException
     */
    public function checkPeriod($startDate, $endDate)
    {
        $isValidStartDate = $this->utils->isSystemDatetime($startDate);
        $isValidEndDate = $this->utils->isSystemDatetime($endDate);
        if (!$isValidEndDate || !$isValidStartDate) {
            throw new InvalidArgumentException('Invalid params: Only system date has available YYYY-MM-DDD HH:II:SS');
        }

        if ($startDate > $endDate) {
            throw new InvalidArgumentException('Invalid params: start_date > end_date');
        }

        $isOutOfLimit = $this->utils->isOutOfLimit($startDate, $endDate, Phone::LIMIT_PERIOD);
        if ($isOutOfLimit) {
            throw new InvalidArgumentException('Invalid params: Period is too large. Available only ' . Phone::LIMIT_PERIOD);
        }
    }

    /**
     * @param array $params
     * @return array
     */
    public function normalizeParams(array &$params)
    {
        $availableParams = array(
            'date' => '',
            'market' => 0,
            'markets' => array(),
            'thematic' => '',
            'thematics' => array(),
            'tracker' => '',
            'trackers' => array(),
        );
        $params = array_merge($availableParams, $params);
        $params = array_intersect_key($params, $availableParams);

        $this->normalizeArray($params, (array)$params['markets'], 'markets');
        $this->normalizeArray($params, (array)$params['thematics'], 'thematics');
        $this->normalizeArray($params, (array)$params['trackers'], 'trackers');

        return $params;
    }

}