<?php
/**
 * Created by PhpStorm.
 * User: micha
 * Date: 06-09-2016
 * Time: 22:55
 */

namespace Nazara\Lib;


class cURL {
    /**
     * @param string $url
     * @param array $headers
     * @return mixed|null
     */
    public function getData(string $url, array $headers = array()) {
        // Merge the headers from the request with the default headers
        $headers = array_merge(array("Connection: keep-alive", "Keep-Alive: timeout=60, max=1000"), $headers);

        // Init curl
        $curl = curl_init();

        // Setup curl
        curl_setopt_array($curl, array(
            CURLOPT_USERAGENT => "DataGetter for Nazara (Discord Bot) (email: karbowiak@gmail.com / slack (tweetfleet): karbowiak / irc (coldfront): karbowiak)",
            CURLOPT_TIMEOUT => 180,
            CURLOPT_POST => false,
            CURLOPT_FORBID_REUSE => false,
            CURLOPT_ENCODING => "",
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => true,
        ));

        // Get the data
        return curl_exec($curl);
    }

    /**
     * @param string $url
     * @param array $postData
     * @param array $headers
     * @return mixed
     */
    public function sendData(string $url, $postData = array(), $headers = array()) {
        // Define default headers
        if (empty($headers)) {
            $headers = array("Connection: keep-alive", "Keep-Alive: timeout=10, max=1000");
        }

        // Init curl
        $curl = curl_init();

        // Init postLine
        $postLine = "";

        // Populate the $postData
        if (!empty($postData)) {
            foreach ($postData as $key => $value) {
                $postLine .= $key . "=" . $value . "&";
            }
        }

        // Trim the last &
        rtrim($postLine, "&");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT,
            "DataPoster for Nazara (Discord Bot) (email: karbowiak@gmail.com / slack (tweetfleet): karbowiak / irc (coldfront): karbowiak)");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if (!empty($postData)) {
            curl_setopt($curl, CURLOPT_POST, count($postData));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postLine);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }
}