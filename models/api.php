<?php

/*
 * This file is a part of the L9-fr API package.
 *
 * (c) François LASSERRE <choiz@me.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Models;

use Engine\Db as DB;
use Engine\Request as Request;

class Api
{
    public function __construct(Request $request = NULL)
    {
        $this->db = DB::getInstance();

        if (isset($request)) {
            $this->req = $request;
            $this->api_url_base = $request->base.'/'.$request->version;
            $this->api_url = $request->base.$request->path_info;
            $this->query_string = $request->query_string;
        }

        return true;
    }

    public function getFields()
    {
        return array();
    }

    public function filterResults($results)
    {
        $filtered_fields = $this->getFields();
        $array_return = array();

        foreach ($results as $r) {
            $row = array();

            foreach ($filtered_fields as $k => $v) {
                $row[$k] = $r[$v];
            }
            $array_return[] = $row;
        }
        return $array_return;
    }

    protected function getPagination($links)
    {
        $meta['nb'] = count($links);
        $meta['cur_page'] = $this->api_url.'?'.http_build_query($this->query_string);
        $next_params = $prev_params = $this->query_string;

        if ($meta['nb'] > 1) {
            $next_params['offset'] = $next_params['offset'] + $next_params['limit'];
            $meta['next_page'] = $this->api_url.'?'.http_build_query($next_params);

            if ($prev_params['offset'] >= $prev_params['limit']) {
                $prev_params['offset'] = $prev_params['offset'] - $prev_params['limit'];
                $meta['prev_page'] = $this->api_url.'?'.http_build_query($prev_params);
            }
        }

        return $meta;
    }

    protected function setLimit($limit, $offset)
    {
        if ($limit == 0) {
            $limit = '';
        } else {
            $limit = ' LIMIT '.$offset.','.$limit;
        }
        return $limit;
    }
}
