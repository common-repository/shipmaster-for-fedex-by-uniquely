<?php

/**
 * @package  shipmaster_for_fedex\ShipmasterForFedex\Inc
 */

namespace ShipmasterForFedex\Inc;

class OAuth
{
    // Obtaining access token from FedEx API.  
    static function get_access_token($objects)
    {
        if (!$objects->api_key or $objects->api_key == "") {
            \ShipmasterForFedex\Inc\Rates\ErrorHandler::error_admin("Failed.");
            return null;
        }
        $access_token = get_transient(Init::$product_id . "_access_token");
        if ($access_token) {

            return $access_token;
        }
        $url = "https://apis.fedex.com/oauth/token";

        $server_output = wp_remote_post($url, array(
            'method' => 'POST',
            'blocking' => true,
            'headers' => array('Content-Type: application/x-www-form-urlencoded'),
            'body' => array(
                'client_id' => $objects->api_key,
                'client_secret' => $objects->api_secret,
                'grant_type' => 'client_credentials',
            )
        ));

        $response = json_decode($server_output['body'], true);

        if (array_key_exists("access_token", $response)) {
            set_transient(Init::$product_id . "_access_token", $response['access_token'], 3599);
            return $response['access_token'];
        } else {
            \ShipmasterForFedex\Inc\Rates\ErrorHandler::error_admin("Error: " .  $response['errors'][0]['code']);
            return null;
        }
    }

    // Obtaining live rates from FedEx API.
    static function get_live_rate($payload, $object)
    {
        $url = "https://apis.fedex.com/rate/v1/rates/quotes";
        $access_token = OAuth::get_access_token($object);
        if (!$access_token)
            return "Get";
        $authorization = "Authorization: Bearer " . $access_token;
        $header = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $access_token,
        );
        $result = wp_remote_post($url, array(
            'body' => json_encode($payload),
            'headers' => $header,
            'method' => 'POST',
            'blocking' => true,
        ));

        return $result['body'];
    }
}
