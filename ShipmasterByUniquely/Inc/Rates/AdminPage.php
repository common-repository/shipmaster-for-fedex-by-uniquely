<?php

/**
 * @package  shipmaster_for_fedex\ShipmasterForFedex\Inc\Rates
 */

namespace ShipmasterForFedex\Inc\Rates;

include_once(plugin_dir_path(__FILE__) . '../../../../woocommerce/woocommerce.php');

// AdminPage class for handling admin options.

class AdminPage
{
    public function __construct()
    {
    }

    function register()
    {
        //        add_action("admin_menu", [$this, 'add_admin_pages']);
        add_filter("plugin_action_links_" . \ShipmasterForFedex\Inc\Init::$plugin_basename, [$this, 'settings_link']);
    }

    public function settings_link($links)
    {
        array_push($links, '<a href="admin.php?page=wc-settings&tab=shipping&section=' . \ShipmasterForFedex\Inc\Init::$product_id . '">Settings</a>');
        return $links;
    }
    static function admin_options($object)
    {
        echo "<div class='uniquelymain' style='overflow:hidden;display:flex;'><div class='uniquelysettings' style='min-width:700px;max-width:800px'>";
        echo "<br><h3>" . esc_html(\ShipmasterForFedex\Inc\Init::$plugin_name) . "</h3>";
        echo "<p>Thanks for choosing " . esc_html(\ShipmasterForFedex\Inc\Init::$plugin_name) . ".  We just need a few things to get started.";
        // generate_settings_html handled by WC_Settings_API and is escaped within the function.
        echo "<table class='form-table'>" . $object->generate_settings_html($object->prepare_defaults($object->plugin_fields), false) . '</table>';
        echo '<h3>API Credentials</h3>';
        echo "<p>Please check <a href='https://uniquelyplugins.com/fedex_api_instructions' target='_blank'>here</a> for instructions on obtaining FedEX REST API credentials.</p>";
        // generate_settings_html handled by WC_Settings_API and is escaped within the function.
        echo "<table class='form-table'>" . $object->generate_settings_html($object->prepare_defaults($object->credentials_fields), false);
        //set_transient(\ShipmasterForFedex\Inc\Init::$product_id . "_access_token", null);
        $access_token = \ShipmasterForFedex\Inc\OAuth::get_access_token($object);
        if ($access_token == null) {
            $access_token = get_transient(\ShipmasterForFedex\Inc\Init::$product_id . "_admin_msg");
        } else {
            $access_token = "<span style='color:green'>Succeeded</span>";
        }
        echo "<tr><th>API Credentials Check</th><td>$access_token</td></tr></table>";
        echo '<h3>Shipment Settings</h3>';
        echo '<p>Please note, this plugin uses your product\'s weight for shipment calculation.  Please make sure all product has weight defined.</p>';
        echo "<table class='form-table'>" . $object->generate_settings_html($object->prepare_defaults($object->account_settings_fields), false) . '</table>';
        echo "</div><div class='test' style='float:right;width:300px;color:white;margin-top:3em;'><div style='background-color:#472F91;padding-bottom:3em;'><h1 style='color:white;margin:1em;padding-top:0.5em;'>Pro version is coming soon! ";
        echo "</h1>";
        echo "<li style='margin-left:1em'>Real Time Multi-Currency support!</li>";
        echo "<li style='margin-left:1em'>Automatic Label Creation upon order completion!</li>";
        echo "<li style='margin-left:1em'>Dimensional Weight!</li>";
        echo "<li style='margin-left:1em'>Automatic Box Packing!</li>";
        echo "<li style='margin-left:1em'>Separate package and combine quotes!</li>";
        echo "<li style='margin-left:1em'>And more....</li><p style='margin-left:1em'>Fair pricing promised!</p>";
        echo "<button style='margin-top:1em;background-color:#F57921;margin-left:1em;border:none;color:white;text-align:center;border-radius:3px;padding:15px 32px;'><a href='https://uniquelyplugins.com' style='color:white' target='_blank'>CHECK NOW</a></button>";
        echo "</div></div></div>";
        echo "<p style='margin-top:3em;'>Thank you for using Shipmaster for Fedex by Uniquely.    If you find it useful, please<br> rate ";
        echo "<a href='https://wordpress.org/support/plugin/shipmaster-for-fedex-by-uniquely/reviews/#new-post'>Shipmaster for Fedex by Uniquely</a> &#11088;&#11088;&#11088;&#11088;&#11088;";
        echo " on WordPress.org to help<br> spread the word to the community.</p>";
    }
}
