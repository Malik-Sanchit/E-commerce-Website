<?php
/**
 * Packinglist section of the plugin
 *
 * @link       
 * @since 2.5.0     
 *
 * @package  Wf_Woocommerce_Packing_List  
 */
if (!defined('ABSPATH')) {
    exit;
}

class Wf_Woocommerce_Packing_List_Shippinglabel
{
	public $module_id='';
	public $module_base='shippinglabel';
    private $customizer=null;
	public function __construct()
	{
		$this->module_id=Wf_Woocommerce_Packing_List::get_module_id($this->module_base);
		add_filter('wf_module_default_settings',array($this,'default_settings'),10,2);

		//hook to generate template html
		add_filter('wf_module_generate_template_html',array($this,'generate_template_html'),10,6);
		
		//hide empty fields on template
		add_filter('wf_pklist_alter_hide_empty',array($this,'hide_empty_elements'),10,6);

		add_action('wt_print_doc',array($this,'print_it'),10,2);
		add_action('wt_pklist_document_save_settings',array($this,'save_settings'),10,2);

		//initializing customizer		
		$this->customizer=Wf_Woocommerce_Packing_List::load_modules('customizer');

		add_filter('wf_pklist_document_setting_fields',array($this,'admin_settings_page'),10,1);
		add_filter('wt_print_metabox',array($this,'add_metabox_data'),10,3);
		add_filter('wt_print_actions',array($this,'add_print_buttons'),10,3);
		add_filter('wt_print_bulk_actions',array($this,'add_bulk_print_buttons'));

		add_filter('wf_pklist_alter_find_replace',array($this,'alter_find_replace'),10,5);
	}
	public function alter_find_replace($find_replace,$template_type,$order,$box_packing,$order_package)
	{
		if($template_type==$this->module_base)
		{
			$is_footer=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_footer_sl',$this->module_id);
			if($is_footer!='Yes')
			{
				$find_replace['wfte_footer']='wfte_hidden';
			}
		}
		return $find_replace;
	}
	public function save_settings()
	{
		$the_options=Wf_Woocommerce_Packing_List::get_settings($this->module_id);
		//save settings
		foreach($the_options as $key => $value) 
        {
            if(isset($_POST[$this->module_id][$key]))
            {
            	$the_options[$key]=$_POST[$this->module_id][$key];
            }
        }
        Wf_Woocommerce_Packing_List::update_settings($the_options,$this->module_id);
	    // save settings
	}

	public function hide_empty_elements($hide_on_empty_fields,$template_type)
	{
		if($template_type==$this->module_base)
		{
			$hide_on_empty_fields[]='wfte_qr_code';
			$hide_on_empty_fields[]='wfte_box_name';
			$hide_on_empty_fields[]='wfte_ship_date';
			$hide_on_empty_fields[]='wfte_weight';
			$hide_on_empty_fields[]='wfte_barcode';
		}
		return $hide_on_empty_fields;
	}

	/**
	 *  Items needed to be converted to HTML for print
	 */
	public function generate_template_html($find_replace,$html,$template_type,$order,$box_packing=null,$order_package=null)
	{
		if($template_type==$this->module_base)
		{	
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_shipping_address($find_replace,$template_type,$order);					
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::package_doc_items($find_replace,$template_type,$order,$box_packing,$order_package);	
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_other_data($find_replace,$template_type,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_order_data($find_replace,$template_type,$html,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_extra_fields($find_replace,$template_type,$html,$order);
			$find_replace=$this->set_total_weight($find_replace,$order);
		}
		return $find_replace;
	}

	protected function set_total_weight($find_replace,$order)
	{
		$total_weight=0;
		$order_items=$order->get_items();
		$find_replace['[wfte_weight]']=__('n/a','wf-woocommerce-packing-list');
		if($order_items)
		{
			foreach($order_items as $item)
			{
				$quantity=(int) $item->get_quantity(); // get quantity
		        $product=$item->get_product(); // get the WC_Product object
		        $weight=(float) $product->get_weight(); // get the product weight
		        $total_weight+=floatval($weight*$quantity);
			}
			$find_replace['[wfte_weight]']=$total_weight.' '.get_option('woocommerce_weight_unit');
		}
		return $find_replace;
	}

	public function default_settings($settings,$base_id)
	{
		if($base_id==$this->module_id)
		{
			return array(
				'woocommerce_wf_packinglist_label_size'=>2, //full page
				'woocommerce_wf_enable_multiple_shipping_label'=>'Yes',
				'woocommerce_wf_packinglist_footer_sl'=>'No',
				'wf_shipping_label_column_number'=>1,
				'wf_'.$this->module_base.'_contactno_email'=>array('contact_number','email'),
			);
		}else
		{
			return $settings;
		}
	}

	public function admin_settings_page()
	{
		include(plugin_dir_path( __FILE__ ).'views/general.php');
	}

	public function add_bulk_print_buttons($actions)
	{
		$actions['print_shippinglabel']=__('Print Shipping Label','wf-woocommerce-packing-list');
		return $actions;
	}
	public function add_print_buttons($html,$order,$order_id)
	{
		$this->generate_print_button_data($order,$order_id,"list_page");
		return $html;
	}
	private function generate_print_button_data($order,$order_id,$button_location="detail_page")
	{
		$icon_url=plugin_dir_url(__FILE__).'/assets/images/shippinglabel-icon.png';
		$label_txt=__('Print Shipping Label','wf-woocommerce-packing-list');
		Wf_Woocommerce_Packing_List_Admin::generate_print_button_data($order,$order_id,'print_shippinglabel',$label_txt,$icon_url,0,$button_location);
	}
	public function add_metabox_data($html,$order,$order_id)
	{
		$this->generate_print_button_data($order,$order_id);
		return $html;
	}
	
	
	/* 
	* Print_window for shippinglabel
	* @param $orders : order ids
	*/    
    public function print_it($order_ids,$action) 
    {
    	if($action=='print_shippinglabel')
    	{   
    		if(!is_array($order_ids))
    		{
    			return;
    		}   
	        if(!is_null($this->customizer))
	        {
	        	$pdf_name=$this->customizer->generate_pdf_name($this->module_base,$order_ids);

	        	//add custom size css here.
	        	if(Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_label_size',$this->module_id)==1) 
	        	{
	        		$this->customizer->custom_css.='
	        		.wfte_custom_shipping_size{
	        			width:'.Wf_Woocommerce_Packing_List::get_option('wf_custom_label_size_width',$this->module_id).'in !important;
	        			min-height:'.Wf_Woocommerce_Packing_List::get_option('wf_custom_label_size_height',$this->module_id).'in !important;
	        		}
	        		.wfte_main{ display:inline-block;}
	        		';
	        	}
	        	//RTL enabled
	        	if(Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_add_rtl_support')=='Yes')
	        	{
	        		$this->customizer->custom_css.='';
	        	}
	        	$html=$this->generate_order_template($order_ids,$pdf_name);
	        	echo $html;
	        }
	        exit();
    	}
    }
    public function generate_order_template($orders,$page_title)
    {
    	if(Wf_Woocommerce_Packing_List::is_from_address_available()===false) 
    	{
    		wp_die(__("Please add shipping from address in the plugin's general settings.",'wf-woocommerce-packing-list'), "", array());
        }

    	$template_type=$this->module_base;
    	//taking active template html
    	$html=$this->customizer->get_template_html($template_type);
    	$style_blocks=$this->customizer->get_style_blocks($html);
    	$html=$this->customizer->remove_style_blocks($html,$style_blocks);
    	$out='<style type="text/css">
    	.wfte_main{ margin:5px;}
    	div{ page-break-inside:avoid;}
    	</style>';
    	$out_arr=array();
    	if($html!="")
    	{
    		$is_single_page_print=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_enable_multiple_shipping_label',$this->module_id);
    		$label_column_number=Wf_Woocommerce_Packing_List::get_option('wf_shipping_label_column_number',$this->module_id);
			if((int) $label_column_number!=$label_column_number || (int) $label_column_number<=0)
			{
                $label_column_number=4;
            }

            //box packing
    		if (!class_exists('Wf_Woocommerce_Packing_List_Box_packing')) {
		        include_once WF_PKLIST_PLUGIN_PATH.'includes/class-wf-woocommerce-packing-list-box_packing.php';
		    }
	        $box_packing=new Wf_Woocommerce_Packing_List_Box_packing();
	        $order_pack_inc=0;
	        if($is_single_page_print=='Yes') //when paper size is not fit to handle labels, then shrink it or keep dimension, Default: shrink
			{
				$keep_label_dimension=false;
				$keep_label_dimension=apply_filters('wf_pklist_label_keep_dimension',$keep_label_dimension,$template_type);
			}
	        foreach ($orders as $order_id)
	        {
	        	$order = ( WC()->version < '2.7.0' ) ? new WC_Order($order_id) : new wf_order($order_id);
				$order_packages=null;
				$order_packages=$box_packing->create_order_package($order);
				$number_of_order_package=count($order_packages);
				if(!empty($order_packages)) 
				{
					foreach ($order_packages as $order_package_id => $order_package)
					{
						if($is_single_page_print=='Yes')
						{
							if(($order_pack_inc%$label_column_number)==0)
							{
								if($order_pack_inc>0) //not starting of loop
								{
									$out.='</div>'; 
								}
								$flex_wrap=$keep_label_dimension ? 'wrap' : 'nowrap';
								$out.='<div style="align-items:start; display:flex; flex-direction:row; flex-wrap:'.$flex_wrap.'; align-content:flex-start; align-items:stretch;">'; //comment this line to give preference to label size
							}
						}
						$order_pack_inc++;
						$order=( WC()->version < '2.7.0' ) ? new WC_Order($order_id) : new wf_order($order_id);						
						if($is_single_page_print=='No')
						{
							$out_arr[]=$this->customizer->generate_template_html($html,$template_type,$order,$box_packing,$order_package);
						}else
						{
							$out.=$this->customizer->generate_template_html($html,$template_type,$order,$box_packing,$order_package);	
						}						
					}
				}else
				{
					wp_die(__("Unable to print Packing slip. Please check the items in the order.",'wf-woocommerce-packing-list'), "", array());
				}
			}
			if($is_single_page_print=='Yes')
			{
				if($order_pack_inc>0) //items exists
				{
					$out.='</div>';
				}
			}else
			{
				$out=implode('<p class="pagebreak"></p>',$out_arr).'<p class="no-page-break">';
			}
			$out=$this->customizer->append_style_blocks($out,$style_blocks);
			//adding header and footer
			$out=$this->customizer->append_header_and_footer_html($out,$template_type,$page_title);
    	}
    	return $out;
    }
}
new Wf_Woocommerce_Packing_List_Shippinglabel();