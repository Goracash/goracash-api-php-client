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
use Goracash\IO\Exception;
use Goracash\Utils;

class LeadAcademic extends Lead
{

    /**
     * @param Client $Client
     */
    public function __construct(Client $Client)
    {
        parent::__construct($Client);

        $this->version = 'v1';
        $this->serviceName = 'leadAcademic';
        $this->servicePath = '/v1/lead/academic/';
    }

    /**
     * @return array
     */
    public function getAvailableLevels()
    {
        $response = $this->execute('/levels');
        $data = $this->normalize($response);
        return $data['levels'];
    }

    /**
     * @return array
     */
    public function getAvailableSubjects()
    {
        $response = $this->execute('/subjects');
        $data = $this->normalize($response);
        return $data['subjects'];
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
            'gender' => '',
            'firstname' => '',
            'lastname' => '',
            'email' => '',
            'phone' => '',
            'child_name' => '',
            'subject' => '',
            'level' => '',
            'tracker' => '',
            'zipcode' => '',
            'city' => '',
        );
        $fields = array_merge($available_fields, $fields);
        $fields = array_intersect_key($fields, $available_fields);
        return $fields;
    }

    public function checkFormFields(array &$fields)
    {
        $required_fields = array('gender', 'firstname', 'lastname', 'email', 'phone', 'child_name', 'subject', 'level', 'zipcode', 'city');
        foreach ($required_fields as $required_field) {
            if (Utils::isEmpty($fields[$required_field])) {
                throw new Exception('Empty field ' . $required_field);
            }
        }
        if (!Utils::isEmail($fields['email'])) {
            throw new Exception('Invalid email');
        }
        if (!Utils::isZipcode($fields['zipcode'])) {
            throw new Exception('Invalid zipcode');
        }
    }

    public function normalizeParams(array &$params)
    {
        $available_params = array(
            'date_lbound' => '',
            'date_ubound' => '',
            'tracker' => 0,
            'trackers' => array(),
            'level' => '',
            'levels' => array(),
            'subject' => '',
            'subjects' => array(),
            'status' => '',
            'statuses' => array(),
            'limit' => LeadAcademic::LIMIT,
            'offset' => 0,
        );
        $params = array_merge($available_params, $params);
        $params = array_intersect_key($params, $available_params);

        $this->normalizeArray($params, (array)$params['trackers'], 'trackers');
        $this->normalizeArray($params, (array)$params['levels'], 'levels');
        $this->normalizeArray($params, (array)$params['subjects'], 'subjects');

        if ($params['limit'] > LeadAcademic::LIMIT) {
            throw new Exception('Invalid params: Limit is too large. Available only < ' . LeadAcademic::LIMIT);
        }
        return $params;
    }

    public function normalizeArray(&$params, array $values, $params_key)
    {
        foreach ($values as $key => $value) {
            $array_key = $params_key . "[" . urlencode($key) . "]";
            if (is_array($value)) {
                $this->normalizeArray($params, $value, $array_key);
                continue;
            }
            $params[$array_key] = $value;
        }
        unset($params[$params_key]);
    }

}