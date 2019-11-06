<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.webtoffee.com/
 * @since      2.5.0
 *
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/admin/partials
 */

$wf_admin_view_path=plugin_dir_path(WF_PKLIST_PLUGIN_FILENAME).'admin/views/';
$wf_img_path=WF_PKLIST_PLUGIN_URL . 'images/';
?>
<div class="wrap">
    <h2 class="wp-heading-inline">
	<?php _e('Settings','wf-woocommerce-packing-list');?>: 
	<?php _e('WooCommerce PDF Invoices, Packing Slips, Delivery Notes & Shipping Labels','wf-woocommerce-packing-list');?>
	</h2>

    <?php
    if(!get_option('wf_pklist_notice_dissmissed_250'))
    {
        ?>
        <div class="notice notice-warning is-dismissible wf_pklist_notice" style="background:#fff3cd;" data-pklist-notice-option="wf_pklist_notice_dissmissed_250">
            <p>
                <b><?php _e('Heads up! This is a major release with changes detailed below.');?></b> <br />
                <ul style="list-style:disc; margin-left:20px;">
                 <li><?php _e('Document templates structured and optimized to remove unwanted third party dependencies across the documents.');?></li>
                 <li><?php _e('Improved RTL support across documents.');?></li>
                 <li><?php _e('Improved WPML compatibility.');?></li>
                 <li><?php _e('Improved UI/UX.');?></li>
                 <li><?php _e('Plugin size restricted to 5.1 MB.');?></li>
                </ul>
                <?php _e('We have taken necessary precautions to migrate all your settings and HTML templates to the new version. However any changes applied via code filters cannot be migrated and have to be configured again manually in the new version.

                We recommend to try out your updates in a local environment prior to live. Contact');?><a href="https://www.webtoffee.com/support/" target="_blank"><?php _e('support');?></a> <?php _e('for assistance');?>.
            </p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text">Dismiss this notice.</span>
            </button>
        </div>
        <?php
    }
    ?>

    <div class="wf_settings_left">
    	<div class="nav-tab-wrapper wp-clearfix wf-tab-head">
    		<?php
    	    $tab_head_arr=array(
                'wf-documents'=>__('Documents','wf-woocommerce-packing-list'),
    	        'wf-general'=>__('General','wf-woocommerce-packing-list'),
    	        'wf-help'=>__('Help Guide','wf-woocommerce-packing-list')
    	    );
    	    if(isset($_GET['debug']))
    	    {
    	        $tab_head_arr['wf-debug']='Debug';
    	    }
    	    Wf_Woocommerce_Packing_List::generate_settings_tabhead($tab_head_arr);
    	    ?>
            <a class="nav-tab" style="background:#5ccc96; color:#fff;" href="https://www.webtoffee.com/product/woocommerce-pdf-invoices-packing-slips/"><?php _e('Upgrade to Premium for More Features','wf-woocommerce-packing-list');?></a>
    	</div>
    	<div class="wf-tab-container">
            <?php
            //inside the settings form
            $setting_views_a=array(
                'wf-general'=>'admin-settings-general.php',         
            );

            //outside the settings form
            $setting_views_b=array(   
                'wf-documents'=>'admin-settings-documents.php',          
                'wf-help'=>'admin-settings-help.php',           
            );
            if(isset($_GET['debug']))
            {
                $setting_views_b['wf-debug']='admin-settings-debug.php';
            }
            ?>
            <form method="post" action="<?php echo esc_url($_SERVER["REQUEST_URI"]);?>" class="wf_settings_form">
                <input type="hidden" value="plugin_settings" class="wf_update_action" />
                <?php
                // Set nonce:
                if (function_exists('wp_nonce_field'))
                {
                    wp_nonce_field(WF_PKLIST_PLUGIN_NAME);
                }
                foreach ($setting_views_a as $target_id=>$value) 
                {
                    $settings_view=$wf_admin_view_path.$value;
                    if(file_exists($settings_view))
                    {
                        include $settings_view;
                    }
                }
                ?>
                <?php 
                //settings form fields for module
                do_action('wf_pklist_plugin_settings_form');?>           
            </form>
            <?php
            foreach ($setting_views_b as $target_id=>$value) 
            {
                $settings_view=$wf_admin_view_path.$value;
                if(file_exists($settings_view))
                {
                    include $settings_view;
                }
            }
            ?>
            <?php do_action('wf_pklist_plugin_out_settings_form');?> 
        </div>
    </div>
    <div class="wf_settings_right">
        <?php include $wf_admin_view_path."goto-pro.php"; ?>
    </div>
</div>