<?php 
$wf_filters_help_doc=array(
	'wf_pklist_alter_order_date'=> array(
			'description'=>'Alter order date',
			'params'=>'$order_date, $template_type, $order'
	),
	'wf_pklist_alter_invoice_date'=> array(
			'description'=>'Alter invoice date',
			'params'=>'$invoice_date, $template_type, $order'
	),
	'wf_pklist_alter_dispatch_date'=> array(
			'description'=>'Alter dispatch date',
			'params'=>'$dispatch_date, $template_type, $order'
	),
	'wf_pklist_add_additional_info'=> array(
			'description'=>'Add additional info',
			'params'=>'$additional_info, $template_type, $order'
	),
	'wf_pklist_alter_subtotal'=> array(
			'description'=>'Alter subtotal',
			'params'=>'$sub_total, $template_type, $order'
	),
	'wf_pklist_alter_subtotal_formated'=> array(
			'description'=>'Alter formated subtotal',
			'params'=>'$sub_total_formated, $template_type, $sub_total, $order'
	),
	'wf_pklist_alter_shipping_method'=> array(
			'description'=>'Alter shipping method',
			'params'=>'$shipping, $template_type, $order'
	),
	'wf_pklist_alter_fee'=> array(
			'description'=>'Alter fee',
			'params'=>'$fee_detail_html, $template_type, $fee_detail, $user_currency, $order'
	),
	'wf_pklist_alter_total_fee'=> array(
			'description'=>'Alter total fee',
			'params'=>'$fee_total_amount_formated, $template_type, $fee_total_amount, $user_currency, $order'
	),
	'wf_pklist_alter_product_table_head'=> array(
			'description'=>'Alter product table head.(Add, remove, change order)',
			'params'=>'$columns_list_arr, $template_type, $order'
	),
	'wf_pklist_alter_package_product_name'=> array(
			'description'=>'Alter product name in product (Works with Packing List, Shipping Label and Delivery note only)',
			'params'=>'$item_name, $template_type, $_product, $item, $order'
	),
	'wf_pklist_add_package_product_variation'=> array(
			'description'=>'Add product variation in product (Works with Packing List, Shipping Label and Delivery note only)',
			'params'=>'$item_meta, $template_type, $_product, $item, $order'
	),
	'wf_pklist_add_package_product_meta'=> array(
			'description'=>'Add product meta in product table (Works with Packing List, Shipping Label and Delivery note only)',
			'params'=>'$addional_product_meta, $template_type, $_product, $item, $order'
	),
	'wf_pklist_alter_package_item_quantiy'=> array(
			'description'=>'Alter item quantity in product table (Works with Packing List, Shipping Label and Delivery note only)',
			'params'=>'$item_quantity, $template_type, $_product, $item, $order'
	),
	'wf_pklist_alter_package_item_price'=> array(
			'description'=>'Alter item price in product table (Works with Packing List, Shipping Label and Delivery note only)',
			'params'=>'$item_weight, $template_type, $_product, $item, $order'
	),
	'wf_pklist_alter_package_item_total'=> array(
			'description'=>'Alter item total in product table (Works with Packing List, Shipping Label and Delivery note only)',
			'params'=>'$product_total, $template_type, $_product, $item, $order'
	),
	'wf_pklist_package_product_table_additional_column_val'=> array(
			'description'=>'You can add additional column head via `wf_pklist_alter_product_table_head` filter. You need to add column data via this filter. (Works with Packing List, Shipping Label and Delivery note only)',
			'params'=>'$column_data, $template_type, $columns_key, $_product, $item, $order'
	),
	'wf_pklist_alter_package_product_table_columns'=> array(
			'description'=>'Alter product table column. (Works with Packing List, Shipping Label and Delivery note only)',
			'params'=>'$product_row_columns, $template_type, $_product, $item, $order'
	),
	'wf_pklist_alter_product_name'=> array(
			'description'=>'Alter product name. (Works with Invoice and Dispatch label only)',
			'params'=>'$order_item_name, $template_type, $_product, $order_item, $order'
	),
	'wf_pklist_add_product_variation'=> array(
			'description'=>'Add product variation. (Works with Invoice and Dispatch label only)',
			'params'=>'$item_meta, $template_type, $_product, $order_item, $order'
	),
	'wf_pklist_add_product_meta'=> array(
			'description'=>'Add product meta. (Works with Invoice and Dispatch label only)',
			'params'=>'$addional_product_meta, $template_type, $_product, $order_item, $order'
	),
	'wf_pklist_alter_item_quantiy'=> array(
			'description'=>'Alter item quantity. (Works with Invoice and Dispatch label only)',
			'params'=>'$order_item_qty, $template_type, $_product, $order_item, $order'
	),
	'wf_pklist_alter_item_price'=> array(
			'description'=>'Alter item price. (Works with Invoice and Dispatch label only)',
			'params'=>'$item_price, $template_type, $_product, $order_item, $order'
	),
	'wf_pklist_alter_item_price_formated'=> array(
			'description'=>'Alter formated item price. (Works with Invoice and Dispatch label only)',
			'params'=>'$item_price_formated, $template_type, $item_price, $_product, $order_item, $order'
	),
	'wf_pklist_alter_item_total'=> array(
		'description'=>'Alter item total. (Works with Invoice and Dispatch label only)',
		'params'=>'$product_total, $template_type, $_product, $order_item, $order'
	),
	'wf_pklist_alter_item_total_formated'=> array(
		'description'=>'Alter formated item total. (Works with Invoice and Dispatch label only)',
		'params'=>'$product_total_formated, $template_type, $product_total, $_product, $order_item, $order'
	),
	'wf_pklist_product_table_additional_column_val'=> array(
		'description'=>'You can add additional column head via `wf_pklist_alter_product_table_head` filter. You need to add column data via this filter. (Works with Invoice and Dispatch label only)',
		'params'=>'$column_data, $template_type, $columns_key, $_product, $order_item, $order'
	),
	'wf_pklist_alter_product_table_columns'=> array(
		'description'=>'Alter product table column. (Works with Invoice and Dispatch label only)',
		'params'=>'$product_row_columns, $template_type, $_product, $order_item, $order'
	),
	'wf_pklist_alter_shipping_address'=> array(
		'description'=>'Alter shipping address',
		'params'=>'$shipping_address, $template_type, $order'
	),
	'wf_pklist_alter_billing_address'=> array(
		'description'=>'Alter billing address',
		'params'=>'$billing_address, $template_type, $order'
	),
	'wf_pklist_alter_shipping_from_address'=> array(
		'description'=>'Alter shipping from address',
		'params'=>'$fromaddress, $template_type, $order'
	),
	'wf_pklist_alter_template_html'=> array(
		'description'=>'Alter template HTML before printing.',
		'params'=>'$html, $template_type'
	),
);