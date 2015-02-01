<?php

/*
 * This file is a part of the L9-fr API package.
 *
 * (c) François LASSERRE <choiz@me.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Engine;

use Views\Html as Html;
use Views\Json as Json;
use Views\JsonP as JsonP;

class Request
{
    public $http_accept = array();
    public $http_host;
    public $http_method;
    public $query_string = array();
    public $path_info;
    public $path = array();
    public $view;

    public function __construct()
    {
        $this->http_method = $_SERVER['REQUEST_METHOD'];

        if (isset($_SERVER['PATH_INFO'])) {
            $this->path_info = $_SERVER['PATH_INFO'];
            $this->path = explode('/', $this->path_info);
        }

        if (isset($_SERVER['HTTP_ACCEPT'])) {
            $this->http_accept = explode(',', $_SERVER['HTTP_ACCEPT']);
        }

        if (isset($_SERVER['HTTP_HOST'])) {
            $this->http_host = $_SERVER['HTTP_HOST'];
        }

        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on")) {
            $this->scheme = "https://";
        } else {
            $this->scheme = "http://";
        }

        $this->base = $this->scheme.$this->http_host.'/API';
        $this->parseParams();

        $headers = apache_request_headers();

        $header_format = $this->headerFormat();
        $this->format = $this->getQueryString('format', $header_format);

        if ($this->format === 'application/json' || $this->format === 'json' || $this->format === 'jsonp') {
            $callback = filter_var($this->getQueryString('callback'), FILTER_SANITIZE_STRING);
            if ($callback) {
                $this->view = new JsonP($callback);
            } else {
                $this->view = new Json();
            }
        } else {
            $this->view = new Html();
        }

        $this->version = $this->getPath(1);

        if ($this->version === '1.0') {
            $this->datas = $this->router();
        } else if ($this->version === '0.1') {
            throw new \Exception('This API version is no longer supported. Please use the version 1.0');
        } else {
            throw new \Exception('API version isn\'t correct.', 404);
        }

        if (isset($this->user_id)) {
            $this->datas['meta']['your_user_id'] = $this->user_id;
        }

        $this->view->render($this->datas);
    }

    public function accepts($header)
    {
        $result = false;

        foreach ($this->http_accept as $accept) {
            if (strstr($accept, $header) !== false) {
                return true;
            }
        }
    }

    private function router()
    {
        if (isset($this->path[2])) {
            $class = 'Controllers\Home';

            if (!empty($this->path[2])) {
                $class = 'Controllers\\'.ucfirst($this->path[2]);
            }

            if (class_exists($class)) {
                $classname = new $class($this);
                $data = $classname->index();
            } else {
                header("Status: 400", false, 400);
                $this->view->render(array('Unknown controller "'.$this->path[2].'"'));
                exit;
            }
        } else {
            header("Status: 404", false, 404);
            $this->view->render(array('Request not understood"'.$this->path[2].'"'));
            exit;
        }

        return $data;
    }

    public function headerFormat()
    {
        $formats = array('application/json', 'text/html');

        foreach($formats as $format) {
            if ($this->accepts($format)) {
                return $format;
            }
        }

        return 'json';
    }

    public function getQueryString($param, $default = '')
    {
        if (isset($this->query_string[$param])) {
            $default = $this->query_string[$param];
        }

        return $default;
    }

    public function getPath($index, $default = '')
    {
        $index = intval($index);

        if (isset($this->path[$index])) {
            $default = $this->path[$index];
        }

        return $default;
    }

    protected function parseParams() {
        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $query_string);
            $this->query_string = $query_string;
        }

        if ($this->http_method == 'POST' || $this->http_method == 'PUT') {
            $body = file_get_contents("php://input");

            if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] == "application/json") {
                $body_params = json_decode($body);

                if ($body_params) {
                    foreach ($body_params as $param_name => $param_value) {
                        $this->query_string[$param_name] = $param_value;
                    }
                }
            }
        }

        $this->query_string['limit'] = $this->getQueryString('limit', NB_ELEMENT_PER_PAGE);
        $this->query_string['offset'] = $this->getQueryString('offset', 0);

        return true;
    }
}
