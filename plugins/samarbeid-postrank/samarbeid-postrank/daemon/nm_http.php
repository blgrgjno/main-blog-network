<?php
/**
* Utilities for HTTP connections
*
* @author Nelson Menezes
*/

/**
 * Performs simultaneous HTTP connections
 * Code "borrowed" from http://www.phpied.com/simultaneuos-http-requests-in-php-with-curl/
 *
 * @see http://www.phpied.com/simultaneuos-http-requests-in-php-with-curl/
 * @see http://php.net/manual/en/function.curl-multi-exec.php
 *
 * @param array $urls An array of URLs or an array of arrays, each with a "url"
 *        element and an optional "post" element with cURL POST options
 * @param array $options cURL options
 * @return Array of result data
 */
function nm_http_multi_request($urls, $options = array()) {

    // array of curl handles
    $handles = array();

    // data to be returned
    $result = array();

    // multi handle
    $mh = curl_multi_init();

    // create curl handles, and add them to the multi-handle
    foreach ($urls as $id => $url) {

        $handles[$id] = curl_init();

        $url = (is_array($url) && !empty($url['url'])) ? $url['url'] : $url;

        curl_setopt($handles[$id], CURLOPT_URL, $url);
        curl_setopt($handles[$id], CURLOPT_HEADER, 0);
        curl_setopt($handles[$id], CURLOPT_RETURNTRANSFER, 1);

        // post?
        if (is_array($url) && (!empty($url['post']))) {

            curl_setopt($handles[$id], CURLOPT_POST, 1);
            curl_setopt($handles[$id], CURLOPT_POSTFIELDS, $url['post']);
        }

        // extra options?
        if (!empty($options)) {

            curl_setopt_array($handles[$id], $options);
        }

        curl_multi_add_handle($mh, $handles[$id]);
    }

    // execute the handles
    $running = null;
    do {

        curl_multi_exec($mh, $running);
        usleep(100000);  // 0.1 seconds

    } while ($running > 0);

    // get content and remove handles
    foreach ($handles as $id => $handle) {

        $result[$id] = curl_multi_getcontent($handle);
        curl_multi_remove_handle($mh, $handle);
    }

    // all done
    curl_multi_close($mh);

    return $result;
}

?>