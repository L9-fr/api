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

class JsonP extends Json
{
    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function render($data)
    {
        header('Content-Type: text/javascript; charset=utf8');
        echo $this->callback.'('.$this->encodeOutput($data).');';
        return true;
    }
}
