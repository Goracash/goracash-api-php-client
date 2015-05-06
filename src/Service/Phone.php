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
use Goracash\Client as Client;
use Goracash\Utils as Utils;

class Phone extends Service
{
    /**
     * @param Client $Client
     */
    public function __construct(Client $Client)
    {
        parent::__construct($Client);

        $this->version = 'v1';
        $this->serviceName = 'Phone';
        $this->servicePath = '/v1/phone/';
    }

    /**
     * @return array
     */
    public function getAvailableCountries()
    {
        $response = $this->execute('/countries');
        $data = $this->normalize($response);
        return $data['countries'];
    }

    /**
     * @return array
     */
    public function getAvailableTypes()
    {
        $response = $this->execute('/types');
        $data = $this->normalize($response);
        return $data['types'];
    }

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
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function getAttachedNumbers(array $params = array())
    {
        $this->normalizeParams($params);
        $this->checkParams($params);
        $response = $this->execute('/numbers', $params);
        $data = $this->normalize($response);
        return $data['numbers'];
    }

    /**
     * @param $caller string: Caller number in International format
     * @param $number string: Called number in International format
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function pushCallback($caller, $number, array $params = array())
    {
        $params['caller'] = $caller;
        $params['phone'] = $number;
        $this->normalizeCallbackParams($params);
        $this->checkCallbackParams($params);
        $response = $this->execute('/callback', $params);
        $data = $this->normalize($response);
        return $data['callback_status'];
    }

    public function normalizeCallbackParams(array &$params)
    {
        $available_params = array(
            'caller' => '',
            'phone' => '',
            'gender' => '',
            'firstname' => '',
            'lastname' => '',
            'tracker' => '',
        );
        $params = array_merge($available_params, $params);
        $params = array_intersect_key($params, $available_params);
        return $params;
    }

    public function checkCallbackParams(array &$params)
    {
        if (!Utils::isInternationalNumber($params['caller'])) {
            throw new Exception('Invalid params: Caller is not in internation format (ex: 0033175757575)');
        }
        if (!Utils::isInternationalNumber($params['phone'])) {
            throw new Exception('Invalid params: Phone is not in internation format (ex: 0033175757575)');
        }
    }

    public function checkParams(array &$params)
    {
        if (!Utils::isEmpty($params['date']) && !Utils::isSystemDate($params['date'])) {
            throw new Exception('Invalid params: Only system date has available YYYY-MM-DDD');
        }
    }

    public function normalizeParams(array &$params)
    {
        $available_params = array(
            'date' => '',
            'market' => 0,
            'markets' => array(),
            'type' => '',
            'types' => array(),
            'thematic' => '',
            'thematics' => array(),
            'country' => '',
            'countries' => array(),
        );
        $params = array_merge($available_params, $params);
        $params = array_intersect_key($params, $available_params);

        $this->normalizeArray($params, (array)$params['markets'], 'markets');
        $this->normalizeArray($params, (array)$params['types'], 'types');
        $this->normalizeArray($params, (array)$params['thematics'], 'thematics');
        $this->normalizeArray($params, (array)$params['countries'], 'countries');

        return $params;
    }
}