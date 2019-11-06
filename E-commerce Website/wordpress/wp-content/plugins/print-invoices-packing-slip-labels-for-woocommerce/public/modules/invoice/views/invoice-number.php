<?php
if (!defined('ABSPATH')) {
	exit;
}
?>
<style type="text/css">
.wf_inv_num_frmt_hlp_btn{ cursor:pointer; }
.wf_inv_num_frmt_hlp table thead th{ font-weight:bold; text-align:left; }
.wf_inv_num_frmt_hlp table tbody td{ text-align:left; }
.wf_inv_num_frmt_hlp .wf_pklist_popup_body{min-width:300px; padding:20px;}
</style>
<!-- Invoice number Prefix/Suffix help popup -->
<div class="wf_inv_num_frmt_hlp wf_pklist_popup">
	<div class="wf_pklist_popup_hd">
		<span style="line-height:40px;" class="dashicons dashicons-calendar-alt"></span> <?php _e('Date formats','wf-woocommerce-packing-list');?>
		<div class="wf_pklist_popup_close">X</div>
	</div>
	<div class="wf_pklist_popup_body">
		<table class="wp-list-table widefat striped">
			<thead>
				<tr>
					<th><?php _e('Format','wf-woocommerce-packing-list');?></th><th><?php _e('Output','wf-woocommerce-packing-list');?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>[F]</td><td><?php echo date('F'); ?></td>
				</tr>
				<tr>
					<td>[dS]</td><td><?php echo date('dS'); ?></td>
				</tr>
				<tr>
					<td>[M]</td><td><?php echo date('M'); ?></td>
				</tr>
				<tr>
					<td>[m]</td><td><?php echo date('m'); ?></td>
				</tr>
				<tr>
					<td>[d]</td><td><?php echo date('d'); ?></td>
				</tr>
				<tr>
					<td>[D]</td><td><?php echo date('D'); ?></td>
				</tr>
				<tr>
					<td>[y]</td><td><?php echo date('y'); ?></td>
				</tr>
				<tr>
					<td>[Y]</td><td><?php echo date('Y'); ?></td>
				</tr>
				<tr>
					<td>[d/m/y]</td><td><?php echo date('d/m/y'); ?></td>
				</tr>
				<tr>
					<td>[d-m-Y]</td><td><?php echo date('d-m-Y'); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="wf_sub_tab_content" data-id="invoice-number">
<p>
	<?php _e('Use the configurations below to set up a custom invoice number with prefix/suffix/number series or mirror the order number respectively.','wf-woocommerce-packing-list');?>
</p>
<form method="post" class="wf_invoice_number_settings_form">
<?php
    // Set nonce:
    if (function_exists('wp_nonce_field'))
    {
        wp_nonce_field(WF_PKLIST_PLUGIN_NAME);
    }
?>
<input type="hidden" name="wf_reset_invoice_settings" value="1">
<table class="form-table wf-form-table">
	<?php
	Wf_Woocommerce_Packing_List_Admin::generate_form_field(array(
		array(
			'type'=>'select',
			'label'=>__("Invoice number format",'wf-woocommerce-packing-list'),
			'option_name'=>"woocommerce_wf_invoice_number_format",
			'select_fields'=>array(
				'[number]'=>'[number]',
				'[number][suffix]'=>'[number][suffix]',
				'[prefix][number]'=>'[prefix][number]',
				'[prefix][number][suffix]'=>'[prefix][number][suffix]',
			)
			//'help_text'=>"Eg: [prefix][number][suffix]",
		),
		array(
			'type'=>"radio",
			'label'=>__("Use order number as invoice number",'wf-woocommerce-packing-list'),
			'option_name'=>"woocommerce_wf_invoice_as_ordernumber",
			'radio_fields'=>array(
				'Yes'=>__('Yes','wf-woocommerce-packing-list'),
				'No'=>__('No','wf-woocommerce-packing-list')
			),
			'form_toggler'=>array(
				'type'=>'parent',
				'target'=>'wwpl_custom_inv_no',
			)
		),
	),$module_id);
	?>
	<tr id="woocommerce_wf_invoice_start_number_tr" wf_frm_tgl-id="wwpl_custom_inv_no" wf_frm_tgl-val="No" wf_frm_tgl-lvl="2">
		<th><label><?php _e("Invoice Start Number",'wf-woocommerce-packing-list'); ?></label></th>
		<td>
			<div class="wf-form-group">
				<?php
				$opt_name="woocommerce_wf_invoice_start_number";
				$vl=Wf_Woocommerce_Packing_List::get_option($opt_name,$module_id);
				?>				
				<input type="number" min="1" step="1" readonly="" style="background:#eee; width:60%; float:left;" name="<?php echo $opt_name;?>" value="<?php echo $vl;?>">
				<input style="float: right;" id="reset_invoice_button" type="button"  class="button button-primary" value="<?php _e('Reset Invoice no','wf-woocommerce-packing-list'); ?>"/>
			</div>
			<?php
			$opt_name="woocommerce_wf_Current_Invoice_number";
			$vl=Wf_Woocommerce_Packing_List::get_option($opt_name,$module_id);
			?>
			<input type="hidden" class="wf_current_invoice_number" value="<?php echo $vl;?>" name="<?php echo $opt_name;?>">
		</td>
		<td></td>
	</tr>
	<?php
	Wf_Woocommerce_Packing_List_Admin::generate_form_field(array(
		array(
			'label'=>__("Prefix",'wf-woocommerce-packing-list'),
			'option_name'=>"woocommerce_wf_invoice_number_prefix",
			'help_text'=>"Use any of the <a class=\"wf_inv_num_frmt_hlp_btn\">date formats</a> or alphanumeric characters.",
		),
		array(
			'label'=>__("Suffix",'wf-woocommerce-packing-list'),
			'option_name'=>"woocommerce_wf_invoice_number_postfix",
			'help_text'=>"Use any of the <a class=\"wf_inv_num_frmt_hlp_btn\">date formats</a> or alphanumeric characters.",
		),
		array(
			'type'=>'number',
			'label'=>__("Invoice length",'wf-woocommerce-packing-list'),
			'option_name'=>"woocommerce_wf_invoice_padding_number",
			'attr'=>'min="0"',
			'help_text'=>'Indicates the total length of the invoice number, excluding the length of prefix and suffix if added. If the length of the generated invoice number is less than the provided, it will be padded with ‘0’. E.g if you specify 7 as invoice length and your invoice number is 8009, it will be represented as 0008009 in the respective documents.
',
		)
	),$module_id);
	?>	
</table>
<?php
$settings_button_title='Update and Reset';
include plugin_dir_path( WF_PKLIST_PLUGIN_FILENAME )."admin/views/admin-settings-save-button.php";
?>
</form>
</div>