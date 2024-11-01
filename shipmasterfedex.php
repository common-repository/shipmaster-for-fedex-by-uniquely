<?php

/**
 *
 * @link              http://uniquelyplugins.com/
 * @since             0.1.0
 * @package           shipmaster_for_fedex
 *
 * @wordpress-plugin
 * Plugin Name:       Shipmaster for FedEx by Uniquely
 * Description:       Display live FedEx rate based on products' weight, fetched through FedEx Rest API. 
 * Version:           0.1.0
 * Author:            Uniquely Plugins
 * Author URI:        http://uniquelyplugins.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 5.0
 * Tested up to: 6.0
 * WC requires at least: 3.9.3
 * WC tested up to: 6.5
 * Requires PHP: 7.1
 *
 * Copyright 2022 Uniquely Plugins
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 */


defined('ABSPATH') or die('Hey, what are you doing here? You silly human!');

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    // Checked for woocommerce presence and init plugin.
    $plugin = new ShipmasterForFedex\Inc\Init(plugin_basename(__FILE__));
}
