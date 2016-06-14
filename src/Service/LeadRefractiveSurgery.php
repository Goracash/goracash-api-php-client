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

use Goracash\Client as Client;
use Goracash\Utils as Utils;

class LeadRefractiveSurgery extends Lead
{
    /**
     * @param Client $Client
     */
    public function __construct(Client $Client)
    {
        parent::__construct($Client);

        $this->version = 'v1';
        $this->serviceName = 'leadRefractiveSurgery';
        $this->servicePath = '/v1/lead/refractive_surgery/';
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
     * @param array $fields
     * @return integer
     */
    public function pushLead(array $fields)
    {
        $this->normalizeFormFields($fields);
        $this->checkFormFields($fields);
        $response = $this->execute('/create', $fields, 'POST');
        $data = $this->normalize($response);
        return $data['id'];
    }

    /**
     * @param array $fields
     * @return array
     */
    public function normalizeFormFields(array &$fields)
    {
        $available_fields = array(
            'firstname' => '',
            'lastname' => '',
            'email' => '',
            'phone' => '',
            'type' => '',
            'birth_date' => '',
            'tracker' => '',
            'zipcode' => '',
            'city' => '',
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
        $required_fields = array('firstname', 'lastname', 'email', 'phone', 'type', 'birth_date', 'zipcode', 'city');
        foreach ($required_fields as $required_field) {
            if (Utils::isEmpty($fields[$required_field])) {
                throw new InvalidArgumentException('Empty field ' . $required_field);
            }
        }
        if (!Utils::isEmail($fields['email'])) {
            throw new InvalidArgumentException('Invalid email');
        }
        if (!Utils::isZipcode($fields['zipcode'])) {
            throw new InvalidArgumentException('Invalid zipcode');
        }
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
            'tracker' => 0,
            'trackers' => array(),
            'type' => '',
            'types' => array(),
            'status' => '',
            'limit' => static::LIMIT,
            'offset' => 0,
        );
        $params = array_merge($available_params, $params);
        $params = array_intersect_key($params, $available_params);

        $this->normalizeArray($params, (array)$params['trackers'], 'trackers');
        $this->normalizeArray($params, (array)$params['types'], 'types');

        if ($params['limit'] > static::LIMIT) {
            throw new InvalidArgumentException('Invalid params: Limit is too large. Available only < ' . static::LIMIT);
        }
        return $params;
    }

}
