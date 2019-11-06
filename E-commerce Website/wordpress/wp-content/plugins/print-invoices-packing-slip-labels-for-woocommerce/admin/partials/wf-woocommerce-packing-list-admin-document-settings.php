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
?>
<div class="wrap">
    <h2 class="wp-heading-inline">
	<?php _e('Document Settings','wf-woocommerce-packing-list');?>
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

	<div class="nav-tab-wrapper wp-clearfix wf-tab-head">
		<?php
        $tab_head_arr=array(
            'wf-other-documents'=>__('Other documents','wf-woocommerce-packing-list'),
        );
	    Wf_Woocommerce_Packing_List::generate_settings_tabhead($tab_head_arr,'document');
	    ?>
	</div>
	<div class="wf-tab-container">
        <form method="post" action="<?php echo esc_url($_SERVER["REQUEST_URI"]);?>" class="wf_settings_form">
            <input type="hidden" value="document_settings" class="wf_update_action" />
            <?php
            // Set nonce:
            if (function_exists('wp_nonce_field'))
            {
                wp_nonce_field(WF_PKLIST_PLUGIN_NAME);
            }
            include $wf_admin_view_path.'admin-document-settings-page.php'; 
            //settings form fields for module
            do_action('wf_pklist_document_settings_form');?>           
        </form>
        <?php do_action('wf_pklist_document_out_settings_form');?> 
    </div>
</div>