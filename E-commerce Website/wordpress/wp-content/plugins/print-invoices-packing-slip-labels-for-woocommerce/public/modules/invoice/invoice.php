<?php
/**
 * Invoice section of the plugin
 *
 * @link       
 * @since 2.5.0     
 *
 * @package  Wf_Woocommerce_Packing_List  
 */
if (!defined('ABSPATH')) {
    exit;
}

class Wf_Woocommerce_Packing_List_Invoice
{
	public $module_id='';
	public static $module_id_static='';
	public $module_base='invoice';
    private $customizer=null;
    public $is_enable_invoice='';
	public function __construct()
	{
		$this->module_id=Wf_Woocommerce_Packing_List::get_module_id($this->module_base);
		self::$module_id_static=$this->module_id;
		add_filter('wf_module_default_settings',array($this,'default_settings'),10,2);
		add_filter('wf_module_customizable_items',array($this,'get_customizable_items'),10,2);
		add_filter('wf_module_non_options_fields',array($this,'get_non_options_fields'),10,2);
		add_filter('wf_module_non_disable_fields',array($this,'get_non_disable_fields'),10,2);
		
		//hook to add which fiedls to convert
		add_filter('wf_module_convert_to_design_view_html',array($this,'convert_to_design_view_html'),10,3);

		//hook to generate template html
		add_filter('wf_module_generate_template_html',array($this,'generate_template_html'),10,6);

		//filter to alter settings
		add_filter('wf_pklist_alter_settings',array($this,'alter_settings'),10,2);		
		add_filter('wf_pklist_alter_option',array($this,'alter_option'),10,4);

		//initializing customizer		
		$this->customizer=Wf_Woocommerce_Packing_List::load_modules('customizer');

		$this->is_enable_invoice=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_enable_invoice',$this->module_id);
		if($this->is_enable_invoice=='Yes')
		{
			add_action('wt_print_doc',array($this,'print_it'),10,2);
			add_filter('wt_print_metabox',array($this,'add_metabox_data'),10,3);
			add_filter('wt_print_actions',array($this,'add_print_buttons'),10,3);
			add_filter('wt_print_bulk_actions',array($this,'add_bulk_print_buttons'));
			add_filter('wt_frontend_print_actions',array($this,'add_frontend_print_buttons'),10,3);				
			add_filter('wt_email_print_actions',array($this,'add_email_print_buttons'),10,3);
			add_filter('wt_email_attachments',array($this,'add_email_attachments'),10,4);
		}

		add_action('wp_ajax_wf_invoice_advanced_settings',array($this,'advanced_settings'));
		add_action('wp_ajax_wf_reset_invoice_number',array($this,'reset_invoice_number'));

		add_action('wt_run_necessary',array($this,'run_necessary'));

		//invoice column and value
		add_filter('manage_edit-shop_order_columns',array($this,'add_invoice_column'),11); /* Add invoice number column to order page */
		add_action('manage_shop_order_posts_custom_column',array($this,'add_invoice_column_value'),11); /* Add value to invoice number column in order page */

		add_filter('wf_pklist_document_settings_tabhead',array( __CLASS__,'settings_tabhead'));
		add_action('wf_pklist_document_out_settings_form',array($this,'out_settings_form'));
	}

	public function alter_option($vl,$settings,$option_name,$base_id)
	{
		if($base_id==$this->module_id)
		{
			if($option_name=='wf_'.$this->module_base.'_contactno_email')
			{
				if($settings['woocommerce_wf_add_customer_note_in_'.$this->module_base]=='Yes')
				{
					if(array_search('cus_note',$vl)===false)
					{
						$vl[]='cus_note';
					}
				}else
				{
					if(($key=array_search('cus_note',$vl))!==false)
					{
						unset($vl[$key]);
					}
				}
			}
		}
		return $vl;
	}
	public function alter_settings($settings,$base_id)
	{
		if($base_id==$this->module_id)
		{
			$vl=$settings['wf_'.$this->module_base.'_contactno_email'];
			if($settings['woocommerce_wf_add_customer_note_in_'.$this->module_base]=='Yes')
			{
				if(array_search('cus_note',$vl)===false)
				{
					$vl[]='cus_note';
				}
			}else
			{
				if(($key=array_search('cus_note',$vl))!==false)
				{
					unset($vl[$key]);
				}
			}
			$settings['wf_'.$this->module_base.'_contactno_email']=$vl;
		}
		return $settings;
	}

	/**
	 *  
	 * 	Tab head for main settings page
	 */
	public static function settings_tabhead($arr)
	{
		$added=0;
		$out_arr=array();
		$out_arr[self::$module_id_static]=__('Invoice','wf-woocommerce-packing-list');
		foreach($arr as $k=>$v)
		{
			$out_arr[$k]=$v;
		}
		return $out_arr;
	}

	/**
	 * 
	 * Modulesettings form
	 * You can include a form, its outside module settings form
	 **/
	public function out_settings_form($args)
	{ 
		$order_statuses = wc_get_order_statuses();
		$wf_generate_invoice_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
		wp_enqueue_script('wc-enhanced-select');
		wp_enqueue_style('woocommerce_admin_styles',WC()->plugin_url().'/assets/css/admin.css');
		wp_enqueue_media();
		wp_enqueue_script($this->module_id,plugin_dir_url( __FILE__ ).'assets/js/main.js',array('jquery'),WF_PKLIST_VERSION);

		//localize params
		$params=array(
			'nonces' => array(
	            'main'=>wp_create_nonce($this->module_id),
	        ),
	        'ajax_url' => admin_url('admin-ajax.php'),
		);
		wp_localize_script($this->module_id,$this->module_id,$params);
		$the_options=Wf_Woocommerce_Packing_List::get_settings($this->module_id);
		
		if(isset($_POST['wf_settings_ajax_update']) && $_POST['wf_settings_ajax_update']=='invoice_settings')
		{
	        check_ajax_referer(WF_PKLIST_PLUGIN_NAME);

	        //multi select form fields array. (It will not return a $_POST val if it's value is empty so we need to set default value)
	        $default_val_needed_fields=array(
	        	'woocommerce_wf_generate_for_orderstatus'=>array(),
	        ); 
	        foreach($the_options as $key => $value) 
	        {
	            if(isset($_POST[$key]))
	            {
	            	$the_options[$key]=$_POST[$key];
	            }else
	            {
	            	if(array_key_exists($key,$default_val_needed_fields))
	            	{
	            		$the_options[$key]=$default_val_needed_fields[$key];
	            	}
	            }
	        }
	        Wf_Woocommerce_Packing_List::update_settings($the_options,$this->module_id);
	        echo '<div class="updated"><p><strong>' . __('Settings Updated.','wf-woocommerce-packing-list') . '</strong></p></div>';
	        if(!empty($_SERVER[ 'HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest')
	        {	            
	        	exit();
	        }
	    }
	    //initializing necessary modules, the argument must be current module name/folder
	    if(!is_null($this->customizer))
		{
			$this->customizer->init($this->module_base);
		}
		
		$view_file=plugin_dir_path( __FILE__ ).'views/invoice-admin-settings.php';
		$params=array(
			'order_statuses'=>$order_statuses,
			'wf_generate_invoice_for'=>$wf_generate_invoice_for,
			'the_options'=>$the_options,
			'module_id'=>$this->module_id,
			'module_base'=>$this->module_base,
		);
		Wf_Woocommerce_Packing_List_Admin::envelope_settings_tabcontent($this->module_id,$view_file,'',$params,0);
	}

	/**
	 *  Items needed to be converted to design view
	 */
	public function convert_to_design_view_html($find_replace,$html,$template_type)
	{
		if($template_type==$this->module_base)
		{
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_billing_address($find_replace,$template_type);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_shipping_address($find_replace,$template_type);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_product_table($find_replace,$template_type,$html);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_extra_charge_fields($find_replace,$template_type,$html);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_other_data($find_replace,$template_type);
		}
		return $find_replace;
	}

	/**
	 *  Items needed to be converted to HTML for print/download
	 */
	public function generate_template_html($find_replace,$html,$template_type,$order,$box_packing=null,$order_package=null)
	{
		if($template_type==$this->module_base)
		{
			//Generate invoice number while printing invoice
			self::generate_invoice_number($order);

			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_billing_address($find_replace,$template_type,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_shipping_address($find_replace,$template_type,$order);
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_product_table($find_replace,$template_type,$html,$order);
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_extra_charge_fields($find_replace,$template_type,$html,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_other_data($find_replace,$template_type,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_order_data($find_replace,$template_type,$html,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_extra_fields($find_replace,$template_type,$html,$order);
		}
		return $find_replace;
	}

	public function run_necessary()
	{
		$this->wf_filter_email_attach_invoice_for_status();
	}


	/** 
	* Check invoice number already exists
	* @return boolean
	*/
	public static function wf_is_invoice_number_exists($invoice_number) 
	{
		global $wpdb;
        $key = 'wf_invoice_number';
        $post_type = 'shop_order';

        $r = $wpdb->get_col($wpdb->prepare("
	    SELECT COUNT(pm.meta_value) AS inv_exists FROM {$wpdb->postmeta} pm
	    LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
	    WHERE pm.meta_key = '%s' 
	    AND p.post_type = '%s' AND pm.meta_value = '%s'
	", $key, $post_type,$invoice_number));
        return $r[0]>0 ? true : false;
	}

	/** 
	* Get all invoice numbers
	* @return int
	*/
	public static function wf_get_all_invoice_numbers() 
	{
        global $wpdb;
        $key = 'wf_invoice_number';
        $post_type = 'shop_order';

        $r = $wpdb->get_col($wpdb->prepare("
	    SELECT pm.meta_value FROM {$wpdb->postmeta} pm
	    LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
	    WHERE pm.meta_key = '%s' 
	    AND p.post_type = '%s'
	", $key, $post_type));
        return $r;
    }

	/**
	* Function to generate invoice number
	* @return mixed
	*/
    public static function generate_invoice_number($order,$force_generate=true) 
    { 
	    //if invoice is disabled then force generate is always false, otherwise the value of argument
	    $force_generate=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_enable_invoice',self::$module_id_static)=='No' ? false : $force_generate;

	    $order_num = $order->get_order_number();
	    $order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
	    $wf_invoice_id = get_post_meta($order_id,'wf_invoice_number',true);
	    if (!empty($wf_invoice_id))
	    {
	        return $wf_invoice_id;
	    }else
	    {
	    	if($force_generate==false)
	    	{
	    		return '';
	    	}
	    }
	    //$all_invoice_numbers =self::wf_get_all_invoice_numbers();
	    $wf_invoice_as_ordernumber =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_as_ordernumber',self::$module_id_static);
	    $generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',self::$module_id_static);
	    if($wf_invoice_as_ordernumber == "Yes")
	    {
	    	$inv_num=$order_num;	
	    }else
	    {
	    	$current_invoice_number =(int) Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_Current_Invoice_number',self::$module_id_static); 
	    	$inv_num=++$current_invoice_number;
	    	$padded_next_invoice_number=self::add_invoice_padding($inv_num,self::$module_id_static);
	        $postfix_prefix_padded_next_invoice_number=self::add_postfix_prefix($padded_next_invoice_number,self::$module_id_static);
	        while(self::wf_is_invoice_number_exists($postfix_prefix_padded_next_invoice_number))
            { 
                 $inv_num++;
                 $padded_next_invoice_number=self::add_invoice_padding($inv_num,self::$module_id_static);
                 $postfix_prefix_padded_next_invoice_number=self::add_postfix_prefix($padded_next_invoice_number,self::$module_id_static);               
            }
            Wf_Woocommerce_Packing_List::update_option('woocommerce_wf_Current_Invoice_number',$inv_num,self::$module_id_static);
	    }
	    $padded_invoice_number=self::add_invoice_padding($inv_num,self::$module_id_static);
        $invoice_number=self::add_postfix_prefix($padded_invoice_number,self::$module_id_static);
        update_post_meta($order_id,'wf_invoice_number',$invoice_number);
        return $invoice_number;
	}

	/**
	*
	* This function sets the autoincrement value while admin edits invoice number settings
	*/
	public function set_current_invoice_autoinc_number()
	{ 
		$wf_invoice_as_ordernumber =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_as_ordernumber',$this->module_id);
	    $generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
	    if($wf_invoice_as_ordernumber == "Yes")
	    {
	    	return true; //no need to set a starting number	
	    }else
	    {
	    	$current_invoice_number =(int) Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_Current_Invoice_number',$this->module_id); 
	    	$inv_num=++$current_invoice_number;
	    	$padded_next_invoice_number=self::add_invoice_padding($inv_num,$this->module_id);
	        $postfix_prefix_padded_next_invoice_number=self::add_postfix_prefix($padded_next_invoice_number,$this->module_id);
	        while(self::wf_is_invoice_number_exists($postfix_prefix_padded_next_invoice_number))
            { 
                 $inv_num++;
                 $padded_next_invoice_number=self::add_invoice_padding($inv_num,$this->module_id);
                 $postfix_prefix_padded_next_invoice_number=self::add_postfix_prefix($padded_next_invoice_number,$this->module_id);               
            }
            $inv_num;
            //$inv_num is the next invoice number so next starting number will be one lesser than the $inv_num
            $inv_num=$inv_num-1;
            Wf_Woocommerce_Packing_List::update_option('woocommerce_wf_Current_Invoice_number',$inv_num,$this->module_id);
            return true;
	    }
	    return false;
	}

	public static function add_invoice_padding($wf_invoice_number,$module_id) 
	{
        $padded_invoice_number = '';
        $padding_count =(int) Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_padding_number',$module_id)- strlen($wf_invoice_number);
        if ($padding_count > 0) {
            for ($i = 0; $i < $padding_count; $i++)
            {
                $padded_invoice_number .= '0';
            }
        }
        return $padded_invoice_number.$wf_invoice_number;
    }

	/* 
	* Add Prefix/Postfix to invoice number
	* @return string
	*/
	public static function add_postfix_prefix($padded_invoice_number,$module_id) 
	{          
        $invoice_format =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_number_format',$module_id);
        $prefix_data =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_number_prefix',$module_id);
        $postfix_data =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_number_postfix',$module_id);
        if($invoice_format=="")
        {
            if($prefix_data!='' && $postfix_data!='')
            {
            	$invoice_format='[prefix][number][suffix]';
            }
            elseif($prefix_data!='')
            {
            	$invoice_format = '[prefix][number]'; 
            }
            elseif($postfix_data!= '')
            {
                $invoice_format = '[number][suffix]'; 
            }
        }
        if($prefix_data != '')
        {
            $prefix_data=self::get_shortcode_replaced_date($prefix_data);
        }
        if($postfix_data != '')
        {
            $postfix_data=self::get_shortcode_replaced_date($postfix_data);
        }
        return str_replace(array('[prefix]','[number]','[suffix]'),array($prefix_data,$padded_invoice_number,$postfix_data),$invoice_format); 
    }

    /* 
	* Replace date shortcode from invoice prefix/postfix data
	* @return string
	*/
    public static function get_shortcode_replaced_date($shortcode_text) 
    {
        preg_match_all("/\[([^\]]*)\]/", $shortcode_text, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $date_shortcode) {
                $date=date($date_shortcode,strtotime('now'));
                $shortcode_text=str_replace("[$date_shortcode]", $date, $shortcode_text);
            }
        }
        return $shortcode_text;
    }

    /**
	* Ajax Hook to rest invoice number
	*/
	public function reset_invoice_number()
	{
		if(isset($_POST['wf_reset_invoice_settings']) && $_POST['wf_reset_invoice_settings']==1)
		{
			//saving settings
			$the_options=Wf_Woocommerce_Packing_List::get_settings($this->module_id);
			foreach($the_options as $key => $value) 
	        {
	            if(isset($_POST[$key]))
	            {
	            	$the_options[$key]=$_POST[$key];
	            }
	            if($key=='woocommerce_wf_invoice_padding_number')
	            {
	            	$the_options[$key]=(int) $the_options[$key];
	            }
	        }
	        Wf_Woocommerce_Packing_List::update_settings($the_options,$this->module_id);
	        
	        //upadate the invoice auto increment value according to current settings
	        $this->set_current_invoice_autoinc_number();
		}
		exit();
	}

	/**
	 * Function to add "Invoice" column in order listing page
	 *
	 * @since    2.5.0
	 */
	public function add_invoice_column($columns)
	{
		$columns['Invoice']=__('Invoice','wf-woocommerce-packing-list');
        return $columns;
	}

	/**
	 * Function to add value in "Invoice" column
	 *
	 * @since    2.5.0
	 */
	public function add_invoice_column_value($column)
	{
		global $post, $woocommerce, $the_order;
		if($column=='Invoice')
		{
			$order=wc_get_order($post->ID);
			$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
			$generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
			$force_generate=in_array(get_post_status($order_id),$generate_invoice_for) ? true :false;		
			echo self::generate_invoice_number($order,$force_generate);
		}
	}

	/**
	 * removing status other than generate invoice status
	 * @since     2.5.0
	 */
	private function wf_filter_email_attach_invoice_for_status()
	{
		$the_options=Wf_Woocommerce_Packing_List::get_settings($this->module_id);
		$email_attach_invoice_for_status=$the_options['woocommerce_wf_attach_invoice'];
		$generate_for_orderstatus=$the_options['woocommerce_wf_generate_for_orderstatus'];
		$email_attach_invoice_for_status=!is_array($email_attach_invoice_for_status) ? array() : $email_attach_invoice_for_status;
		$the_options['woocommerce_wf_attach_invoice']=array_intersect($email_attach_invoice_for_status,$generate_for_orderstatus);
		Wf_Woocommerce_Packing_List::update_settings($the_options,$this->module_id);
	}

	public function get_customizable_items($settings,$base_id)
	{
		if($base_id==$this->module_id)
		{
			$only_pro_html='<span style="color:red;"> ('.__('Pro version','wf-woocommerce-packing-list').')</span>';
			//these fields are the classname in template Eg: `company_logo` will point to `wfte_company_logo`
			return array(
				'company_logo'=>__('Company Logo','wf-woocommerce-packing-list'),
				//'barcode_disabled'=>__('Bar Code','wf-woocommerce-packing-list').$only_pro_html,
				'invoice_number'=>__('Invoice Number','wf-woocommerce-packing-list'),
				'order_number'=>__('Order Number','wf-woocommerce-packing-list'),
				'invoice_date'=>__('Invoice Date','wf-woocommerce-packing-list'),
				'order_date'=>__('Order Date','wf-woocommerce-packing-list'),
				'from_address'=>__('From Address','wf-woocommerce-packing-list'),
				'billing_address'=>__('Billing Address','wf-woocommerce-packing-list'),
				'shipping_address'=>__('Shipping Address','wf-woocommerce-packing-list'),
				'email'=>__('Email Field','wf-woocommerce-packing-list'),
				'tel'=>__('Tel Field','wf-woocommerce-packing-list'),
				//'shipping_method'=>__('Shipping Method','wf-woocommerce-packing-list'),
				'tracking_number_disabled'=>__('Tracking Number','wf-woocommerce-packing-list').$only_pro_html,
				'product_table'=>__('Product Table','wf-woocommerce-packing-list'),
				'product_table_subtotal_disabled'=>__('Sub Total','wf-woocommerce-packing-list').$only_pro_html,
				'product_table_shipping_disabled'=>__('Shipping','wf-woocommerce-packing-list').$only_pro_html,
				'product_table_cart_discount_disabled'=>__('Cart Discount','wf-woocommerce-packing-list').$only_pro_html,
				'product_table_order_discount_disabled'=>__('Order Discount','wf-woocommerce-packing-list').$only_pro_html,
				'product_table_total_tax_disabled'=>__('Total Tax','wf-woocommerce-packing-list').$only_pro_html,
				'product_table_fee_disabled'=>__('Fee','wf-woocommerce-packing-list').$only_pro_html,
				'product_table_coupon_disabled'=>__('Coupon info','wf-woocommerce-packing-list').$only_pro_html,
				'product_table_payment_method_disabled'=>__('Payment Method','wf-woocommerce-packing-list').$only_pro_html,
				'product_table_payment_total_disabled'=>__('Total','wf-woocommerce-packing-list').$only_pro_html,
				'footer'=>__('Footer','wf-woocommerce-packing-list'),
			);
		}
		return $settings;
	}

	/*
	* These are the fields that have no customizable options, Just on/off
	* 
	*/
	public function get_non_options_fields($settings,$base_id)
	{
		if($base_id==$this->module_id)
		{
			return array(
				'barcode',
				'footer',
				'return_policy',
			);
		}
		return $settings;
	}

	/*
	* These are the fields that are switchable
	* 
	*/
	public function get_non_disable_fields($settings,$base_id)
	{
		if($base_id==$this->module_id)
		{
			return array(
				'product_table_payment_summary'
			);
		}
		return $settings;
	}
	public function default_settings($settings,$base_id)
	{
		if($base_id==$this->module_id)
		{
			return array(
	        	'woocommerce_wf_generate_for_orderstatus'=>array('wc-completed'),
	        	'woocommerce_wf_attach_invoice'=>array(),
	        	'woocommerce_wf_packinglist_logo'=>'',
	        	'woocommerce_wf_add_invoice_in_mail'=>'No',
	        	'woocommerce_wf_packinglist_frontend_info'=>'No',
	        	'woocommerce_wf_invoice_number_format'=>"[number]",
				'woocommerce_wf_Current_Invoice_number'=>1,
				'woocommerce_wf_invoice_start_number'=>1,
				'woocommerce_wf_invoice_number_prefix'=>'',
				'woocommerce_wf_invoice_padding_number'=>0,
				'woocommerce_wf_invoice_number_postfix'=>'',
				'woocommerce_wf_invoice_as_ordernumber'=>"Yes",
				'woocommerce_wf_enable_invoice'=>"Yes",
				'woocommerce_wf_add_customer_note_in_invoice'=>"No", //Add customer note
				'woocommerce_wf_packinglist_variation_data'=>'Yes', //Add product variation data
				'wf_'.$this->module_base.'_contactno_email'=>array('contact_number','email'),
			);
		}else
		{
			return $settings;
		}
	}
	public function add_bulk_print_buttons($actions)
	{
		$actions['print_invoice']=__('Print Invoices','wf-woocommerce-packing-list');
		$actions['download_invoice']=__('Download Invoices','wf-woocommerce-packing-list');
		return $actions;
	}
	public function add_print_buttons($html,$order,$order_id)
	{
		$this->generate_print_button_data($order,$order_id,"list_page");
		return $html;
	}
	private function generate_print_button_data($order,$order_id,$button_location="detail_page")
	{
		$invoice_number=self::generate_invoice_number($order,false);
		$generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
		$is_show=0;
		$is_show_prompt=1;
		$icon_url=plugin_dir_url(__FILE__).'/assets/images/invoice-icon.png';
		$icon_url_dw=plugin_dir_url(__FILE__).'/assets/images/download-invoice.png';
		$label_txt=__('Print Invoice','wf-woocommerce-packing-list');
		$label_txt_dw=__('Download Invoice','wf-woocommerce-packing-list');
		if(in_array(get_post_status($order_id), $generate_invoice_for) || !empty($invoice_number))
        {
        	$is_show_prompt=0;
        	$is_show=1;
		}else
		{
			if(empty($invoice_number))
			{
				$is_show_prompt=1;
				$is_show=1;
			}
		}
		if($is_show==1)
		{
			if($button_location=="detail_page")
			{
			?>
			<tr>
				<td style="height:30px;">
					<?php _e('<strong>Invoice Number: </strong>'.$invoice_number); ?>
				</td>
			</tr>
			<?php
			}
			Wf_Woocommerce_Packing_List_Admin::generate_print_button_data($order,$order_id,'print_invoice',$label_txt,$icon_url,$is_show_prompt,$button_location);
			Wf_Woocommerce_Packing_List_Admin::generate_print_button_data($order,$order_id,'download_invoice',$label_txt_dw,$icon_url_dw,$is_show_prompt,$button_location);
		}
	}
	public function add_metabox_data($html,$order,$order_id)
	{
		$this->generate_print_button_data($order,$order_id);
		return $html;
	}
	public function add_email_attachments($attachments,$order,$order_id,$status)
	{ 
		$show_print_button_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_attach_invoice',$this->module_id);
		if(in_array('wc-'.$order->get_status(),$show_print_button_for) && $status!='customer_partially_refunded_order') 
		{                    
           	if(Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_add_invoice_in_mail',$this->module_id)== "Yes")
           	{          		
           		if(!is_null($this->customizer))
		        { 
		        	$order_ids=array($order_id);
		        	$pdf_name=$this->customizer->generate_pdf_name($this->module_base,$order_ids);
		        	$this->customizer->template_for_pdf=true;
		        	$html=$this->generate_order_template($order_ids,$pdf_name);
		        	$attachments[]=$this->customizer->generate_template_pdf($html,$this->module_base,$pdf_name,'attach');
		        }
           	}
        }
        return $attachments;
	}
	public function add_email_print_buttons($html,$order,$order_id)
	{
		$show_on_frontend=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_frontend_info',$this->module_id);
		if($show_on_frontend=='Yes')
		{
			$show_print_button_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_attach_invoice',$this->module_id);
	        if(in_array('wc-'.$order->get_status(),$show_print_button_for))
	        {
	            Wf_Woocommerce_Packing_List::generate_print_button_for_user($order,$order_id,'print_invoice',esc_html__('Print Invoice','wf-woocommerce-packing-list'),true); 
	        }
	    }
	    return $html;
	}
	public function add_frontend_print_buttons($html,$order,$order_id)
	{
		$show_on_frontend=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_frontend_info',$this->module_id);
		if($show_on_frontend=='Yes')
		{
			$generate_invoice_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
			if(in_array('wc-'.$order->get_status(),$generate_invoice_for))
			{
				Wf_Woocommerce_Packing_List::generate_print_button_for_user($order,$order_id,'print_invoice',esc_html__('Print Invoice','wf-woocommerce-packing-list'));
			}
		}
		return $html;
	}
	
	/* 
	* Ajax function for saving advanced settings
	*/
	public function advanced_settings()
	{
		Wf_Woocommerce_Packing_List_Admin::advanced_settings($this->module_base,$this->module_id);
	}
	
	/* 
	* Print_window for invoice
	* @param $orders : order ids
	*/    
    public function print_it($order_ids,$action) 
    {
    	if($action=='print_invoice' || $action=='download_invoice')
    	{   
    		if(!is_array($order_ids))
    		{
    			return;
    		}    
	        if(!is_null($this->customizer))
	        {
	        	$pdf_name=$this->customizer->generate_pdf_name($this->module_base,$order_ids);
	        	$this->customizer->template_for_pdf=($action=='download_invoice' ? true : false);
	        	
	        	$html=$this->generate_order_template($order_ids,$pdf_name);

	        	if($action=='download_invoice')
	        	{
	        		$this->customizer->generate_template_pdf($html,$this->module_base,$pdf_name,'download');
	        	}else
	        	{
	        		echo $html;
	        	}
	        }
	        exit();
    	}
    }
    public function generate_order_template($orders,$page_title)
    {
    	$template_type=$this->module_base;
    	//taking active template html
    	$html=$this->customizer->get_template_html($template_type);
    	$style_blocks=$this->customizer->get_style_blocks($html);
    	$html=$this->customizer->remove_style_blocks($html,$style_blocks);
    	$out='';
    	if($html!="")
    	{
    		$number_of_orders=count($orders);
			$order_inc=0;
			foreach($orders as $order_id)
			{
				$order_inc++;
				$order=( WC()->version < '2.7.0' ) ? new WC_Order($order_id) : new wf_order($order_id);
				$out.=$this->customizer->generate_template_html($html,$template_type,$order);
				if($number_of_orders>1 && $order_inc<$number_of_orders)
				{
                	$out.='<p class="pagebreak"></p>';
	            }else
	            {
	                //$out.='<p class="no-page-break"></p>';
	            }
			}
			$out=$this->customizer->append_style_blocks($out,$style_blocks);
			$out=$this->customizer->append_header_and_footer_html($out,$template_type,$page_title);
    	}
    	return $out;
    }
}
new Wf_Woocommerce_Packing_List_Invoice();