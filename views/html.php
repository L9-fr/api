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

class Html extends Api
{
    public function render($data)
    {
        $data = $this->addContent($data);

        header('Content-Type: text/html; charset=utf8');
        echo '<!DOCTYPE html>
<html>
<head>
    <title>'.API_NAME.'</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
';
        $this->displayContent($data);
        echo '</body>
</html>';
        return true;
    }

    protected function displayContent($data)
    {
        if (is_array($data)) {
            echo '<ul>'."\n";

            foreach ($data as $field => $value) {
                echo '<li>'.$field;

                if (is_array($value)) {
                    $this->displayContent($value);
                } else {
                    $value = htmlentities($value, ENT_COMPAT, 'UTF-8');

                    if ((strpos($value, 'http://') === 0) || (strpos($value, 'https://') === 0)) {
                        echo '<a href="'.$value.'">'.$value.'</a>';
                    } else {
                        echo $value;
                    }
                }
                echo '</li>'."\n";;
            }

            echo '</ul>'."\n";
        } else if (is_object($data)) {
            echo '<h1>'.$data->bundle_name.'</h1>'."\n";
            echo 'Description: '.$data->bundle_description.'<br>'."\n";
            echo 'Author: '.htmlentities($data->bundle_author).'<br>'."\n";
            echo 'Version: '.$data->bundle_version.'<br>'."\n";
            echo '<br>'."\n";

            if (!empty($data->links)) {
                echo 'Examples: <ul>'."\n";

                foreach($data->links as $k => $v) {
                    echo '<li><a href="'.$v.'">'.$v.'</a></li>'."\n";
                }

                echo '</ul>'."\n";
            }

        } else {
            echo 'no data';
        }
    }
}
