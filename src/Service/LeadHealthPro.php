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

class LeadHealthPro extends Lead
{
    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);

        $this->version = 'v1';
        $this->serviceName = 'leadHealthPro';
        $this->servicePath = '/v1/lead/health_pro/';
    }

    /**
     * @return array
     */
    public function getAvailableTitles()
    {
        $response = $this->execute('/titles');
        $data = $this->normalize($response);
        return $data['titles'];
    }

    /**
     * @return array
     */
    public function getAvailableProfessions()
    {
        $response = $this->execute('/professions');
        $data = $this->normalize($response);
        return $data['professions'];
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
        $availableFields = array(
            'title' => '',
            'firstname' => '',
            'lastname' => '',
            'email' => '',
            'phone' => '',
            'profession' => '',
            'tracker' => '',
            'zipcode' => '',
            'city' => '',
        );
        $fields = array_merge($availableFields, $fields);
        $fields = array_intersect_key($fields, $availableFields);
        return $fields;
    }

    /**
     * @param array $fields
     * @throws InvalidArgumentException
     */
    public function checkFormFields(array &$fields)
    {
        $requiredFields = array('title', 'firstname', 'lastname', 'email', 'phone', 'profession', 'zipcode', 'city');
        foreach ($requiredFields as $requiredField) {
            if ($this->utils->isEmpty($fields[$requiredField])) {
                throw new InvalidArgumentException('Empty field ' . $requiredField);
            }
        }
        if (!$this->utils->isEmail($fields['email'])) {
            throw new InvalidArgumentException('Invalid email');
        }
        if (!$this->utils->isZipcode($fields['zipcode'])) {
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
        $availableParams = array(
            'date_lbound' => '',
            'date_ubound' => '',
            'tracker' => 0,
            'trackers' => array(),
            'profession' => '',
            'professions' => array(),
            'status' => '',
            'limit' => static::LIMIT,
            'offset' => 0,
        );
        $params = array_merge($availableParams, $params);
        $params = array_intersect_key($params, $availableParams);

        $this->normalizeArray($params, (array)$params['trackers'], 'trackers');
        $this->normalizeArray($params, (array)$params['professions'], 'professions');

        if ($params['limit'] > static::LIMIT) {
            throw new InvalidArgumentException('Invalid params: Limit is too large. Available only < ' . static::LIMIT);
        }
        return $params;
    }

}
