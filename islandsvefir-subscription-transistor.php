<?php

/*
  Plugin Name: Islandsvefir Subscription Transistor
  Plugin URI: https://islandsvefir.is/
  Description: Tenging milli Woocommerce og Transistor
  Author: Islandsvefir
  Author URI: https://islandsvefir.is/
  Version: 1.0.0
 */

if (!defined('WPINC')) {
    return;
}
require( 'includes/class-islandsvefir-subscription-transistor.php' );
require('includes/class-islandsvefir-subscription-transistor-request.php');
require('includes/islandsvefir-subscription-transistor-constants.php');
require('includes/islandsvefir-subscription-transistor-hooks.php');
require('log/islandsvefir-logger.php');

if(!function_exists('run_islandsvefir_subscription_transistor')) :
function run_islandsvefir_subscription_transistor() {

    upgrade_islandsvefir_log();
}
endif;
register_activation_hook(__file__,'run_islandsvefir_subscription_transistor');
