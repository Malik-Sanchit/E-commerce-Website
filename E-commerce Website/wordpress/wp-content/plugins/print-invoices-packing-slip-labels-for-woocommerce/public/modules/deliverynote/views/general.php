<?php
if (!defined('ABSPATH')) {
	exit;
}
?>
<h3 style="margin-bottom:0px; padding-bottom:5px; border-bottom:dashed 1px #ccc;"><?php _e('Delivery note', 'wf-woocommerce-packing-list'); ?></h3>
<table class="form-table wf-form-table">
	<?php
	Wf_Woocommerce_Packing_List_Admin::generate_form_field(array(
		array(
			'type'=>"radio",
			'label'=>__("Include product image",'wf-woocommerce-packing-list'),
			'option_name'=>"woocommerce_wf_attach_image_deliverynote",
			'field_name'=>$this->module_id."[woocommerce_wf_attach_image_deliverynote]",
			'radio_fields'=>array(
				'Yes'=>__('Yes','wf-woocommerce-packing-list'),
				'No'=>__('No','wf-woocommerce-packing-list')
			)
		),
		array(
            'type'=>"radio",
            'label'=>__("Add customer note",'wf-woocommerce-packing-list'),
            'option_name'=>"woocommerce_wf_add_customer_note_in_deliverynote",
            'field_name'=>$this->module_id."[woocommerce_wf_add_customer_note_in_deliverynote]",
            'radio_fields'=>array(
                'Yes'=>__('Yes','wf-woocommerce-packing-list'),
                'No'=>__('No','wf-woocommerce-packing-list')
            ),
            'help_text'=>__("Add customer note in delivery note",'wf-woocommerce-packing-list'),
        ),
        array(
            'type'=>"radio",
            'label'=>__("Add footer",'wf-woocommerce-packing-list'),
            'option_name'=>"woocommerce_wf_packinglist_footer_dn",
            'field_name'=>$this->module_id."[woocommerce_wf_packinglist_footer_dn]",
            'radio_fields'=>array(
                'Yes'=>__('Yes','wf-woocommerce-packing-list'),
                'No'=>__('No','wf-woocommerce-packing-list')
            ),
            'help_text'=>__("Add footer in delivery note",'wf-woocommerce-packing-list'),
        ),
	),$this->module_id);
	?>
</table>