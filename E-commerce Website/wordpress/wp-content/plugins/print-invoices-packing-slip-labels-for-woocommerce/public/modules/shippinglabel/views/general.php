<?php
if (!defined('ABSPATH')) {
	exit;
}
?>
<h3 style="margin-bottom:0px; padding-bottom:5px; border-bottom:dashed 1px #ccc;"><?php _e('Shipping label', 'wf-woocommerce-packing-list'); ?></h3>
<table class="form-table wf-form-table">
	<?php
	Wf_Woocommerce_Packing_List_Admin::generate_form_field(array(
		array(
			'type'=>"select",
			'label'=>__("Shipping label size",'wf-woocommerce-packing-list'),
			'option_name'=>$this->module_id."[woocommerce_wf_packinglist_label_size]",
			'select_fields'=>array(
				2=>__('Full Page','wf-woocommerce-packing-list'),
			)
		),
        array(
            'type'=>"radio",
            'label'=>__("Add footer",'wf-woocommerce-packing-list'),
            'option_name'=>"woocommerce_wf_packinglist_footer_sl",
            'field_name'=>$this->module_id."[woocommerce_wf_packinglist_footer_sl]",
            'radio_fields'=>array(
                'Yes'=>__('Yes','wf-woocommerce-packing-list'),
                'No'=>__('No','wf-woocommerce-packing-list')
            ),
            'help_text'=>__("Add footer in shipping label",'wf-woocommerce-packing-list'),
        ),
	),$this->module_id);
	?>
</table>