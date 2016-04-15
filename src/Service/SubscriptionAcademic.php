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

class SubscriptionAcademic extends Subscription
{

    /**
     * @param Client $Client
     */
    public function __construct(Client $Client)
    {
        parent::__construct($Client);

        $this->version = 'v1';
        $this->serviceName = 'subscriptionAcademic';
        $this->servicePath = '/v1/subscription/academic/';
    }

    /**
     * @return array
     */
    public function getAvailableChildLevels()
    {
        $response = $this->execute('/childLevels');
        $data = $this->normalize($response);
        return $data['child_levels'];
    }

    /**
     * @return array
     */
    public function getAvailableOffers()
    {
        $response = $this->execute('/offers');
        $data = $this->normalize($response);
        return $data['offers'];
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
     * @return array
     */
    public function pushSubscription(array $fields)
    {
        $this->normalizeFormFields($fields);
        $this->checkFormFields($fields);
        $response = $this->execute('/create', $fields, 'POST');
        $data = $this->normalize($response);
        return array(
            'id' => $data['id'],
            'status' => $data['subscription_status'],
            'redirect_url' => $data['redirect_url'],
        );
    }

    public function pushLeadAndRedirect(array $fields)
    {
        $result = $this->pushSubscription($fields);
        if ($result['status'] != 'ok') {
            throw new Exception('Subscription #' . $result['id'] . ' on status ' . $result['status']);
        }
        $this->redirectTo($result['redirect_url']);
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
            'children' => array(),
            'offer' => '',
            'tracker' => '',
        );
        $available_child_fields = array(
            'firstname' => '',
            'level' => '',
        );
        $fields = array_merge($available_fields, $fields);
        $fields = array_intersect_key($fields, $available_fields);
        foreach ($fields['children'] as $i => $child_fields) {
            $child_fields = array_merge($available_child_fields, $child_fields);
            $fields['children'][$i] = array_intersect_key($child_fields, $available_child_fields);
        }
        $this->normalizeArray($fields, $fields['children'], 'children');
        return $fields;
    }

    public function checkFormFields(array &$fields)
    {
        $required_fields = array('gender', 'firstname', 'lastname', 'email', 'phone', 'offer');
        foreach ($required_fields as $required_field) {
            if (Utils::isEmpty($fields[$required_field])) {
                throw new InvalidArgumentException('Empty field ' . $required_field);
            }
        }
        if (!Utils::isEmail($fields['email'])) {
            throw new InvalidArgumentException('Invalid email');
        }
        $exist = false;
        for ($i = 0; $i < 5; $i++) {
            $firstname_field = sprintf('children[%s][firstname]', $i);
            $level_field = sprintf('children[%s][level]', $i);
            if (!array_key_exists($firstname_field, $fields)) {
                break;
            }
            $exist = true;
            if (Utils::isEmpty($fields[$firstname_field])) {
                throw new InvalidArgumentException('Empty child #' . $i . ' field firstname');
            }
            if (!$fields[$level_field]) {
                throw new InvalidArgumentException('Empty child #' . $i . ' field level');
            }
        }
        if (!$exist) {
            throw new InvalidArgumentException('Empty field children');
        }
    }

}