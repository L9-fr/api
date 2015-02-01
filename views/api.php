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

class Api
{
    protected function addContent($data)
    {
        if (is_array($data)) {
            foreach($data as $name => $item) {
                if ($name == "meta") {
                    continue;
                } else if (is_array($item)) {
                    $data['meta']['nb'] = count($item);
                }
            }
        }

        return $data;
    }
}
