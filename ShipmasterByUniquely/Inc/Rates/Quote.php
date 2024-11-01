<?php

/**
 * @package  shipmaster_for_fedex\ShipmasterForFedex\Inc\Rates
 */

namespace ShipmasterForFedex\Inc\Rates;

include_once(plugin_dir_path(__FILE__) . '../../../../woocommerce/woocommerce.php');

use \ShipmasterForFedex\Inc\OAuth;

class Quote
{
    function register($object)
    {
        add_action("woocommerce_cart_totals_after_order_total", "ShipmasterForFedex\\Inc\\Rates\\show_error");
        add_filter('woocommerce_shipping_methods', 'ShipmasterForFedex\\Inc\\Rates\\add_your_shipping_method');
    }
}

/*  Shipping Method class */

class FedExPluginForWoocommerceShippingMethod extends \WC_Shipping_Method
{
    public function __construct()
    {
        $this->id                 = \ShipmasterForFedex\Inc\Init::$product_id; // Id for your shipping method. Should be uunique.
        $this->method_title       = __(\ShipmasterForFedex\Inc\Init::$plugin_name);  // Title shown in admin
        $this->method_description = __('Dynamic FedEx shipping rate'); // Description shown in admin
        $this->product_id       = \ShipmasterForFedex\Inc\Init::$product_id;
        $this->init();
    }

    function init()
    {

        $this->init_form_fields();  // This is part of the settings API. Override the method to add your own settings
        $this->init_settings();     // This is part of the settings API. Loads settings you previously init.
        add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        $this->api_key = $this->settings['api_key'];
        $this->api_secret = $this->settings['api_secret'];
        $this->account_no = $this->settings['account_no'];
        $this->include_or_exclude = $this->settings['include_or_exclude'];
        $this->countries_list = gettype($this->settings['countries']) == "array" ? $this->settings['countries'] : [];
    }

    function admin_options()
    {
        \ShipmasterForFedex\Inc\Rates\AdminPage::admin_options($this);
    }

    function init_form_fields()
    {
        $countries = array(
            "AL" => "Albania", "DZ" => "Algeria", "AS" => "American Samoa", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AG" => "Antigua", "AR" => "Argentina", "AM" => "Armenia", "AW" => "Aruba", "AU" =>
            "Australia", "AT" => "Austria", "AZ" => "Azerbaijan", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BY" => "Belarus", "BE" => "Belgium", "BZ" => "Belize", "BJ" => "Benin",
            "BM" => "Bermuda", "BT" => "Bhutan", "BO" => "Bolivia", "BW" => "Botswana", "BR" => "Brazil", "VG" => "British Virgin Is.", "BN" => "Brunei", "BG" => "Bulgaria", "BF" => "Burkino Faso", "MM" => "Burma", "BI"
            => "Burundi", "KH" => "Cambodia", "CM" => "Cameroon", "CA" => "Canada", "CV" => "Cape Verde", "KY" => "Cayman Islands", "CF" => "Central African", "TD" => "Chad", "CL" => "Chile", "CN" => "China", "CO" =>
            "Colombia", "CG" => "Congo", "CD" => "Congo, The Republic of", "CK" => "Cook Islands", "CR" => "Costa Rica", "CI" => "Cote D'Ivoire", "HR" => "Croatia", "CY" => "Cyprus", "CZ" => "Czech Republic", "DK" =>
            "Denmark", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Dominican Republic", "EC" => "Ecuador", "EG" => "Egypt", "SV" => "El Salvador", "GQ" => "Equatorial Guinea", "ER" => "Eritrea", "EE" => "Estonia",
            "ET" => "Ethiopia", "FO" => "Faeroe Islands", "FJ" => "Fiji", "FI" => "Finland", "FR" => "France", "GF" => "French Guiana", "PF" => "French Polynesia", "GA" => "Gabon", "GM" => "Gambia", "GE" => "Georgia, 
        Republic of", "DE" => "Germany", "GH" => "Ghana", "GI" => "Gibraltar", "GR" => "Greece", "GL" => "Greenland", "GD" => "Grenada", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GN" => "Guinea", "GW"
            => "Guinea-Bissau", "GY" => "Guyana", "HT" => "Haiti", "HN" => "Honduras", "HK" => "Hong Kong", "HU" => "Hungary", "IS" => "Iceland", "IN" => "India", "ID" => "Indonesia", "IE" => "Ireland", "IL" => "Israel",
            "IT" => "Italy", "CI" => "Ivory Coast", "JM" => "Jamaica", "JP" => "Japan", "JO" => "Jordan", "KZ" => "Kazakhstan", "KE" => "Kenya", "KW" => "Kuwait", "KG" => "Kyrgyzstan", "LV" => "Latvia", "LB" => "Lebanon",
            "LS" => "Lesotho", "LI" => "Liechtenstein", "LT" => "Lithuania", "LU" => "Luxembourg", "MO" => "Macau", "MK" => "Macedonia", "MG" => "Madagascar", "MW" => "Malawi", "MY" => "Malaysia", "MV" => "Maldives", "ML"
            => "Mali", "MT" => "Malta", "MH" => "Marshall Islands", "MQ" => "Martinique", "MR" => "Mauritania", "MU" => "Mauritius", "MX" => "Mexico", "FM" => "Micronesia", "MD" => "Moldova", "MC" => "Monaco", "MN" =>
            "Mongolia", "MS" => "Montserrat", "MA" => "Morocco", "MZ" => "Mozambique", "MM" => "Myanmar", "NA" => "Namibia", "NP" => "Nepal", "NL" => "Netherlands", "AN" => "Netherlands Antilles", "NC" => "New Caledonia",
            "NZ" => "New Zealand", "NI" => "Nicaragua", "NE" => "Niger", "NG" => "Nigeria", "NO" => "Norway", "OM" => "Oman", "PK" => "Pakistan", "PW" => "Palau", "PA" => "Panama", "PG" => "Papua New Guinea", "PY" =>
            "Paraguay", "PE" => "Peru", "PH" => "Philippines", "PL" => "Poland", "PT" => "Portugal", "US" => "Puerto Rico", "QA" => "Qatar", "RE" => "Reunion Island", "RO" => "Romania", "RU" => "Russia", "RW" => "Rwanda",
            "MP" => "Saipan", "SM" => "San Marino", "SA" => "Saudi Arabia", "SN" => "Senegal", "SC" => "Seychelles", "SL" => "Sierra Leone", "SG" => "Singapore", "SK" => "Slovak Republic", "SI" => "Slovenia", "ZA" =>
            "South Africa", "KR" => "South Korea", "ES" => "Spain", "LK" => "Sri Lanka", "KN" => "St. Kitts & Nevis", "LC" => "St. Lucia", "VC" => "St. Vincent", "SR" => "Suriname", "SZ" => "Swaziland", "SE" => "Sweden",
            "CH" => "Switzerland", "SY" => "Syria", "TW" => "Taiwan", "TZ" => "Tanzania", "TH" => "Thailand", "TG" => "Togo", "TT" => "Trinidad & Tobago", "TN" => "Tunisia", "TR" => "Turkey", "TM" => "Turkmenistan, 
        Republic of", "TC" => "Turks & Caicos Is.", "AE" => "U.A.E.", "VI" => "U.S. Virgin Islands", "US" => "U.S.A.", "UG" => "Uganda", "UA" => "Ukraine", "GB" => "United Kingdom", "UY" => "Uruguay", "UZ" =>
            "Uzbekistan", "VU" => "Vanuatu", "VA" => "Vatican City", "VE" => "Venezuela", "VN" => "Vietnam", "WF" => "Wallis & Futuna Islands", "YE" => "Yemen", "ZM" => "Zambia", "ZW" => "Zimbabwe",
        );
        $conversion_help = "A multiplier applied to the FedEx rates.<br>Useful for when your store's currency is different than FedEx account's currency.<br>";
        $conversion_help = $conversion_help . "<br>For example, if your store's currency is USD, but FedEx account is in JPY, <br>you can enter a multiplier that will convert JPY to USD.";
        $this->form_fields = array(
            'enabled' => array('title' => __('Enable', $this->id), 'type' => 'checkbox', 'default' => 'yes', 'description' => 'Uncheck to disable plugin.'),
            'debug_mode' => array('title' => __('Debug Mode', $this->id), 'type' => 'checkbox', 'default' => 'no', 'description' => 'Enable to show error message.'),
            'api_key' => array('title' => __('API Key',  $this->id), 'type' => 'text'),
            'api_secret' => array('title' => __('API Secret', $this->id), 'type' => 'text',),
            'account_no' => array('title' => __('FedEx Account Number', $this->id), 'type' => 'text'),
            'rate_type' => array('title' => __('Rate Type', $this->id), 'type' => 'text',),
            'residential' => array('title' => __('Residential?', $this->id), 'type' => 'checkbox', 'default' => 'no'),
            'pickup_type' => array('title' => __('Pick Up Type',  $this->id), 'type' => 'text',),
            'conversion_rate' => array('title' => __('Conversion Rate',  $this->id), 'type' => 'text', 'default' => '1'),
            'include_or_exclude' => array('title' => __('Include/Exclude Countries', $this->id), 'type' => 'select', 'options' => ["INCLUDE" => "Include", "EXCLUDE" => "Exclude"], 'default' => "EXCLUDE"),
            'countries' => array('title' => __('Request Rate Type', $this->id), 'type' => 'multiselect', 'options' => [$countries]),
            "fixed_markupdown" => array('title' => __('Fixed Mark Up/Down',  $this->id), 'type' => 'text', 'default' => '0'),
            "percentage_markupdown" => array('title' => __('Percentage Mark Up/Down',  $this->id), 'type' => 'text', 'default' => '0'),
        );
        $this->credentials_fields = array(
            'api_key' => array('title' => __('API Key',  $this->id), 'type' => 'password',),
            'api_secret' => array('title' => __('API Secret', $this->id), 'type' => 'password',),
            'account_no' => array('title' => __('FedEx Account Number', $this->id), 'type' => 'text'),

        );
        $this->plugin_fields = array(
            'enabled' => array('title' => __('Enable', $this->id), 'type' => 'checkbox', 'default' => 'yes', 'description' => 'Uncheck to disable plugin.'),
            'debug_mode' => array('title' => __('Debug Mode', $this->id), 'type' => 'checkbox', 'default' => 'no', 'description' => 'Enable to show error message.'),
            'conversion_rate' => array('title' => __('Conversion Rate',  $this->id), 'type' => 'text', 'default' => '1', 'description' => $conversion_help),
            "fixed_markupdown" => array('title' => __('Fixed Mark Up/Down',  $this->id), 'type' => 'text', 'default' => '00', 'description' => 'Use - sign for marking down.'),
            "percentage_markupdown" => array('title' => __('Percentage Mark Up/Down',  $this->id), 'type' => 'text', 'default' => '00', 'description' => 'Use - sign for marking down.'),
        );
        $this->account_settings_fields = array(
            'residential' => array('title' => __('Residential?', $this->id), 'type' => 'checkbox', 'default' => 'no'),
            'rate_type' => array('title' => __('Request Rate Type', $this->id), 'type' => 'select', 'options' => ["LIST" => "List Rate", "ACCOUNT" => "Account Rate"]),
            'pickup_type' => array('title' => __('Pick Up Type',  $this->id), 'type' => 'select', 'options' => ["CONTACT_FEDEX_TO_SCHEDULE" => "Contact FedEx to schedule", "DROPOFF_AT_FEDEX_LOCATION" => "Dropoff at FedEx location", "USE_SCHEDULED_PICKUP" => "Use scheduled pickup"]),
            'residential' => array('title' => __('Residential?', $this->id), 'type' => 'checkbox', 'default' => 'no'),
            'include_or_exclude' => array('title' => __('Include/Exclude Countries', $this->id), 'type' => 'select', 'options' => ["INCLUDE" => "Include", "EXCLUDE" => "Exclude"]),
            'countries' => array('title' => __('  ', $this->id), 'type' => 'multiselect', 'options' => $countries, 'css' => 'height:500px'),
        );
    }


    function prepare_defaults($fields)
    {
        return array_map(array($this, 'set_defaults'), $fields);
    }

    /**
     * calculate_shipping function.
     *
     * @access public
     * @param mixed $package
     * @return void
     */
    public function calculate_shipping($package = [])
    {
        clear_error_msg();
        if (!$this->api_key) {
            return;
        }
        if (!\ShipmasterForFedex\Inc\OAuth::get_access_token($this)) {
            return;
        }

        if (in_array($package['destination']['country'], $this->countries_list) and $this->include_or_exclude == "EXCLUDE")
            return;
        if (!in_array($package['destination']['country'], $this->countries_list) and $this->include_or_exclude == "INCLUDE")
            return;

        $payload = PackageProcessor::processPackage($package, $this);
        $response = OAuth::get_live_rate($payload, $this);
        $result = json_decode($response, true);

        if (gettype($result) != "array") {
            ErrorHandler::error_msg("API Error, json_decode failed", 'Problem while fetching Shipping (FedEx) rate, please try again later.', $this);
            return;
        }

        if (array_key_exists('output', $result) and array_key_exists('rateReplyDetails', $result['output'])) {
            clear_error_msg();
            foreach ($result['output']['rateReplyDetails'] as $item) {
                $fedex_currency = \ShipmasterForFedex\Inc\Currencies::wooCurrency($item['ratedShipmentDetails'][0]['currency']);
                $woo_currency = get_woocommerce_currency();
                if ($fedex_currency != $woo_currency) {
                    $totalcharge = $item['ratedShipmentDetails'][0]['totalNetCharge'] * floatval($this->settings['conversion_rate']);
                } else {
                    $totalcharge = $item['ratedShipmentDetails'][0]['totalNetCharge'];
                }
                $totalcharge = $totalcharge + floatval($this->settings['fixed_markupdown']);
                $percentmarkup = (100 + floatval($this->settings['percentage_markupdown'])) / 100;
                $totalcharge = $totalcharge * $percentmarkup;
                $rate = array(
                    'id' => $this->get_rate_id($item['serviceType']),
                    'label' => $item['serviceName'],
                    'cost' => $totalcharge,
                    'calc_tax' => 'per_item'
                );
                $this->add_rate($rate);
            }
        } else {
            if (array_key_exists('errors', $result)) {
                ErrorHandler::error_msg('API Error : <br>' . $result['errors'][0]['code'] . $result['errors'][0]['message'], 'Problem while fetching Shipping (FedEx) rate, please try again later.', $this);
            }
        }
    }
}

function add_your_shipping_method($methods)
{
    $methods[\ShipmasterForFedex\Inc\Init::$product_id . "-shipping_method"] = FedExPluginForWoocommerceShippingMethod::class;

    return $methods;
}

function clear_error_msg()
{
    WC()->session->set(\ShipmasterForFedex\Inc\Init::$product_id . "_error_msg", null);
}

function show_error()
{
    $error_msg = WC()->session->get(\ShipmasterForFedex\Inc\Init::$product_id . "_error_msg");
    if ($error_msg) {
        echo esc_html($error_msg);
    }
}
