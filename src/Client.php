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

namespace Goracash;
use Goracash\Config as Config;

if (!class_exists('\Goracash\Client')) {
    require_once dirname(__FILE__) . '/autoload.php';
}

/**
 * The Goracash API Client
 */
class Client
{
    const LIBVER = "1.0.0";
    const USER_AGENT_SUFFIX = "goracash-api-php-client/";

    /**
     * @var Goracash\Config $config
     */
    private $config;

    // Used to track authenticated state, can't discover services after doing authenticate()
    protected $authenticated = false;

    /**
     * Construct the Goracash Client
     *
     * @param $config (Goracash\Config or string for ini file to load)
     */
    public function __construct($config = null)
    {
        if (is_string($config) && strlen($config)) {
            $config = new Config($config);
        }
        else if ( !($config instanceof Config)) {
            $config = new Config();
        }

        $this->config = $config;
    }

    /**
     * Set the OAuth 2.0 Client ID.
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->config->setClientId($clientId);
    }

    /**
     * Set the OAuth 2.0 Client Secret.
     * @param string $clientSecret
     */
    public function setClientSecret($clientSecret)
    {
        $this->config->setClientSecret($clientSecret);
    }

    /**
     * Retrieve custom configuration for a specific class.
     * @param $class string|object - class or instance of class to retrieve
     * @param $key string optional - key to retrieve
     * @return array
     */
    public function getClassConfig($class, $key = null)
    {
        if (!is_string($class)) {
            $class = get_class($class);
        }
        return $this->config->getClassConfig($class, $key);
    }

    /**
     * Set configuration specific to a given class.
     * @param $class string|object - The class name for the configuration
     * @param $config string key or an array of configuration values
     * @param $value string optional - if $config is a key, the value
     *
     */
    public function setClassConfig($class, $config, $value = null)
    {
        if (!is_string($class)) {
            $class = get_class($class);
        }
        $this->config->setClassConfig($class, $config, $value);
    }

    /**
     * @return string the base URL to use for calls to the APIs
     */
    public function getBasePath()
    {
        return $this->config->getBasePath();
    }

    /**
     * Set the application name, this is included in the User-Agent HTTP header.
     * @param string $applicationName
     */
    public function setApplicationName($applicationName)
    {
        $this->config->setApplicationName($applicationName);
    }

    /**
     * @return string the name of the application
     */
    public function getApplicationName()
    {
        return $this->config->getApplicationName();
    }

    /**
     * Get a string containing the version of the library.
     *
     * @return string
     */
    public function getLibraryVersion()
    {
        return self::LIBVER;
    }

}