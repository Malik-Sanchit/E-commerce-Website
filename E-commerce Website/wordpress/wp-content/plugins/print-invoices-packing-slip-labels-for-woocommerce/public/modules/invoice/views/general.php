<?php
if (!defined('ABSPATH')) {
	exit;
}
?>
<div class="wf_sub_tab_content" data-id="general" style="display:block;">
<p><?php _e('Configure the general settings required for the invoice.','wf-woocommerce-packing-list');?></p>
<form method="post" action="<?php echo esc_url($_SERVER["REQUEST_URI"]);?>" class="wf_settings_form">
    <?php
    // Set nonce:
    if (function_exists('wp_nonce_field'))
    {
        wp_nonce_field('wf-update-invoice-'.WF_PKLIST_POST_TYPE);
    }
    ?>
    <input type="hidden" value="invoice_settings" class="wf_update_action" />
	<table class="form-table wf-form-table">
	    <?php
	    Wf_Woocommerce_Packing_List_Admin::generate_form_field(array(
	        array(
	            'type'=>"radio",
	            'label'=>__("Enable invoice",'wf-woocommerce-packing-list'),
	            'option_name'=>"woocommerce_wf_enable_invoice",
	            'radio_fields'=>array(
	                'Yes'=>__('Yes','wf-woocommerce-packing-list'),
	                'No'=>__('No','wf-woocommerce-packing-list')
	            ),
	        ),
	        array(
	            'type'=>'order_st_multiselect',
	            'label'=>__("Generate invoice for order statuses",'wf-woocommerce-packing-list'),
	            'option_name'=>"woocommerce_wf_generate_for_orderstatus",
	            'help_text'=>__("Order statuses for which an invoice should be generated.",'wf-woocommerce-packing-list'),
	            'order_statuses'=>$order_statuses,
	            'field_vl'=>array_flip($order_statuses),
	            'attr'=>'',
	        ),
	        array(
	            'type'=>"radio",
	            'label'=>__("Attach invoice PDF in email",'wf-woocommerce-packing-list'),
	            'option_name'=>"woocommerce_wf_add_invoice_in_mail",
	            'radio_fields'=>array(
	                'Yes'=>__('Yes','wf-woocommerce-packing-list'),
	                'No'=>__('No','wf-woocommerce-packing-list')
	            ),
	            'help_text'=>__('PDF version of currently active invoice template will be attached with the order email','wf-woocommerce-packing-list'),
	            'form_toggler'=>array(
	                'type'=>'parent',
	                'target'=>'wf_attach_invoice_on_email',
	            )
	        ),

	        //remove it
	        array(
	            'type'=>'order_st_multiselect',
	            'label'=>__("Email invoice for order statuses",'wf-woocommerce-packing-list'),
	            'option_name'=>"woocommerce_wf_attach_invoice",
	            //'help_text'=>__("Order statuses for which an invoice should be mailed.",'wf-woocommerce-packing-list'),
	            'order_statuses'=>$order_statuses,
	            'field_vl'=>$wf_generate_invoice_for,
	            'form_toggler'=>array(
	                'type'=>'child',
	                'id'=>'wf_attach_invoice_on_email',
	                'val'=>'Yes',
	            )
	        ),
	        
	    ),$module_id);
	    ?>
	    <?php 
	    Wf_Woocommerce_Packing_List_Admin::generate_form_field(array(
	        array(
	            'type'=>"radio",
	            'label'=>__("Enable print invoice option for customers",'wf-woocommerce-packing-list'),
	            'option_name'=>"woocommerce_wf_packinglist_frontend_info",
	            'radio_fields'=>array(
	                'Yes'=>__('Yes','wf-woocommerce-packing-list'),
	                'No'=>__('No','wf-woocommerce-packing-list')
	            ),
	            'help_text'=>__("Add print invoice button to the order email",'wf-woocommerce-packing-list'),
	        ),
	        array(
	            'type'=>"radio",
	            'label'=>__("Add customer note",'wf-woocommerce-packing-list'),
	            'option_name'=>"woocommerce_wf_add_customer_note_in_invoice",
	            'radio_fields'=>array(
	                'Yes'=>__('Yes','wf-woocommerce-packing-list'),
	                'No'=>__('No','wf-woocommerce-packing-list')
	            ),
	            'help_text'=>__("Add customer note in invoice",'wf-woocommerce-packing-list'),
	        ),
	        array(
	            'type'=>"uploader",
	            'label'=>__("Custom logo for invoice",'wf-woocommerce-packing-list'),
	            'option_name'=>"woocommerce_wf_packinglist_logo",
	            'help_text'=>__('If left blank, defaulted to logo from General settings.','wf-woocommerce-packing-list'),
	        ),
	    ),$module_id);
	    ?>
	</table>
	<?php
    include plugin_dir_path( WF_PKLIST_PLUGIN_FILENAME )."admin/views/admin-settings-save-button.php";
    ?>
    <?php 
    //settings form fields
    //do_action('wf_pklist_module_settings_form');
    ?>
</form> 
</div>      