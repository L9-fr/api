<?php

/*
 * This file is a part of the L9-fr API package.
 *
 * (c) François LASSERRE <choiz@me.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Views;

class Json extends Api
{

    public function render($data)
    {
        header('Content-Type: application/json; charset=utf8');
        echo $this->encodeOutput($data);
        return true;
    }

    public function encodeOutput($data)
    {
        $data = $this->addContent($data);
        return json_encode($data, JSON_NUMERIC_CHECK);
    }

}
