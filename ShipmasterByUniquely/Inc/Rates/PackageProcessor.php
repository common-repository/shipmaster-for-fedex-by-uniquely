<?php

/**
 * @package  shipmaster_for_fedex\ShipmasterForFedex\Inc\Rates
 */

namespace ShipmasterForFedex\Inc\Rates;

use \WC_Product_Variation;

//  PackageProcessor processed packages passed by woocommerce into FedEx API payload.
class PackageProcessor
{
    static function prepare_weight($weight, $weight_unit)
    {
        if ($weight_unit == "g") return $weight / 1000;
        if ($weight_unit == "oz") return $weight / 16;
        return $weight;
    }
    static function processPackage($package, $object)
    {
        $weight_unit = get_option('woocommerce_weight_unit');

        if ($weight_unit == "kg") $request_weight_unit = "KG";
        if ($weight_unit == "g") $request_weight_unit = "KG";
        if ($weight_unit == "lbs") $request_weight_unit = "LB";
        if ($weight_unit == "oz") $request_weight_unit = "LB";

        $json_paylod = '{"accountNumber":{"value":""},"rateRequestControlParameters":{"returnTransitTimes":true,"rateSortOrder":"SERVICENAMETRADITIONAL"},"requestedShipment":{"shipper":{"address":{"streetLines":["64 Connaught Road Central","21F"],"city":"Central","postalCode":"852","countryCode":"HK","residential":false}},"rateRequestType":["ACCOUNT"],"recipient":{"address":{"postalCode":"SW7 4XH","countryCode":"GB","residential":true}},"customsClearanceDetail":{"dutiesPayment":{"paymentType":"SENDER","payor":{"responsibleParty":null}},"commodities":[{"description":"Watches","quantity":1,"quantityUnits":"PCS","weight":{"units":"KG","value":0.6},"customsValue":{"amount":499,"currency":"USD"}}]},"pickupType":"DROPOFF_AT_FEDEX_LOCATION","requestedPackageLineItems":[{"weight":{"units":"KG","value":1}}],"edtRequestType":"ALL","packagingType":"FEDEX_PAK"}}';
        $payload = json_decode($json_paylod, true);
        $residential = $object->settings["residential"] == "yes" ? True : False;
        $total_weight = 0;
        $i = 0;
        foreach ($package['contents'] as $index => $item) {
            $product = wc_get_product($item['product_id']);
            if ($item['variation_id'] != 0) {
                $product = new WC_Product_Variation($item["variation_id"]);
            }
            $weight = $product->get_weight() * $item['quantity'];
            $total_weight += $weight;
            $payload['requestedShipment']['customsClearanceDetail']['commodities'][$i] = [];
            $payload['requestedShipment']['customsClearanceDetail']['commodities'][$i]['description'] = $product->get_name();
            $payload['requestedShipment']['customsClearanceDetail']['commodities'][$i]['quantity'] = $item['quantity'];
            $payload['requestedShipment']['customsClearanceDetail']['commodities'][$i]['quantiyUnits'] = 'PCS';
            $payload['requestedShipment']['customsClearanceDetail']['commodities'][$i]['weight'] = [];
            $payload['requestedShipment']['customsClearanceDetail']['commodities'][$i]['weight']['units'] = $request_weight_unit;
            $payload['requestedShipment']['customsClearanceDetail']['commodities'][$i]['weight']['value'] = PackageProcessor::prepare_weight($weight, $weight_unit);
            $payload['requestedShipment']['customsClearanceDetail']['commodities'][$i]['customsValue'] = [];
            $payload['requestedShipment']['customsClearanceDetail']['commodities'][$i]['customsValue']['currency'] =  \ShipmasterForFedex\Inc\Currencies::fedexCurrency(get_woocommerce_currency());
            $payload['requestedShipment']['customsClearanceDetail']['commodities'][$i]['customsValue']['amount'] = floatval($product->get_sale_price()) * $item['quantity'];
            $i++;
        }

        //  applying values to payload

        $store_address_2   = get_option('woocommerce_store_address_2');
        $store_postcode    = get_option('woocommerce_store_postcode');
        $store_raw_country = get_option('woocommerce_default_country');
        $split_country = explode(":", $store_raw_country);
        $store_country = $split_country[0];
        $store_state   = $split_country[1];
        $payload['accountNumber']['value'] = $object->account_no;
        $payload['requestedShipment']['shipper']['address']['streetLines'][0] = get_option('woocommerce_store_address');
        $payload['requestedShipment']['shipper']['address']['streetLines'][1] = $store_address_2 ? $store_address_2 : null;
        $payload['requestedShipment']['shipper']['address']['city'] = get_option('woocommerce_store_city');
        $payload['requestedShipment']['shipper']['address']['postalCode'] = $store_postcode ? $store_postcode : "NA";
        $payload['requestedShipment']['shipper']['address']['countryCode'] =  $store_country;
        $payload['requestedShipment']['recipient']['address']['countryCode'] = $package['destination']['country'];
        $payload['requestedShipment']['recipient']['address']['postalCode'] = $package['destination']['postcode'];
        $payload['requestedShipment']['recipient']['address']['residential'] = $residential;
        $payload['requestedShipment']['rateRequestType'] = [$object->settings['rate_type']];
        $payload['requestedShipment']['requestedPackageLineItems'][0]['weight']['value'] = $total_weight;
        $payload['requestedShipment']['pickupType'] = $object->settings['pickup_type'];
        return $payload;
    }
}
