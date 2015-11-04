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
use Goracash\Client as Client;

class Contact extends Service
{

    /**
     * @param Client $Client
     */
    public function __construct(Client $Client)
    {
        parent::__construct($Client);

        $this->version = 'v1';
        $this->serviceName = 'contact';
        $this->servicePath = '/v1/contact/';
    }

    /**
     * @return array
     */
    public function getAvailableGenders()
    {
        $response = $this->execute('/genders');
        $data = $this->normalize($response);
        return $data['genders'];
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
     * @param array $fields
     * @return array
     */
    public function pushContact(array $fields)
    {
        $this->normalizeFormFields($fields);
        $this->checkFormFields($fields);
        $response = $this->execute('/create', $fields, 'POST');
        $data = $this->normalize($response);
        return $data;
    }

    /**
     * @param array $params
     * @return int
     * @throws Exception
     */
    public function getDoubleOptinCount(array $params = array())
    {
        $this->normalizeParams($params);
        $response = $this->execute('/count', $params);
        $data = $this->normalize($response);
        return (int)$data['count'];
    }

    /**
     * @param array $params
     * @return array
     * @throws InvalidArgumentException
     */
    public function normalizeParams(array &$params)
    {
        $available_params = array(
            'date_lbound' => '',
            'date_ubound' => '',
            'type' => '',
            'types' => array(),
            'tracker' => 0,
            'trackers' => array(),
            'market' => '',
            'markets' => array(),
            'thematic' => '',
            'thematics' => array(),
        );
        $params = array_merge($available_params, $params);
        $params = array_intersect_key($params, $available_params);

        $this->normalizeArray($params, (array)$params['trackers'], 'trackers');
        $this->normalizeArray($params, (array)$params['markets'], 'markets');
        $this->normalizeArray($params, (array)$params['thematics'], 'thematics');
        $this->normalizeArray($params, (array)$params['types'], 'types');

        if ($params['date_lbound'] && !Utils::isSystemDatetime($params['date_lbound'])) {
            throw new InvalidArgumentException('Invalid params: date_lbound not in format YYYY-MMM-DD HH:II:SS');
        }
        if ($params['date_ubound'] && !Utils::isSystemDatetime($params['date_ubound'])) {
            throw new InvalidArgumentException('Invalid params: date_ubound not in format YYYY-MMM-DD HH:II:SS');
        }
        if ($params['date_lbound'] && $params['date_ubound'] && $params['date_lbound'] > $params['date_ubound']) {
            throw new InvalidArgumentException('Invalid params: date_lbound > date_ubound');
        }
        return $params;
    }

    /**
     * @param array $fields
     * @return array
     */
    public function normalizeFormFields(array &$fields)
    {
        $available_fields = array(
            'gender' => '',
            'firstname' => '',
            'lastname' => '',
            'email' => '',
            'phone' => '',
            'thematic' => '',
            'market' => '',
        );
        $fields = array_merge($available_fields, $fields);
        $fields = array_intersect_key($fields, $available_fields);
        return $fields;
    }

    /**
     * @param array $fields
     * @throws InvalidArgumentException
     */
    public function checkFormFields(array &$fields)
    {
        $required_fields = array('gender', 'firstname', 'lastname', 'market', 'thematic');
        foreach ($required_fields as $required_field) {
            if (Utils::isEmpty($fields[$required_field])) {
                throw new InvalidArgumentException('Empty field ' . $required_field);
            }
        }
        if (Utils::isEmpty($fields['email']) && Utils::isEmpty($fields['phone'])) {
            throw new InvalidArgumentException('Empty email & phone');
        }
        if ($fields['email'] && !Utils::isEmail($fields['email'])) {
            throw new InvalidArgumentException('Invalid email');
        }
    }

}