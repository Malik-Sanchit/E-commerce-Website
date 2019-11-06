<?php

/**
 * The plugin bootstrap file
 *
 *
 * @link              https://www.webtoffee.com/
 * @since             2.5.0
 * @package           Wf_Woocommerce_Packing_List
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce PDF Invoices, Packing Slips, Delivery Notes & Shipping Labels (Basic)
 * Plugin URI:        https://www.webtoffee.com/product/woocommerce-pdf-invoices-packing-slips/
 * Description:       Prints Packing List,Invoice,Delivery Note & Shipping Label.
 * Version:           2.5.2
 * Author:            WebToffee
 * Author URI:        https://www.webtoffee.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wf-woocommerce-packing-list
 * Domain Path:       /languages
 * WC tested up to:   3.6
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define ( 'WF_PKLIST_PLUGIN_DEVELOPMENT_MODE', false );
define ( 'WF_PKLIST_PLUGIN_BASENAME', plugin_basename(__FILE__) );
define ( 'WF_PKLIST_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define ( 'WF_PKLIST_PLUGIN_URL', plugin_dir_url(__FILE__));
define ( 'WF_PKLIST_PLUGIN_FILENAME',__FILE__);
define ( 'WF_PKLIST_POST_TYPE','wf_woocommerce_packing_list');
define ( 'WF_PKLIST_ACTIVATION_ID','wt_pdfinvoice');
define ( 'WF_PKLIST_DOMAIN','wf-woocommerce-packing-list');
define ( 'WF_PKLIST_SETTINGS_FIELD','Wf_Woocommerce_Packing_List');
define ( 'WF_PKLIST_PLUGIN_NAME','wf-woocommerce-packing-list');
define ( 'WF_PKLIST_PLUGIN_DESCRIPTION','WooCommerce PDF Invoices, Packing Slips, Delivery Notes & Shipping Labels');

/**
 * Currently plugin version.
 */
define( 'WF_PKLIST_VERSION', '2.5.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wf-woocommerce-packing-list-activator.php
 */
function activate_wf_woocommerce_packing_list() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wf-woocommerce-packing-list-activator.php';
	Wf_Woocommerce_Packing_List_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wf-woocommerce-packing-list-deactivator.php
 */
function deactivate_wf_woocommerce_packing_list() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wf-woocommerce-packing-list-deactivator.php';
	Wf_Woocommerce_Packing_List_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wf_woocommerce_packing_list' );
register_deactivation_hook( __FILE__, 'deactivate_wf_woocommerce_packing_list' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wf-woocommerce-packing-list.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.5.0
 */
function run_wf_woocommerce_packing_list() {

	$plugin = new Wf_Woocommerce_Packing_List();
	$plugin->run();

}
function woocommerce_packing_list_check_necessary()
{
	global $wpdb;
	$search_query = "SHOW TABLES LIKE %s";
	$tb=Wf_Woocommerce_Packing_List::$template_data_tb;
    $like = '%' . $wpdb->prefix.$tb.'%';
    if(!$wpdb->get_results($wpdb->prepare($search_query, $like),ARRAY_N)) 
    {
    	return false;
    	//wp_die(_e('Plugin not installed correctly','wf-woocommerce-packing-list'));
    }
    return true;	
}

if(woocommerce_packing_list_check_necessary() && in_array( 'woocommerce/woocommerce.php',apply_filters('active_plugins',get_option('active_plugins')))) 
{
	run_wf_woocommerce_packing_list(); 
}

function wf_woocommerce_packing_list_update_message( $data, $response )
{
    if(isset( $data['upgrade_notice']))
    {
        printf(
        '<style type="text/css">
        #print-invoices-packing-slip-labels-for-woocommerce-update .update-message p:last-child{ display:none;}
        #print-invoices-packing-slip-labels-for-woocommerce-update .wf-update-message p::before{ content: "";}
        #print-invoices-packing-slip-labels-for-woocommerce-update ul{ list-style:disc; margin-left:30px;}
        </style>
        .update-message
        <div class="update-message wf-update-message">%s</div>',
           $data['upgrade_notice']
        );
    }
}
add_action( 'in_plugin_update_message-print-invoices-packing-slip-labels-for-woocommerce/wf-woocommerce-packing-list.php', 'wf_woocommerce_packing_list_update_message', 10, 2 );