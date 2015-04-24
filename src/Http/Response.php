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

namespace Goracash\Http;

class Response
{
    public $url = null;
    public $method = null;
    public $request_headers = array();

    public $code = 0;
    public $status = 0;
    public $content_type = null;
    public $headers = array();
    public $body = null;

    public function get_headers()
    {
        return $this->headers;
    }

    public function set_request_headers($headers)
    {
        $headers = explode("\n", trim($headers));
        // Skip first line
        for ($i = 1, $len = count($headers); $i < $len; $i++) {
            list($name, $value) = explode(':', $headers[$i], 2);
            $name = self::normalize_header($name);
            $this->request_headers[$name] = trim($value);
        }
    }

    public function set_content_type($content_type)
    {
        $value = explode(';', $content_type);
        $this->content_type = $value[0];
    }

    public function length()
    {
        $length = $this->get_header('Content-Length');
        if (!$length) {
            $length = strlen($this->body);
        }
        return $length;
    }

}

?>
