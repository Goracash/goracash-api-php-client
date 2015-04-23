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

use Goracash\Logger\Primary as GoracashLogger;

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
     * Set the OAuth 2.0 Client Token.
     * @param string $access_token
     * @param string $access_token_limit
     */
    public function setAccessToken($access_token, $access_token_limit = null)
    {
        $this->config->setAccessToken($access_token, $access_token_limit);
    }

    /**
     * Get the OAuth 2.0 Client ID.
     * @return string $token
     */
    public function getClientId()
    {
        return $this->config->getClientId();
    }

    /**
     * Get the OAuth 2.0 Client Secret.
     * @return string $token
     */
    public function getClientSecret()
    {
        return $this->config->getClientSecret();
    }

    /**
     * Get the OAuth 2.0 Client Token.
     * @return string $token
     */
    public function getAccessToken()
    {
        return $this->config->getAccessToken();
    }

    /**
     * Get the OAuth 2.0 Client Token.
     * @return string $token
     */
    public function getAccessTokenLimit()
    {
        return $this->config->getAccessTokenLimit();
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
     * Set the Logger object
     * @param GoracashLogger $logger
     */
    public function setLogger(GoracashLogger $logger)
    {
        $this->config->setLoggerClass(get_class($logger));
        $this->logger = $logger;
    }

    /**
     * @return GoracashLogger Logger implementation
     */
    public function getLogger()
    {
        if (!isset($this->logger)) {
            $class = $this->config->getLoggerClass();
            $this->logger = new $class($this);
        }
        return $this->logger;
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