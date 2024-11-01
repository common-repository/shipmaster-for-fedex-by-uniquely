<?php

/**
 * @package  shipmaster_for_fedex\ShipmasterForFedex\Inc
 */

namespace ShipmasterForFedex\Inc;

$product_id = " test";

final class Init
{
    /**
     * Store all the classes inside an array
     * @return array Full list of classes
     */

    public static $plugin_name = "Shipmaster for Fedex by Uniquely";
    public static $product_id = "shipmaster_for_fedex";
    public static $plugin_file = __FILE__;
    public static $plugin_short = "Shipmaster FedEx";
    public static $plugin_basename = "shipmasterfedex/shipmasterfedex.php";

    function __construct($file)
    {
        $this->plugin = $file;
        $this->register_services();
    }

    function get_services()
    {
        return [
            Rates\PackageProcessor::class,
            Rates\AdminPage::class,
            Rates\Quote::class,
        ];
    }

    /**
     * Loop through the classes, initialize them, 
     * and call the register() method if it exists
     * @return
     */
    public function register_services()
    {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register($this);
            }
        }
    }

    /**
     * Initialize the class
     * @param  class $class    class from the services array
     * @return class instance  new instance of the class
     */
    private static function instantiate($class)
    {
        $service = new $class();

        return $service;
    }
}
