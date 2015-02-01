<?php

/*
 * This file is a part of the L9-fr API package.
 *
 * (c) François LASSERRE <choiz@me.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Controllers;

abstract class Api
{

    abstract public function index();

    abstract public function readme();

    public function __construct($request) {
        $this->req = $request;
        $this->url_base = $request->base;
        $this->api_url = $request->base.'/'.$request->version;
        $this->path = $request->path;
        $this->query_string = $request->query_string;
    }

    public function getId()
    {
		if (!empty($this->path[3]) && is_numeric($this->path[3])) {
            return intval($this->path[3]);
		}

        return false;
    }

    public function getLimit()
    {
        return intval($this->query_string['limit']);
    }

    public function getOffset()
    {
        return intval($this->query_string['offset']);
    }

    public function getSort()
    {
        if (!empty($this->query_string['sort'])) {
            return $this->query_string['sort'];
        }

        return false;
    }
}
