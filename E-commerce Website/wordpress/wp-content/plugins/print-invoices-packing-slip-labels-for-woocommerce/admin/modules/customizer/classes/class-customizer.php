<?php
/**
 * Necessary functions for customizer module
 *
 * @link       
 * @since 2.5.0     
 *
 * @package  Wf_Woocommerce_Packing_List  
 */

if (!defined('ABSPATH')) {
    exit;
}
class Wf_Woocommerce_Packing_List_CustomizerLib
{
	const TO_HIDE_CSS='wfte_hidden';
	public static function set_order_data($find_replace,$template_type,$html,$order=null)
	{
		if(!is_null($order))
        {
        	$wc_version=WC()->version;
			$order_id=$wc_version<'2.7.0' ? $order->id : $order->get_id();

			$find_replace['[wfte_order_number]']=$order->get_order_number();
			if(Wf_Woocommerce_Packing_List_Public::module_exists('invoice'))
			{
				$find_replace['[wfte_invoice_number]']=Wf_Woocommerce_Packing_List_Invoice::generate_invoice_number($order,false); //do not force generate
			}else
			{
				$find_replace['[wfte_invoice_number]']='';
			}

			//order date
			$order_date_match=array();
			$order_date_format='m/d/Y';
			if(preg_match('/data-order_date-format="(.*?)"/s',$html,$order_date_match))
			{
				$order_date_format=$order_date_match[1];
			}
			$order_date=get_the_date($order_date_format,$order_id);
			$order_date=apply_filters('wf_pklist_alter_order_date',$order_date,$template_type,$order);
			$find_replace['[wfte_order_date]']=$order_date;

			//invoice date
			$invoice_date_match=array();
			$invoice_date_format='m/d/Y';
			if(preg_match('/data-invoice_date-format="(.*?)"/s',$html,$invoice_date_match))
			{
				$invoice_date_format=$invoice_date_match[1];
			}
			$invoice_date=get_the_date($invoice_date_format,$order_id);
			$invoice_date=apply_filters('wf_pklist_alter_invoice_date',$invoice_date,$template_type,$order);
			$find_replace['[wfte_invoice_date]']=$invoice_date;


			//dispatch date
			$dispatch_date_match=array();
			$dispatch_date_format='m/d/Y';
			if(preg_match('/data-dispatch_date-format="(.*?)"/s',$html,$dispatch_date_match))
			{
				$dispatch_date_format=$dispatch_date_match[1];
			}
			$dispatch_date=get_the_date($dispatch_date_format,$order_id);
			$dispatch_date=apply_filters('wf_pklist_alter_dispatch_date',$dispatch_date,$template_type,$order);
			$find_replace['[wfte_dispatch_date]']=$dispatch_date;
		}
		return $find_replace;
	}
	public static function package_doc_items($find_replace,$template_type,$order,$box_packing,$order_package)
	{
		if(!is_null($box_packing))
        {
			$box_details=$box_packing->wf_packinglist_get_table_content($order,$order_package);
			$box_name=$box_details['name'];
			if(Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_package_type')=='box_packing')
			{
				$box_name=apply_filters('wf_pklist_include_box_name_in_packinglist',$box_name,$box_details,$order);
				$find_replace['[wfte_box_name]']=(trim($box_name)!="" ? __('Box name','wf-woocommerce-packing-list').': '.$box_name : '');
			}else
			{
				$find_replace['[wfte_box_name]']='';
			}
		}else
		{
			$find_replace['[wfte_box_name]']='';
		}
		return $find_replace;
	}
	public static function set_other_data($find_replace,$template_type,$order=null)
	{
		//module settings are saved under module id
		$module_id=Wf_Woocommerce_Packing_List::get_module_id($template_type);

		//return policy
		$find_replace['[wfte_return_policy]']=nl2br(Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_return_policy'));

		//footer data
		$footer_data=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_footer',$module_id);
		if($footer_data===false || $footer_data=='') //custom footer not present or empty
		{
			//call main footer data
			$footer_data=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_footer');
		}
		$find_replace['[wfte_footer]']=nl2br($footer_data);

		//signature	
		$signture_url=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_invoice_signature',$module_id);
		$find_replace['[wfte_signature_url]']=$signture_url;

		//barcode, additional info
		if(!is_null($order))
        {
			include_once plugin_dir_path(WF_PKLIST_PLUGIN_FILENAME).'includes/class-wf-woocommerce-packing-list-barcode_generator.php';
			$invoice_number=Wf_Woocommerce_Packing_List_Public::module_exists('invoice') ? Wf_Woocommerce_Packing_List_Invoice::generate_invoice_number($order,false) : ''; 
			$invoice_number=apply_filters('wf_pklist_alter_barcode_data',$invoice_number,$template_type,$order);
			if($invoice_number!="")
			{
				$find_replace['[wfte_barcode_url]']=Wf_Woocommerce_Packing_List_Barcode_generator::generate($invoice_number);
				$find_replace['[wfte_barcode]']='1'; //just a value to prevent hiding barcode
			}else
			{
				$find_replace['[wfte_barcode_url]']='';
				$find_replace['[wfte_barcode]']='';
			}

			$additional_info='';
			$find_replace['[wfte_additional_data]']=apply_filters('wf_pklist_add_additional_info',$additional_info,$template_type,$order);			
		}

		return $find_replace;
	}
	public static function set_extra_charge_fields($find_replace,$template_type,$html,$order=null)
	{
		//module settings are saved under module id
		$module_id=Wf_Woocommerce_Packing_List::get_module_id($template_type);

		if(!is_null($order))
        {
        	$the_options=Wf_Woocommerce_Packing_List::get_settings($module_id);
			$order_items=$order->get_items();
			$wc_version=WC()->version;
			$order_id=$wc_version<'2.7.0' ? $order->id : $order->get_id();
			$user_currency=get_post_meta($order_id,'_order_currency',true);
			$sub_total=0;
			foreach($order_items as $order_item_id=>$order_item) 
			{
		    	$sub_total+=($wc_version< '2.7.0' ? $order->get_item_meta($order_item_id,'_line_total',true) : $order_item->get_total());
		    }
		    $sub_total=apply_filters('wf_pklist_alter_subtotal',$sub_total,$template_type,$order);
		    $sub_total_formated=wc_price($sub_total,array('currency'=>$user_currency));
		    $find_replace['[wfte_product_table_subtotal]']=apply_filters('wf_pklist_alter_subtotal_formated',$sub_total_formated,$template_type,$sub_total,$order);

		    //shipping method ==========================
		    if(get_option('woocommerce_calc_shipping')==='yes')
		    {
		        $shippingdetails=$order->get_items('shipping');
		        if (!empty($shippingdetails))
		        {
		            $shipping=$order->get_shipping_to_display();
		            $shipping=apply_filters('wf_pklist_alter_shipping_method',$shipping,$template_type,$order);
		            $find_replace['[wfte_product_table_shipping]']=__($shipping,'wf-woocommerce-packing-list');
		        }else
		        {
		            $find_replace['[wfte_product_table_shipping]']='';
		        }
		    }else
		    {
		        $find_replace['[wfte_product_table_shipping]']='';
		    }

		    //cart discount ==========================
		    $cart_discount=($wc_version<'2.7.0' ? $order->cart_discount : get_post_meta($order_id,'_cart_discount',true));
		    if($cart_discount>0) 
		    {
		        $find_replace['[wfte_product_table_cart_discount]']=wc_price($cart_discount,array('currency'=>$user_currency));
			}
			else
			{
		        $find_replace['[wfte_product_table_cart_discount]']='';
			}

			//order discount ==========================
			$order_discount=($wc_version<'2.7.0' ? $order->order_discount : get_post_meta($order_id,'_order_discount',true));
			if ($order_discount>0)
			{
		        $find_replace['[wfte_product_table_order_discount]']=wc_price($order_discount,array('currency'=>$user_currency));
			}
			else
			{
		        $find_replace['[wfte_product_table_order_discount]']='';				
			}

			//tax items ==========================
			$tax_items = $order->get_tax_totals();

			//total tax ==========================
			if(is_array($tax_items) && count($tax_items)>0)
			{
				$find_replace['[wfte_product_table_total_tax]']=wc_price($order->get_total_tax(),array('currency'=>$user_currency));
				$tax_type=array('ex_tax');
			}else
			{
				$find_replace['[wfte_product_table_total_tax]']='';
				$tax_type=array('in_tax');
			}

			$tax_items_match=array();
			$tax_items_row_html=''; //row html
			$tax_items_html='';
			$tax_items_total=0;
			if(preg_match('/<[^>]*data-row-type\s*=\s*"[^"]*\bwfte_tax_items\b[^"]*"[^>]*>(.*?)<\/tr>/s',$html,$tax_items_match))
			{
				$tax_items_row_html=isset($tax_items_match[0]) ? $tax_items_match[0] : '';
			}
			if(is_array($tax_items) && count($tax_items)>0)
			{
				foreach($tax_items as $tax_item)
				{
					if(in_array('ex_tax',$tax_type) && $tax_items_row_html!='')
					{
	                    $tax_label=apply_filters('wf_pklist_alter_taxitem_label',esc_html($tax_item->label),$template_type,$order);
	                    $tax_amount=wc_price($tax_item->amount,array('currency'=>$user_currency));
	                    $tax_items_html.=str_replace(array('[wfte_product_table_tax_item_label]','[wfte_product_table_tax_item]'),array($tax_label,$tax_amount),$tax_items_row_html);
	                }
	                else
	                {
	                    $tax_items_total+=$tax_item->amount;
	                }
				}
			}
			if($tax_items_row_html!='' && isset($tax_items_match[0])) //tax items placeholder exists
			{ 
				$find_replace[$tax_items_match[0]]=$tax_items_html; //replace tax items
			}

			//fee details ==========================
			$fee_details=$order->get_items('fee');
	        $fee_details_html='';
	        $fee_total_amount = 0;
	        if(!empty($fee_details))
	        {
		        foreach($fee_details as $fee_detail)
		        {
		            $fee_detail_html=wc_price($fee_detail['amount'],array('currency'=>$user_currency)).' via '.$fee_detail['name'];
		            $fee_detail_html=apply_filters('wf_pklist_alter_fee',$fee_detail_html,$template_type,$fee_detail,$user_currency,$order);
		            $fee_details_html.=($fee_detail_html!="" ? $fee_detail_html.'<br/>' : '');
		            $fee_total_amount+=$fee_detail['amount'];	            
		        }
		        $fee_total_amount_formated= wc_price($fee_total_amount,array('currency'=>$user_currency));
	        	$fee_total_amount_formated=apply_filters('wf_pklist_alter_total_fee',$fee_total_amount_formated,$template_type,$fee_total_amount,$user_currency,$order);
	        	$find_replace['[wfte_product_table_fee]']=$fee_details_html.($fee_total_amount_formated!="" ? '<br />'.$fee_total_amount_formated : '');
	    	}else
	        {
	        	$find_replace['[wfte_product_table_fee]']='';
	        }

	        //coupon details ==========================
	        $coupon_details=$order->get_items('coupon');
	        $coupon_info_arr=array();
	        $coupon_info_html='';
	        if(!empty($coupon_details))
	        {
				foreach($coupon_details as $coupon_id=>$coupon_detail)
				{
					$discount=$coupon_detail['discount_amount'];
					$discount_tax=$coupon_detail['discount_amount_tax'];
					$discount_total=$discount+$discount_tax;
					$coupon_info_arr[$coupon_detail['name']]=wc_price($discount_total,array('currency'=>$user_currency));
				}
				$coupon_code_arr=array_keys($coupon_info_arr);
				$coupon_info_html="{".implode("} , {",$coupon_code_arr)."}";
				$find_replace['[wfte_product_table_coupon]']=$coupon_info_html;
			}else
			{
				$find_replace['[wfte_product_table_coupon]']='';
			}

			//payment info ==========================
			$paymethod_title=($wc_version< '2.7.0' ? $order->payment_method_title : $order->get_payment_method_title());
        	$paymethod_title=__($paymethod_title,'wf-woocommerce-packing-list');
        	$find_replace['[wfte_product_table_payment_method]']=$paymethod_title;


        	//total amount ==========================
        	$total_price_final=($wc_version<'2.7.0' ? $order->order_total : get_post_meta($order_id,'_order_total',true));
			$total_price=$total_price_final; //taking value for future use
			$refund_amount=0;
			if($total_price_final)
			{
				$refund_data_arr=$order->get_refunds();
				if(!empty($refund_data_arr))
				{
					foreach($refund_data_arr as $refund_data)
					{
						$refund_id=($wc_version< '2.7.0' ? $refund_data->id : $refund_data->get_id());
						$refund_amount=(float) get_post_meta($refund_id,'_order_total',true);
						$total_price_final+=$refund_amount;
						$refund_amount-=$refund_amount;
					}
				}
			}
        	$incl_tax_text=__('incl. tax','wf-woocommerce-packing-list');
        	$incl_tax_text=apply_filters('wf_pklist_alter_tax_inclusive_text',$incl_tax_text,$template_type,$order);
        	//inclusive tax data      	
        	$tax_data=((in_array('in_tax',$tax_type) && !empty($tax_items_total)) ? ' ('.$incl_tax_text .wc_price($tax_items_total,array('currency'=>$user_currency)).')' : '');
        	$total_price_formated=wc_price($total_price,array('currency'=>$user_currency));
        	if(!empty($refund_amount) && $refund_amount!=0)
			{
				$total_price_final_formated=wc_price($total_price_final_formated,array('currency'=>$user_currency));
				$refund_formated=' ('.__('Refund','wf-woocommerce-packing-list').' -'.wc_price($refund_amount,array('currency'=>$user_currency)).')';
				$refund_formated=apply_filters('wf_pklist_alter_refund_html',$refund_formated,$template_type,$refund_amount,$order);
				$total_price_html='<strike>'.$total_price_formated.'</strike> '.$total_price_final_formated.$tax_data.$refund_formated;
			}else
			{
				$total_price_html=$total_price_formated.$tax_data;
			}
			$find_replace['[wfte_product_table_payment_total]']=$total_price_html;

		}
		return $find_replace;
	}
	public static function set_product_table($find_replace,$template_type,$html,$order=null,$box_packing=null,$order_package=null)
	{
		$match=array();
		$default_columns=array('image','sku','product','quantity','price','total_price');
		$columns_list_arr=array();
		if(preg_match('/\[wfte_product_table_start\](.*?)\[wfte_product_table_end\]/s',$html,$match))
		{
			$product_tb_html=$match[1];
			$thead_match=array();
			$th_html='';
			if(preg_match('/<thead(.*?)>(.*?)<\/thead>/s',$product_tb_html,$thead_match))
			{
				if(isset($thead_match[2]) && $thead_match[2]!="")
				{
					$thead_tr_match=array();
					if(preg_match('/<tr(.*?)>(.*?)<\/tr>/s',$thead_match[2],$thead_tr_match))
					{
						if(isset($thead_tr_match[2]))
						{
							$th_html=$thead_tr_match[2];
						}
					}
				}				
			}

			if($th_html!="")
			{
				$th_html_arr=explode('</th>',$th_html);

				$th_html_arr=array_filter($th_html_arr);
				$col_ind=0;
				foreach($th_html_arr as $th_single_html)
				{
					$th_single_html=trim($th_single_html);
					if($th_single_html!="")
					{
						$matchs=array();
						$is_have_col_id=preg_match('/col-type="(.*?)"/',$th_single_html,$matchs);
						$col_ind++;
						if($is_have_col_id) //column id exists
						{
							//adding column data to arrays
							$columns_list_arr[$matchs[1]]=$th_single_html.'</th>';
						}else
						{
							//adding column data to arrays
							$columns_list_arr[$col_ind]=$th_single_html.'</th>';
						}
					}
				}
				if(!is_null($order))
	    		{
	    			//filter to alter table head
					$columns_list_arr=apply_filters('wf_pklist_alter_product_table_head',$columns_list_arr,$template_type,$order);
				}
				$columns_list_arr=(!is_array($columns_list_arr) ? array() : $columns_list_arr);
				
				$columns_list_arr=apply_filters('wf_pklist_reverse_product_table_columns',$columns_list_arr,$template_type);

				//replace for table head section
				$find_replace[$th_html]=self::generate_product_table_head_html($columns_list_arr);
				$find_replace['[wfte_product_table_start]']='';
				$find_replace['[wfte_product_table_end]']='';
			}

			//product table body section
			$tbody_tag_match=array();
			$tbody_tag='';
			if(preg_match('/<tbody(.*?)>/s',$product_tb_html,$tbody_tag_match))
			{
				if(!is_null($box_packing))
				{
					$find_replace[$tbody_tag_match[0]]=$tbody_tag_match[0].self::generate_package_product_table_product_row_html($columns_list_arr,$template_type,$order,$box_packing,$order_package);
				}else
				{
					$find_replace[$tbody_tag_match[0]]=$tbody_tag_match[0].self::generate_product_table_product_row_html($columns_list_arr,$template_type,$order);
				}
			}
		}
		return $find_replace;
	}

	/*
	* Render product table column data for package type documents
	* 
	*/
	private static function generate_package_product_table_product_column_html($wc_version,$the_options,$order,$template_type,$_product,$item,$columns_list_arr)
	{
		$html='';
		$product_row_columns=array(); //for html generation
        $product_id=($wc_version< '2.7.0' ? $_product->id : $_product->get_id());
        $variation_id=($item['variation_id']!='' ? $item['variation_id']*1 : 0);
        $parent_id=wp_get_post_parent_id($variation_id);
        //$order_item_id=$item['order_item_id'];
        $dimension_unit=get_option('woocommerce_dimension_unit');
        $weight_unit = get_option('woocommerce_weight_unit');
        foreach($columns_list_arr as $columns_key=>$columns_value)
        {
        	//$hide_it=array_key_exists($key,$columns_list_arr) ? '' : 'style="display:none;"';
            if($columns_key=='image' || $columns_key=='-image')
            {
            	$column_data=self::generate_product_image_column_data($product_id,$variation_id,$parent_id);
            }
            elseif($columns_key=='sku' || $columns_key=='-sku')
            {
            	$column_data=$_product->get_sku();
            }
            elseif($columns_key=='product' || $columns_key=='-product')
            {
            	$product_name=apply_filters('wf_pklist_alter_package_product_name',$item['name'],$template_type,$_product,$item,$order);

            	//variation data======
            	$variation='';
            	if(isset($the_options['woocommerce_wf_packinglist_variation_data']) && $the_options['woocommerce_wf_packinglist_variation_data']=='Yes')
            	{
	            	$variation=$item['variation_data'];
			        $item_meta=$item['extra_meta_details'];
			        $variation_data=apply_filters('wf_pklist_add_package_product_variation',$item_meta,$template_type,$_product,$item,$order);
			        if(!empty($variation_data) && !is_array($variation_data))
			        {
			            $variation.='<br>'.$variation_data;
			        }
			        $variation='<br/><small style="word-break: break-word;">'.$variation.'</small>';
		    	}

		        //additional product meta
		        $addional_product_meta = '';
		        if(isset($the_options['wf_'.$template_type.'_product_meta_fields']) && count($the_options['wf_'.$template_type.'_product_meta_fields'])>0) 
		        {
		            $selected_product_meta_arr=$the_options['wf_'.$template_type.'_product_meta_fields'];
		            $product_meta_arr=Wf_Woocommerce_Packing_List::get_option('wf_product_meta_fields');
		            foreach($selected_product_meta_arr as $value)
		            {
		                $meta_data=get_post_meta($product_id,$value,true);
		                if($meta_data=='' && $variation_id>0)
		                {
		                	$meta_data=get_post_meta($parent_id,$value,true);
		                }
		                if(is_array($meta_data))
	                    {
	                        $output_data=(self::wf_is_multi($meta_data) ? '' : implode(', ',$meta_data));
	                    }else
	                    {
	                        $output_data=$meta_data;
	                    }
	                    $addional_product_meta.=$output_data!="" ? '<small>'.$product_meta_arr[$value].' : '.$output_data.'</small><br>' : '';
	                }
		        }
		        $addional_product_meta=apply_filters('wf_pklist_add_package_product_meta',$addional_product_meta,$template_type,$_product,$item,$order);
		        $column_data='<b>'.$product_name.'</b> <br />'.$variation.' <br />'.$addional_product_meta;
            }
            elseif($columns_key=='quantity' || $columns_key=='-quantity')
            {
            	$column_data=apply_filters('wf_pklist_alter_package_item_quantiy',$item['quantity'],$template_type,$_product,$item,$order);
            }
            elseif($columns_key=='total_weight' || $columns_key=='-total_weight')
            {
            	$item_weight=($item['weight']!= '') ? $item['weight']*$item['quantity'].' '.$weight_unit : __('n/a','wf-woocommerce-packing-list');
            	$column_data=apply_filters('wf_pklist_alter_package_item_price',$item_weight,$template_type,$_product,$item,$order);         	
            }
            elseif($columns_key=='total_price' || $columns_key=='-total_price')
            {
            	$product_total=$item['quantity']*$item['price'];
            	$currency = get_woocommerce_currency();
				$currency_symbol = get_woocommerce_currency_symbol($currency);
            	$column_data=$currency_symbol.apply_filters('wf_pklist_alter_package_item_total',$product_total,$template_type,$_product,$item,$order);

            }else //custom column by user
            {
            	$column_data='';
            	$column_data=apply_filters('wf_pklist_package_product_table_additional_column_val',$column_data,$template_type,$columns_key,$_product,$item,$order);
            }
            $product_row_columns[$columns_key]=$column_data;
        }
        $product_row_columns=apply_filters('wf_pklist_alter_package_product_table_columns',$product_row_columns,$template_type,$_product,$item,$order);
        if(is_array($product_row_columns))
        {
        	$html='<tr>';
        	foreach($product_row_columns as $columns_key=>$columns_value) 
        	{
        		$hide_it=($columns_key[0]=='-' ? 'class="'.self::TO_HIDE_CSS.'"' : ''); //column not enabled
        		$html.='<td '.$hide_it.'>';
        		$html.=$columns_value;
        		$html.='</td>';
        	}
        	$html.='</tr>';
        }
        return $html;
	}


	/*
	* Render product table row HTML for package type documents
	* 
	*/
	private static function generate_package_product_table_product_row_html($columns_list_arr,$template_type,$order=null,$box_packing=null,$order_package=null)
	{
		$html='';
		if(!is_null($order))
        {
        	//module settings are saved under module id
			$module_id=Wf_Woocommerce_Packing_List::get_module_id($template_type);
			$wc_version=WC()->version;
			$the_options=Wf_Woocommerce_Packing_List::get_settings($module_id);

        	$package_type =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_package_type');
            $category_wise_split =Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_product_category_wise_splitting',$module_id);
            if($package_type == 'single_packing' && $category_wise_split == 'Yes')
           	{
           		$product_cat_slug = array();
	            $l=0;
	            $wf_increment=0;
	            foreach ($order_package as $id => $item)
	            {
	                $wf_increment++;
	                $_product = wc_get_product($item['id']);                
	                if($item['variation_id'] !='')
	                {
	                   $parent_id=wp_get_post_parent_id($item['variation_id'] );
	                   $item['id']=$parent_id; 
	                }
	                $terms=get_the_terms($item['id'],'product_cat');
                    foreach($terms as $term)
                    {
                    	// Categories by name
                        $product_cat_slug[$term->name][$l] = $_product;
                        $product_cat_slug[$term->name][$l]->qty = $item['quantity'];
                        $product_cat_slug[$term->name][$l]->weight = $item['weight'];
                        $product_cat_slug[$term->name][$l]->price = $item['price'];
                        $product_cat_slug[$term->name][$l]->variation_data = $item['variation_data'];
                        $product_cat_slug[$term->name][$l]->variation_id = $item['variation_id'];
                        $product_cat_slug[$term->name][$l]->item_id = $item['id'];
                        $product_cat_slug[$term->name][$l]->name = $item['name'];
                        $product_cat_slug[$term->name][$l]->sku = $item['sku'];
                        $product_cat_slug[$term->name][$l]->order_item_id = $item['order_item_id'];
                        $product_cat_slug[$term->name][$l]->item= $item;
                    }
	            	$l++;
	            } 
	            $total_column=count($columns_list_arr);
            	foreach ($product_cat_slug as $cat_name=>$cat_datas)
            	{
            		$html.='<tr class="wfte_product_table_category_row"><td colspan="'.$total_column.'" align="center">'.$cat_name.'</td></tr>';
	            	foreach ($cat_datas as $cat_ind=>$cat_data) 
	            	{
	            		// get the product; if this variation or product has been deleted, this will return null...
			    		$_product=$cat_data;
			    		$item=$cat_data->item;
			    		if($_product)
			    		{
			    			$html.=self::generate_package_product_table_product_column_html($wc_version,$the_options,$order,$template_type,$_product,$item,$columns_list_arr);
			    		}
	            	}
	            }
           	}else
           	{
           		foreach($order_package as $id => $item)
	            {
	            	$_product = wc_get_product($item['id']);                
	                if($item['variation_id'] !='')
	                {
	                   $parent_id=wp_get_post_parent_id($item['variation_id'] );
	                   $item['id']=$parent_id; 
	                }
	            	$html.=self::generate_package_product_table_product_column_html($wc_version,$the_options,$order,$template_type,$_product,$item,$columns_list_arr);
	            }
           	}
        }else
        {
			$html=$this->dummy_product_row($columns_list_arr);
        }
        return $html;
	}

	/*
	* Render image column for product table
	* 
	*/
	private static function generate_product_image_column_data($product_id,$variation_id,$parent_id)
	{
		$image_id=get_post_thumbnail_id($product_id);
        $attachment=wp_get_attachment_image_src($image_id);
        if(empty($attachment[0]) && $variation_id>0) //attachment is empty and variation is available
        {		            
            $var_image_id=get_post_thumbnail_id($variation_id);
            $image_id=($var_image_id=='' ? get_post_thumbnail_id($parent_id) : $var_image_id);
            $attachment=wp_get_attachment_image_src($image_id);
        }
        return (!empty($attachment[0]) ? '<img src="'.$attachment[0].'"  height="30" width="30" style="border-radius:25%;"/>' : '');
	}


	/*
	* Render product table row HTML for non package type documents
	* 
	*/
	private static function generate_product_table_product_row_html($columns_list_arr,$template_type,$order=null)
	{
		$html='';
		//module settings are saved under module id
		$module_id=Wf_Woocommerce_Packing_List::get_module_id($template_type);
		if(!is_null($order))
        {
			$wc_version=WC()->version;
			$order_id=$wc_version<'2.7.0' ? $order->id : $order->get_id();
			$user_currency=get_post_meta($order_id,'_order_currency',true);
			$order_items=$order->get_items();
			$the_options=Wf_Woocommerce_Packing_List::get_settings($module_id);
			if($wc_version<'2.7.0')
			{
	            $order_prices_include_tax=$order->prices_include_tax;
	            $order_display_cart_ex_tax=$order->display_cart_ex_tax;
	        } else {
	            $order_prices_include_tax=$order->get_prices_include_tax();
	            $order_display_cart_ex_tax=get_post_meta($order_id,'_display_cart_ex_tax',true);
	        }
			foreach ($order_items as $order_item_id=>$order_item) 
			{
			    // get the product; if this variation or product has been deleted, this will return null...
			    $_product=$order->get_product_from_item($order_item);
			    if($_product)
			    {
			        $product_row_columns=array(); //for html generation
			        $product_id=($wc_version< '2.7.0' ? $_product->id : $_product->get_id());
			        $variation_id=($order_item['variation_id']!='' ? $order_item['variation_id']*1 : 0);
			        $parent_id=wp_get_post_parent_id($variation_id);
			        foreach($columns_list_arr as $columns_key=>$columns_value)
			        {
			            //$hide_it=array_key_exists($key,$columns_list_arr) ? '' : 'style="display:none;"';
			            if($columns_key=='image' || $columns_key=='-image')
			            {
			            	$column_data=self::generate_product_image_column_data($product_id,$variation_id,$parent_id);
			            }
			            elseif($columns_key=='sku' || $columns_key=='-sku')
			            {
			            	$column_data=$_product->get_sku();
			            }
			            elseif($columns_key=='product' || $columns_key=='-product')
			            {
			            	$product_name=apply_filters('wf_pklist_alter_product_name',$order_item['name'],$template_type,$_product,$order_item,$order);

			            	//variation data======
			            	$variation='';
			            	if(isset($the_options['woocommerce_wf_packinglist_variation_data']) && $the_options['woocommerce_wf_packinglist_variation_data']=='Yes')
			            	{
				            	$variation=self::get_order_line_item_variation_data($order_item_id,$_product,$order,$template_type);
						        $item_meta=function_exists('wc_get_order_item_meta') ? wc_get_order_item_meta($order_item_id,'',false) : $order->get_item_meta($order_item_id);
						        $variation_data=apply_filters('wf_pklist_add_product_variation',$item_meta,$template_type,$_product,$order_item,$order);
						        if(!empty($variation_data) && !is_array($variation_data))
						        {
						            $variation.='<br>'.$variation_data;
						        }
						        $variation='<br/><small style="word-break: break-word;">'.$variation.'</small>';
					    	}

					        //additional product meta
					        $addional_product_meta = '';
					        if(isset($the_options['wf_'.$template_type.'_product_meta_fields']) && count($the_options['wf_'.$template_type.'_product_meta_fields'])>0) 
					        {
					            $selected_product_meta_arr=$the_options['wf_'.$template_type.'_product_meta_fields'];
					            $product_meta_arr=Wf_Woocommerce_Packing_List::get_option('wf_product_meta_fields');
					            foreach($selected_product_meta_arr as $value)
					            {
					                $meta_data=get_post_meta($product_id,$value,true);
					                if($meta_data=='' && $variation_id>0)
					                {
					                	$meta_data=get_post_meta($parent_id,$value,true);
					                }
					                if(is_array($meta_data))
				                    {
				                        $output_data=(self::wf_is_multi($meta_data) ? '' : implode(', ',$meta_data));
				                    }else
				                    {
				                        $output_data=$meta_data;
				                    }
				                    $addional_product_meta.=$output_data!="" ? '<small>'.$product_meta_arr[$value].' : '.$output_data.'</small><br>' : '';
				                }
					        }
					        $addional_product_meta=apply_filters('wf_pklist_add_product_meta',$addional_product_meta,$template_type,$_product,$order_item,$order);
					        $column_data='<b>'.$product_name.'</b>'.$variation.' <br />'.$addional_product_meta;
			            }
			            elseif($columns_key=='quantity' || $columns_key=='-quantity')
			            {
			            	$column_data=apply_filters('wf_pklist_alter_item_quantiy',$order_item['qty'],$template_type,$_product,$order_item,$order);
			            }
			            elseif($columns_key=='price' || $columns_key=='-price')
			            {
			            	$item_price=$order->get_item_total($order_item,false,true);
	                    	$item_price=apply_filters('wf_pklist_alter_item_price',$item_price,$template_type,$_product,$order_item,$order);
	                    	$item_price_formated=wc_price($item_price,array('currency'=>$user_currency));
	                    	$column_data=apply_filters('wf_pklist_alter_item_price_formated',$item_price_formated,$template_type,$item_price,$_product,$order_item,$order);          	
			            }
			            elseif($columns_key=='total_price' || $columns_key=='-total_price')
			            {
			            	$product_total=($wc_version< '2.7.0' ? $order->get_item_meta($order_item_id,'_line_total',true) : $order_item->get_total());
	                        $product_total=apply_filters('wf_pklist_alter_item_total',$product_total,$template_type,$_product,$order_item,$order);
	                        if($order_display_cart_ex_tax || !$order_prices_include_tax)
	                        {
	                            $ex_tax_label=($order_prices_include_tax ? 1 : 0);
	                            $product_total_formated=wc_price($product_total,array('currency'=>$user_currency),array('ex_tax_label'=>$ex_tax_label));
	                            $column_data=apply_filters('wf_pklist_alter_item_total_formated',$product_total_formated,$template_type,$product_total,$_product,$order_item,$order);
	                        }
	                        else
	                        {
	                            $product_total_formated=wc_price($product_total,array('currency'=>$user_currency));
	                            $column_data=apply_filters('wf_pklist_alter_item_total_formated',$product_total_formated,$template_type,$product_total,$_product,$order_item,$order);
	                        }
			            }else //custom column by user
			            {
			            	$column_data='';
			            	$column_data=apply_filters('wf_pklist_product_table_additional_column_val',$column_data,$template_type,$columns_key,$_product,$order_item,$order);
			            }
			            $product_row_columns[$columns_key]=$column_data;
			        }
			        $product_row_columns=apply_filters('wf_pklist_alter_product_table_columns',$product_row_columns,$template_type,$_product,$order_item,$order);
			        if(is_array($product_row_columns))
			        {
			        	$html.='<tr>';
			        	foreach($product_row_columns as $columns_key=>$columns_value) 
			        	{
			        		$hide_it=($columns_key[0]=='-' ? 'class="'.self::TO_HIDE_CSS.'"' : ''); //column not enabled
			        		$html.='<td '.$hide_it.'>';
			        		$html.=$columns_value;
			        		$html.='</td>';
			        	}
			        	$html.='</tr>';
			        }
			    }
			}
		}else //dummy value for preview section (No order data available)
		{
			$html=self::dummy_product_row($columns_list_arr);
		}
		return $html;
	}
	private static function dummy_product_row($columns_list_arr)
	{
		$html='';
		$dummy_vals=array(
			'image'=>'<div style="display:inline-block; width:30px; height:30px; background:#ccc; margin:0px;"></div>',
			'product'=>'Jumbing LED Light Wall Ball',
			'sku'=>'A1234',
			'quantity'=>'1',
			'price'=>'$20.00',
			'total_price'=>'$100.00',
			'total_weight'=>'2 kg',
		);
		$html='<tr>';
		foreach($columns_list_arr as $columns_key=>$columns_value)
		{
			$is_hidden=($columns_key[0]=='-' ? 1 : 0); //column not enabled
			$column_id=($is_hidden==1 ? substr($columns_key,1) : $columns_key);
			$hide_it=($is_hidden==1 ? 'class="'.self::TO_HIDE_CSS.'"' : ''); //column not enabled
			$html.='<td '.$hide_it.'>';
			$html.=isset($dummy_vals[$column_id]) ? $dummy_vals[$column_id] : '';
			$html.='</td>';
		}
		$html.='</tr>';
		return $html;
	}
	private static function generate_product_table_head_html($columns_list_arr)
	{
		foreach ($columns_list_arr as $columns_key=>$columns_value)
		{
			$is_hidden=($columns_key[0]=='-' ? 1 : 0); //column not enabled
			if($is_hidden==1)
			{
				$columns_value_updated=self::addClass('',$columns_value,self::TO_HIDE_CSS);
				if($columns_value_updated==$columns_value) //no class attribute in some cases
				{
					$columns_value_updated=str_replace('<th>','<th class="'.self::TO_HIDE_CSS.'">',$columns_value);
				}
			}else
			{
				$columns_value_updated=self::removeClass('',$columns_value,self::TO_HIDE_CSS);
			}
			$columns_list_arr[$columns_key]=$columns_value_updated;
		}
		$columns_list_val_arr=array_values($columns_list_arr);
		return implode('',$columns_list_val_arr);
	}
	public static function set_extra_fields($find_replace,$template_type,$html,$order=null)
	{
		$extra_fields=array();
		//module settings are saved under module id
		$module_id=Wf_Woocommerce_Packing_List::get_module_id($template_type);
		if(!is_null($order))
        {
        	$the_options=Wf_Woocommerce_Packing_List::get_settings($module_id);
        	$default_options=Wf_Woocommerce_Packing_List::default_settings($module_id);
        	$default_fields=array_values(Wf_Woocommerce_Packing_List::$default_additional_data_fields);
        	$default_fields_label=array_flip(Wf_Woocommerce_Packing_List::$default_additional_data_fields);
        	$wc_version=(WC()->version<'2.7.0') ? 0 : 1;
        	$order = ($wc_version==0 ? new WC_Order($order) : new wf_order($order));
        	$order_id = ($wc_version==0 ? $order->id : $order->get_id());
        	if(isset($the_options['wf_'.$template_type.'_contactno_email']) && is_array($the_options['wf_'.$template_type.'_contactno_email'])) //if user selected any fields
        	{ 
        		$user_created_fields=Wf_Woocommerce_Packing_List::get_option('wf_additional_data_fields'); //this is plugin main setting so no need to specify module id
        		foreach($the_options['wf_'.$template_type.'_contactno_email'] as $val) //user selected fields
        		{
        			if(in_array($val,$default_fields))
        			{
        				$meta_vl='';
        				if($val=='email')
        				{
        					$meta_vl=($wc_version==0 ? $order->billing_email : $order->get_billing_email());
        				}elseif($val=='contact_number')
        				{
        					$meta_vl=($wc_version==0 ? $order->billing_phone : $order->get_billing_phone());
        				}elseif($val=='vat')
        				{
        					$meta_vl=($wc_version==0 ? $order->billing_VAT : get_post_meta($order_id,'_billing_VAT',true));
        				}elseif($val=='ssn')
        				{
        					$meta_vl=($wc_version==0 ? $order->billing_SSN : get_post_meta($order_id,'_billing_SSN',true));
        				}elseif($val=='cus_note')
        				{
        					$meta_vl=($wc_version==0 ? $order->customer_note : $order->get_customer_note());
        				}
        				$extra_fields[$val]=$meta_vl;
        			}else
        			{
        				//check meta key exists, and user created field exists
         				if(isset($user_created_fields[$val]))
        				{
        					$label=$user_created_fields[$val];
        					if(get_post_meta($order_id,'_billing_'.$val,true))
							{
								$extra_fields[$label]=get_post_meta($order_id,'_billing_'.$val,true);
							}
							if(get_post_meta($order_id,$val,true))
							{
								$extra_fields[$label]=get_post_meta($order_id,$val,true);
							}elseif(get_post_meta($order_id,'_'.$val,true))
							{
								$extra_fields[$label]=get_post_meta($order_id,'_'.$val,true);
							}
        				}
        			}       			
        		}
        	}

        	//shipping method
        	$order_shipping =($wc_version==0 ? $order->shipping_method : $order->get_shipping_method());
        	if(get_post_meta($order_id, '_tracking_provider', true) || $order_shipping)
        	{
        		$find_replace['[wfte_shipping_method]']=apply_filters('wf_pklist_alter_shipping_method',$order_shipping,$template_type,$order);
        	}else
        	{
        		$find_replace['[wfte_shipping_method]']='';
        	}

        	//tracking number
        	$tracking_key=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_tracking_number');
        	$tracking_data=apply_filters('wf_pklist_tracking_data_key',$tracking_key,$template_type,$order);
        	$tracking_details=get_post_meta($order_id,($tracking_key!='' ? $tracking_data : '_tracking_number'),true);
        	if($tracking_details)
        	{
        		$find_replace['[wfte_tracking_number]']=apply_filters('wf_pklist_alter_tracking_details',$tracking_details,$template_type,$order);    		
        	}else
        	{
        		$find_replace['[wfte_tracking_number]']='';
        	}

        	//filter to alter extra fields
        	$extra_fields=apply_filters('wf_pklist_alter_additional_fields',$extra_fields,$template_type,$order);
        	

        	$find_replace['[wfte_vat_number]']=isset($extra_fields['vat']) ? $extra_fields['vat'] : '';
        	$find_replace['[wfte_ssn_number]']=isset($extra_fields['ssn']) ? $extra_fields['ssn'] : '';
        	$find_replace['[wfte_email]']=isset($extra_fields['email']) ? $extra_fields['email'] : '';
        	$find_replace['[wfte_tel]']=isset($extra_fields['contact_number']) ? $extra_fields['contact_number'] : '';

        	$default_fields_placeholder=array(
        		'vat'=>'vat_number',
        		'ssn'=>'ssn_number',
        		'contact_number'=>'tel',
        	);


        	//extra fields
        	$ex_html='';
        	if(is_array($extra_fields))
        	{
	        	foreach($extra_fields as $ex_key=>$ex_vl)
	        	{
	        		if(!in_array($ex_key,$default_fields)) //not default fields like vat,ssn
        			{
        				if(is_string($ex_vl) && trim($ex_vl)!="")
        				{
        					$ex_html.='<div class="wfte_extra_fields">
					            <span>'.__(ucfirst($ex_key),'wf-woocommerce-packing-list').':</span>
					            <span>'.__($ex_vl,'wf-woocommerce-packing-list').'</span>
					          </div>';
        				}
	        		}else 
	        		{
	        			$placeholder_key=isset($default_fields_placeholder[$ex_key]) ? $default_fields_placeholder[$ex_key] : $ex_key;
	        			$placeholder='[wfte_'.$placeholder_key.']';
	        			if(strpos($html,$placeholder)===false) //default fields that have no placeholder
	        			{
	        				if(trim($ex_vl)!="")
	        				{
	        					$ex_html.='<div class="wfte_extra_fields">
						            <span>'.__($default_fields_label[$ex_key],'wf-woocommerce-packing-list').':</span>
						            <span>'.__($ex_vl,'wf-woocommerce-packing-list').'</span>
						          </div>';
	        				}
	        			}
	        		}
	        	}
        	}
        	$find_replace['[wfte_extra_fields]']=$ex_html;

        	$order_item_meta_data='';
        	$order_item_meta_data=apply_filters('wf_pklist_order_additional_item_meta',$order_item_meta_data,$template_type,$order);
        	$find_replace['[wfte_order_item_meta]']=$order_item_meta_data;
		}
		return $find_replace;
	}
	public static function set_logo($find_replace,$template_type)
	{
		//module settings are saved under module id
		$module_id=Wf_Woocommerce_Packing_List::get_module_id($template_type);

		$the_options=Wf_Woocommerce_Packing_List::get_settings($module_id);
		$the_options_main=Wf_Woocommerce_Packing_List::get_settings();
		$find_replace['[wfte_company_logo_url]']='';
		if(isset($the_options['woocommerce_wf_packinglist_logo']) && $the_options['woocommerce_wf_packinglist_logo']!="")
		{
			$find_replace['[wfte_company_logo_url]']=$the_options['woocommerce_wf_packinglist_logo'];
		}else
		{ //print_r($the_options_main);
			if($the_options_main['woocommerce_wf_packinglist_logo']!="")
			{
				$find_replace['[wfte_company_logo_url]']=$the_options_main['woocommerce_wf_packinglist_logo'];
			}				
		}
		$find_replace['[wfte_company_name]']=$the_options_main['woocommerce_wf_packinglist_companyname'];
		return $find_replace;
	}

	/**
	 * Get shipping address
	 *
	 * @param String $template_type Document type eg:invoice
	 * @param Object $order Order object 
	 * @return String billing address
	 */
	protected static function get_shipping_address($template_type,$order=null)
	{
		if(!is_null($order))
        {
			$the_options=Wf_Woocommerce_Packing_List::get_settings();
			$order = ( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
	        $order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
	        $shipping_address = array();
	        $countries = new WC_Countries;
	        $shipping_country = get_post_meta($order_id, '_shipping_country', true);
	        $shipping_state = get_post_meta($order_id, '_shipping_state', true);
	        $shipping_country_full = ( $shipping_country && isset($countries->countries[$shipping_country]) ) ? $countries->countries[$shipping_country] : $shipping_country;
	        
	        $shipping_address=array(
	        	'first_name'=>$order->shipping_first_name,
	        	'last_name'=>$order->shipping_last_name,
	        	'company'=>$order->shipping_company,
	        	'address_1'=>$order->shipping_address_1,
	        	'address_2'=>$order->shipping_address_2,
	        	'city'=>$order->shipping_city,
	        	'state'=>$shipping_state,
	        	'country'=>$shipping_country_full,
	        	'postcode'=>$order->shipping_postcode,
	        );
	        $shipping_address=apply_filters('wf_pklist_alter_shipping_address',$shipping_address,$template_type,$order);

	        $shipping_address['first_name']=(isset($shipping_address['first_name']) ? $shipping_address['first_name'] : '').' '.(isset($shipping_address['last_name']) ? $shipping_address['last_name'] : ''); 
	        unset($shipping_address['last_name']);
	        if(trim($shipping_address['first_name'])==""){ unset($shipping_address['first_name']); }

	        $shipping_address=self::merge_city_state_zip($shipping_address);

	        $shipping_addr_vals=is_array($shipping_address) ? array_filter(array_values($shipping_address)) : array();
	    	return implode("<br />",$shipping_addr_vals);
	    }else
	    {
	    	return '';
	    }
	}
	protected static function merge_city_state_zip($address)
	{
		//return $address; //disabled
		$arr=array();
		$arr[]=isset($address['city']) ? $address['city'] : '';
		$arr[]=isset($address['state']) ? $address['state'] : '';
		$arr[]=isset($address['postcode']) ? $address['postcode'] : '';
		unset($address['state']);
		unset($address['postcode']);
		$address['city']=implode(", ",array_filter(array_values($arr)));
		return $address;
	}
	public static function set_shipping_address($find_replace,$template_type,$order=null)
	{
		if(!is_null($order))
        {
			$shipping_address=self::get_shipping_address($template_type,$order);
        	$shipping_address=trim($shipping_address)=="" ? self::get_billing_address($template_type,$order) : $shipping_address;
	    	$find_replace['[wfte_shipping_address]']=$shipping_address;
	    }else
	    {
	    	$find_replace['[wfte_shipping_address]']='';
	    }
	    return $find_replace;
	}
	
	/**
	 * Get billing address
	 *
	 * @param String $template_type Document type eg:invoice
	 * @param Object $order Order object 
	 * @return String billing address
	 */
	protected static function get_billing_address($template_type,$order=null)
	{
		if(!is_null($order))
        {
			$the_options=Wf_Woocommerce_Packing_List::get_settings();
			$order = ( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
			$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
	        $order_data = $order->get_data();      
	        $countries = new WC_Countries;  
	        $billing_country = get_post_meta($order_id, '_billing_country', true);   
	        $billing_state = get_post_meta($order_id, '_billing_state', true);
	        $billing_country_full = ( $billing_country && isset($countries->countries[$billing_country]) ) ? $countries->countries[$billing_country] : $billing_country;
	        
	        $billing_address=array(
	        	'first_name'=>$order->billing_first_name,
	        	'last_name'=>$order->billing_last_name,
	        	'company'=>$order->billing_company,
	        	'address_1'=>$order->billing_address_1,
	        	'address_2'=>$order->billing_address_2,
	        	'city'=>$order->billing_city,
	        	'state'=>$billing_state,
	        	'country'=>$billing_country_full,
	        	'postcode'=>$order->billing_postcode,
	        );
	        $billing_address=apply_filters('wf_pklist_alter_billing_address',$billing_address,$template_type,$order);

	        $billing_address['first_name']=(isset($billing_address['first_name']) ? $billing_address['first_name'] : '').' '.(isset($billing_address['last_name']) ? $billing_address['last_name'] : ''); 
	        unset($billing_address['last_name']);
	        if(trim($billing_address['first_name'])==""){ unset($billing_address['first_name']); }

	        $billing_address=self::merge_city_state_zip($billing_address);
	        $billing_addr_vals=is_array($billing_address) ? array_filter(array_values($billing_address)) : array();
	        return implode("<br />",$billing_addr_vals);
	    }else
	    {
	    	return '';
	    }
	}
	public static function set_billing_address($find_replace,$template_type,$order=null)
	{
		if(!is_null($order))
        {
			$billing_address=self::get_billing_address($template_type,$order);
        	$find_replace['[wfte_billing_address]']=$billing_address;
	    }else
	    {
	    	$find_replace['[wfte_billing_address]']='';
	    }
	    return $find_replace;
	}
	public static function set_shipping_from_address($find_replace,$template_type,$order=null)
	{
		$the_options=Wf_Woocommerce_Packing_List::get_settings();
		
        $country_selected=$the_options['wf_country'];
        $country_arr=explode(":",$country_selected);
        $country=isset($country_arr[0]) ? $country_arr[0] : '';
        $state=isset($country_arr[1]) ? $country_arr[1] : '';
        $countries=new WC_Countries; 
        $fromaddress=array(
        	'name'=>$the_options['woocommerce_wf_packinglist_sender_name'],
        	'address_line1'=>$the_options['woocommerce_wf_packinglist_sender_address_line1'],
        	'address_line2'=>$the_options['woocommerce_wf_packinglist_sender_address_line2'],
        	'city'=>$the_options['woocommerce_wf_packinglist_sender_city'],
        	'state'=>$state,
        	'country'=>(isset($countries->countries[$country]) ? $countries->countries[$country] : ''),
        	'postcode'=>$the_options['woocommerce_wf_packinglist_sender_postalcode'],
        	'contact_number'=>$the_options['woocommerce_wf_packinglist_sender_contact_number'],
        	'vat'=>$the_options['woocommerce_wf_packinglist_sender_vat'],
        );
              
        $returnaddress=$fromaddress; //not affect from address filter to return address
        if(!is_null($order))
        {
        	$order=( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
        	$fromaddress=apply_filters('wf_pklist_alter_shipping_from_address',$fromaddress,$template_type,$order);
        	$returnaddress=apply_filters('wf_pklist_alter_shipping_return_address',$returnaddress,$template_type,$order);
        }
        $fromaddress=self::merge_city_state_zip($fromaddress);
        $returnaddress=self::merge_city_state_zip($returnaddress);

        $from_addr_vals=is_array($fromaddress) ? array_filter(array_values($fromaddress)) : array();
        $return_addr_vals=is_array($returnaddress) ? array_filter(array_values($returnaddress)) : array();
        $find_replace['[wfte_from_address]']=implode("<br />",$from_addr_vals);
        $find_replace['[wfte_return_address]']=implode("<br />",$return_addr_vals);
		return $find_replace;
	}
	public static function get_order_line_item_variation_data($item_id, $_product, $order,$template_type)
	{  
        if (WC()->version > '2.7.0') {
            global $wpdb;
            $meta_value_data = $wpdb->get_results($wpdb->prepare("SELECT meta_key, meta_value, meta_id, order_item_id
        FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = %d
        ORDER BY meta_id", absint($item_id)), ARRAY_A);
        }
        $variation = '';
        $meta_data = array();
        if ($metadata = ((WC()->version < '2.7.0') ? $order->has_meta($item_id) : $meta_value_data)) {
            foreach ($metadata as $meta) {
                // Skip hidden core fields
                if (in_array($meta['meta_key'], array(
                            '_qty',
                            '_reduced_stock',
                            '_tax_class',
                            '_product_id',
                            '_variation_id',
                            '_line_subtotal',
                            '_line_subtotal_tax',
                            '_line_total',
                            '_line_tax',
                            'method_id',
                            'cost',
                            '_refunded_item_id',
                        ))) {
                    continue;
                }
                
                // Skip serialised meta
                if (is_serialized($meta['meta_value'])) {
                    continue;
                }

                // Get attribute data
                if (taxonomy_exists(wc_sanitize_taxonomy_name($meta['meta_key']))) {
                    $term = get_term_by('slug', $meta['meta_value'], wc_sanitize_taxonomy_name($meta['meta_key']));
                    $meta_key = wc_attribute_label(wc_sanitize_taxonomy_name($meta['meta_key']));

                    $meta_value = isset($term->name) ? $term->name : $meta['meta_value'];

                    $meta_data[$meta_key] = $meta_value;
                } else {
                    $meta_data[$meta['meta_key']] = $meta['meta_value'];
                    $meta_data[$meta['meta_key']] = apply_filters('wf_pklist_alter_meta_value',$meta['meta_value'],$meta_data, $meta['meta_key']);


                }
            }
            $meta_data = apply_filters('wf_pklist_modify_meta_data', $meta_data);
            $variation='';
            foreach ($meta_data as $id => $value) 
            {
                if($value != ''){
                $current_item= wp_kses_post(rawurldecode($id)) . ' : ' . wp_kses_post(rawurldecode($value)) . ' ';
                $variation.= apply_filters('wf_alter_line_item_variation_data',$current_item, $meta_data, $id, $value);
                }
            }
        }
        return $variation;
    }
    private static function wf_is_multi($array)
    {
	    $multi_check = array_filter($array,'is_array');
	    if(count($multi_check)>0) return true;
	    return false;
    }
    public static function hide_empty_elements($find_replace,$html,$template_type)
    {
    	$hide_on_empty_fields=array('wfte_vat_number','wfte_ssn_number','wfte_email','wfte_tel','wfte_shipping_method','wfte_tracking_number','wfte_footer','wfte_return_policy',
    		'wfte_product_table_coupon',
			'wfte_product_table_fee',
			'wfte_product_table_total_tax',
			'wfte_product_table_order_discount',
			'wfte_product_table_cart_discount',
			'wfte_product_table_shipping',
			'wfte_order_item_meta',
		);
		$hide_on_empty_fields=apply_filters('wf_pklist_alter_hide_empty',$hide_on_empty_fields,$template_type);
    	foreach ($hide_on_empty_fields as $key => $value)
    	{
    		if(isset($find_replace['['.$value.']']))
	    	{
	    		if($find_replace['['.$value.']']=="")
	    		{
	    			$html=self::addClass($value,$html,self::TO_HIDE_CSS);
	    		}
	    	}else
	    	{
	    		$find_replace['['.$value.']']='';
	    		$html=self::addClass($value,$html,self::TO_HIDE_CSS);
	    	}
    	}
    	return $html;
    }
    public static function getElmByClass($elm_class,$html)
    {
    	$matches=array();
    	$re = '/<[^>]*class\s*=\s*["\'](.*?[^"\']*)'.$elm_class.'(.*?[^"\']*)["\'][^>]*>/m';
		if(preg_match($re,$html,$matches))
		{
		  return $matches;
		}else
		{
			return false;
		}
    }
    private static function filterCssClasses($class)
    {
    	$class_arr=explode(" ",$class);
    	return array_unique(array_filter($class_arr));
    }
	private static function removeClass($elm_class,$html,$remove_class)
    {
    	$match=self::getElmByClass($elm_class,$html);
    	if($match) //found
    	{
    		$elm_class=$match[1].$elm_class.$match[2];
    		$new_class_arr=self::filterCssClasses($elm_class);
			foreach(array_keys($new_class_arr,$remove_class) as $key) {
			    unset($new_class_arr[$key]);
			}
			$new_class=implode(" ",$new_class_arr);
    		return str_replace($elm_class,$new_class,$html);
    	}
    	return $html;
    }
    private static function addClass($elm_class,$html,$new_class)
    {
    	$match=self::getElmByClass($elm_class,$html);
    	if($match) //found
    	{
    		$elm_class=$match[1].$elm_class.$match[2];
    		$new_class_arr=self::filterCssClasses($elm_class.' '.$new_class);
			$new_class=implode(" ",$new_class_arr);
    		return str_replace($elm_class,$new_class,$html);
    	}
    	return $html;
    }

    public static function get_template_html_attr_vl($html,$attr,$default='')
	{
		$match_arr=array();
		$out=$default;
		if(preg_match('/'.$attr.'="(.*?)"/s',$html,$match_arr))
		{
			$out=$match_arr[1];
			$out=($out=='' ? $default : $out);
		}
		return $out;
	}

	/* 
	* Add dummy data for customizer design view
	* @return array
	*/
	public static function dummy_data_for_customize($find_replace,$template_type,$html)
	{
		$find_replace['[wfte_invoice_number]']=123456;
		$find_replace['[wfte_order_number]']=123456;

		$order_date_format=self::get_template_html_attr_vl($html,'data-order_date-format','m/d/Y');
		$find_replace['[wfte_order_date]']=date($order_date_format);

		$invoice_date_format=self::get_template_html_attr_vl($html,'data-invoice_date-format','m/d/Y');
		$find_replace['[wfte_invoice_date]']=date($invoice_date_format);

		$dispatch_date_format=self::get_template_html_attr_vl($html,'data-dispatch_date-format','m/d/Y');
		$find_replace['[wfte_dispatch_date]']=date($dispatch_date_format);
		
		//Dummy billing addresss
		$find_replace['[wfte_billing_address]']='Webtoffee <br>20 Maple Avenue <br>San Pedro <br>California <br>United States (US) <br>90731 <br>';
		
		//Dummy shipping addresss
		$find_replace['[wfte_shipping_address]']='Webtoffee <br>20 Maple Avenue <br>San Pedro <br>California <br>United States (US) <br>90731 <br>';
		
		$find_replace['[wfte_vat_number]']='123456';
    	$find_replace['[wfte_ssn_number]']='SSN123456';
    	$find_replace['[wfte_email]']='info@example.com';
    	$find_replace['[wfte_tel]']='+1 123 456';
    	$find_replace['[wfte_shipping_method]']='DHL';
    	$find_replace['[wfte_tracking_number]']='123456';
    	$find_replace['[wfte_order_item_meta]']='';
    	$find_replace['[wfte_extra_fields]']='';
		$find_replace['[wfte_product_table_subtotal]']='$100.00';
		$find_replace['[wfte_product_table_shipping]']='$0.00';
		$find_replace['[wfte_product_table_cart_discount]']='$0.00';
		$find_replace['[wfte_product_table_order_discount]']='$0.00';
		$find_replace['[wfte_product_table_total_tax]']='$0.00';
		$find_replace['[wfte_product_table_fee]']='$0.00';
		$find_replace['[wfte_product_table_payment_method]']='PayPal';
		$find_replace['[wfte_product_table_payment_total]']='$100.00';
		$find_replace['[wfte_product_table_coupon]']='{ABCD100}';
		$find_replace['[wfte_barcode_url]']='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEYAAAAeAQMAAACrPfpdAAAABlBMVEX///8AAABVwtN+AAAAAXRSTlMAQObYZgAAABdJREFUGJVj+MzDfPg8P/NnG4ZRFgEWAHrncvdCJcw9AAAAAElFTkSuQmCC';
		
		$find_replace['[wfte_return_policy]']='Mauris dignissim neque ut sapien vulputate, eu semper tellus porttitor. Cras porta lectus id augue interdum egestas. Suspendisse potenti. Phasellus mollis porttitor enim sit amet fringilla. Nulla sed ligula venenatis, rutrum lectus vel';
		$find_replace['[wfte_footer]']='Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc nec vehicula purus. Mauris tempor nec ipsum ac tempus. Aenean vehicula porttitor tortor, et interdum tellus fermentum at. Fusce pellentesque justo rhoncus';
		//on package type documents
		$find_replace['[wfte_box_name]']='';
		$find_replace['[wfte_qr_code]']='';

		$find_replace=apply_filters('wf_pklist_alter_dummy_data_for_customize',$find_replace,$template_type,$html);

		$tax_items_match=array();
		if(preg_match('/<[^>]*data-row-type\s*=\s*"[^"]*\bwfte_tax_items\b[^"]*"[^>]*>(.*?)<\/tr>/s',$html,$tax_items_match))
		{
			$find_replace[$tax_items_match[0]]='';
		}
		return $find_replace;
	}
}
