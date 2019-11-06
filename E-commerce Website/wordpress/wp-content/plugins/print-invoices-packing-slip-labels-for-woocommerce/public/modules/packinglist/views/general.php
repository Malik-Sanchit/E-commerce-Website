<?php
if (!defined('ABSPATH')) {
	exit;
}
?>
<h3 style="margin-bottom:0px; padding-bottom:5px; border-bottom:dashed 1px #ccc;"><?php _e('Packing slip', 'wf-woocommerce-packing-list'); ?></h3>
<table class="form-table wf-form-table">
	<?php
	Wf_Woocommerce_Packing_List_Admin::generate_form_field(array(
		array(
			'type'=>"radio",
			'label'=>__("Include product image",'wf-woocommerce-packing-list'),
			'option_name'=>"woocommerce_wf_attach_image_packinglist",
			'field_name'=>$this->module_id."[woocommerce_wf_attach_image_packinglist]",
			'radio_fields'=>array(
				'Yes'=>__('Yes','wf-woocommerce-packing-list'),
				'No'=>__('No','wf-woocommerce-packing-list')
			),
			'help_text'=>__("Add product image in product table",'wf-woocommerce-packing-list'),
		),
		array(
            'type'=>"radio",
            'label'=>__("Add customer note",'wf-woocommerce-packing-list'),
            'option_name'=>"woocommerce_wf_add_customer_note_in_packinglist",
            'field_name'=>$this->module_id."[woocommerce_wf_add_customer_note_in_packinglist]",
            'radio_fields'=>array(
                'Yes'=>__('Yes','wf-woocommerce-packing-list'),
                'No'=>__('No','wf-woocommerce-packing-list')
            ),
            'help_text'=>__("Add customer note in packing list",'wf-woocommerce-packing-list'),
        ),
        array(
            'type'=>"radio",
            'label'=>__("Add footer",'wf-woocommerce-packing-list'),
            'option_name'=>"woocommerce_wf_packinglist_footer_pk",
            'field_name'=>$this->module_id."[woocommerce_wf_packinglist_footer_pk]",
            'radio_fields'=>array(
                'Yes'=>__('Yes','wf-woocommerce-packing-list'),
                'No'=>__('No','wf-woocommerce-packing-list')
            ),
            'help_text'=>__("Add footer in packing list",'wf-woocommerce-packing-list'),
        ),
	),$this->module_id);
	?>
</table>