<?php

/**
 * @package  shipmaster_for_fedex\ShipmasterForFedex\Inc\Rates
 */



namespace ShipmasterForFedex\Inc\Rates;

// For debug mode.
class ErrorHandler
{
    static function error_msg($msg, $notice, $object)
    {
        if ($object->settings['debug_mode'] == "yes") {
            $errormsg = '<span style="color:red;font-weight:bold">' . $msg . '</span>';
            WC()->session->set(\ShipmasterForFedex\Inc\Init::$product_id . "_error_msg", $errormsg);
            wc_add_notice($notice, 'error');
        }
    }
    static function error_admin($msg)
    {
        $errormsg = '<span style="color:red;font-weight:bold">' . $msg . '</span>';
        set_transient(\ShipmasterForFedex\Inc\Init::$product_id . "_admin_msg", $errormsg, 30);
    }
}
