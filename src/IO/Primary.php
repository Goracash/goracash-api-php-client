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

namespace Goracash\IO;

use Goracash\Client as Client;
use Goracash\Http\Request as Request;

abstract class Primary
{

    const UNKNOWN_CODE = 0;
    private static $CONNECTION_ESTABLISHED_HEADERS = array(
        "HTTP/1.0 200 Connection established\r\n\r\n",
        "HTTP/1.1 200 Connection established\r\n\r\n",
    );
    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $timeout = $client->getClassConfig('Goracash\IO\Primary', 'request_timeout_seconds');
        if ($timeout > 0) {
            $this->setTimeout($timeout);
        }
    }

    /**
     * Executes a Goracash Http Request
     * @param Request $request the http request to be executed
     * @return array containing response headers, body, and http code
     * @throws Exception on curl or IO error
     */
    abstract public function executeRequest(Request $request);

    /**
     * Set options that update the transport implementation's behavior.
     * @param $options
     */
    abstract public function setOptions($options);

    /**
     * Set the maximum request time in seconds.
     * @param $timeout in seconds
     */
    abstract public function setTimeout($timeout);

    /**
     * Get the maximum request time in seconds.
     * @return timeout in seconds
     */
    abstract public function getTimeout();

    /**
     * Test for the presence of a cURL header processing bug
     *
     * The cURL bug was present in versions prior to 7.30.0 and caused the header
     * length to be miscalculated when a "Connection established" header added by
     * some proxies was present.
     *
     * @return boolean
     */
    abstract protected function needsQuirk();

    /**
     * Used by the IO lib and also the batch processing.
     *
     * @param $respData
     * @param $headerSize
     * @return array
     */
    public function parseHttpResponse($respData, $headerSize)
    {
        // check proxy header
        foreach (self::$CONNECTION_ESTABLISHED_HEADERS as $established_header) {
            if (stripos($respData, $established_header) !== false) {
                // existed, remove it
                $respData = str_ireplace($established_header, '', $respData);
                // Subtract the proxy header size unless the cURL bug prior to 7.30.0
                // is present which prevented the proxy header size from being taken into
                // account.
                if (!$this->needsQuirk()) {
                    $headerSize -= strlen($established_header);
                }
                break;
            }
        }

        if ($headerSize) {
            $responseBody = substr($respData, $headerSize);
            $responseHeaders = substr($respData, 0, $headerSize);
        } else {
            $responseSegments = explode("\r\n\r\n", $respData, 2);
            $responseHeaders = $responseSegments[0];
            $responseBody = isset($responseSegments[1]) ? $responseSegments[1] :
                null;
        }

        $responseHeaders = $this->getHttpResponseHeaders($responseHeaders);
        return array($responseHeaders, $responseBody);
    }

    /**
     * Parse out headers from raw headers
     * @param $rawHeaders array or string
     * @return array
     */
    public function getHttpResponseHeaders($rawHeaders)
    {
        if (is_array($rawHeaders)) {
            return $this->parseArrayHeaders($rawHeaders);
        } else {
            return $this->parseStringHeaders($rawHeaders);
        }
    }

    private function parseStringHeaders($rawHeaders)
    {
        $headers = array();
        $responseHeaderLines = explode("\r\n", $rawHeaders);
        foreach ($responseHeaderLines as $headerLine) {
            if ($headerLine && strpos($headerLine, ':') !== false) {
                list($header, $value) = explode(': ', $headerLine, 2);
                $header = strtolower($header);
                if (isset($headers[$header])) {
                    $headers[$header] .= "\n" . $value;
                } else {
                    $headers[$header] = $value;
                }
            }
        }
        return $headers;
    }

    private function parseArrayHeaders($rawHeaders)
    {
        $header_count = count($rawHeaders);
        $headers = array();

        for ($i = 0; $i < $header_count; $i++) {
            $header = $rawHeaders[$i];
            // Times will have colons in - so we just want the first match.
            $header_parts = explode(': ', $header, 2);
            if (count($header_parts) == 2) {
                $headers[strtolower($header_parts[0])] = $header_parts[1];
            }
        }

        return $headers;
    }
}