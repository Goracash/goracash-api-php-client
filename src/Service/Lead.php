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
use Goracash\Utils as Utils;

class Lead extends Service
{

    /**
     * Get leads from specific period
     * @param $start_date
     * @param $end_date
     * @param $params array
     * @return array
     * @throws Exception
     */
    public function getLeads($start_date, $end_date, array $params = array())
    {
        $is_valid_start_date = Utils::is_system_datetime($start_date);
        $is_valid_end_date = Utils::is_system_datetime($end_date);
        if (!$is_valid_end_date || !$is_valid_start_date) {
            throw new Exception('Invalid params: Only system date has available YYYY-MM-DDD HH:II:SS');
        }

        $is_out_of_limit = Utils::is_out_of_limit($start_date, $end_date, LeadAcademic::LIMIT_PERIOD);
        if ($is_out_of_limit) {
            throw new Exception('Invalid params: Period is too large. Available only ' . LeadAcademic::LIMIT_PERIOD);
        }

        $params['date_lbound'] = $start_date;
        $params['date_ubound'] = $end_date;

        $this->normalizeParams($params);
        $response = $this->execute('/', $params);
        $data = $this->normalize($response);
        return $data['leads'];
    }

    /**
     * @param $id
     * @return array
     * @throws Exception
     */
    public function getLead($id)
    {
        if (!is_numeric($id)) {
            throw new Exception('Invalid params: Id of lead is numeric');
        }

        $response = $this->execute('/' . $id . '/');
        $data = $this->normalize($response);
        return $data['lead'];
    }

}