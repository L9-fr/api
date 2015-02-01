<?php

/*
 * This file is a part of the L9-fr API package.
 *
 * (c) François LASSERRE <choiz@me.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once 'config.php';

ini_set('default_charset', ENCODING);
ini_set('php.input_encoding', ENCODING);
ini_set('php.internal_encoding', ENCODING);
ini_set('php.output_encoding', ENCODING);

date_default_timezone_set(TIMEZONE);

function loader($class) {
    $class = str_replace('\\', '/', $class);
    $file = BASE_DIR.'/'.strtolower($class).'.php';

    if (file_exists($file)) {
        require_once $file;
    }
}

spl_autoload_register('loader');

if (!function_exists('apache_request_headers') ) {

    function apache_request_headers() {
        $arh = array();
        $rx_http = '/\AHTTP_/';

        foreach ($_SERVER as $key => $val) {
            if (preg_match($rx_http, $key)) {
                $arh_key = preg_replace($rx_http, '', $key);
                $rx_matches = array();
                $rx_matches = explode('_', strtolower($arh_key));

                if (count($rx_matches) > 0 && strlen($arh_key) > 2) {
                    foreach ($rx_matches as $ak_key => $ak_val) {
                        $rx_matches[$ak_key] = ucfirst($ak_val);
                    }

                    $arh_key = implode('-', $rx_matches);
                }

                $arh[$arh_key] = $val;
            }
        }

        if (isset($_SERVER['CONTENT_TYPE'])) {
            $arh['Content-Type'] = $_SERVER['CONTENT_TYPE'];
        }

        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $arh['Content-Length'] = $_SERVER['CONTENT_LENGTH'];
        }

        return $arh;
    }
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

try {
    $request = new Engine\Request();
} catch(Exception $e) {
    header("Status: ".$e->getCode(), false, $e->getCode());
	echo $e->getMessage();
}
