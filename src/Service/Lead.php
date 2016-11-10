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

abstract class Lead extends Service
{
    const LIMIT_PERIOD = '1 week';
    const LIMIT = 50;

    /**
     * Get leads from specific period
     * @param $startDate
     * @param $endDate
     * @param $params array
     * @return array
     * @throws InvalidArgumentException
     */
    public function getLeads($startDate, $endDate, array $params = array())
    {
        $isValidStartDate = $this->utils->isSystemDatetime($startDate);
        $isValidEndDate = $this->utils->isSystemDatetime($endDate);
        if (!$isValidEndDate || !$isValidStartDate) {
            throw new InvalidArgumentException('Invalid params: Only system date has available YYYY-MM-DDD HH:II:SS');
        }

        if ($startDate > $endDate) {
            throw new InvalidArgumentException('Invalid params: start_date > end_date');
        }

        $isOutOfLimit = $this->utils->isOutOfLimit($startDate, $endDate, LeadAcademic::LIMIT_PERIOD);
        if ($isOutOfLimit) {
            throw new InvalidArgumentException('Invalid params: Period is too large. Available only ' . LeadAcademic::LIMIT_PERIOD);
        }

        $params['date_lbound'] = $startDate;
        $params['date_ubound'] = $endDate;

        $this->normalizeParams($params);
        $response = $this->execute('/', $params);
        $data = $this->normalize($response);
        return $data['leads'];
    }

    /**
     * @param array $params
     * @return mixed
     */
    abstract public function normalizeParams(array &$params);

    /**
     * @param $leadId
     * @return array
     * @throws InvalidArgumentException
     */
    public function getLead($leadId)
    {
        if (!is_numeric($leadId)) {
            throw new InvalidArgumentException('Invalid params: Id of lead is numeric');
        }

        $response = $this->execute('/' . $leadId . '/');
        $data = $this->normalize($response);
        return $data['lead'];
    }

}