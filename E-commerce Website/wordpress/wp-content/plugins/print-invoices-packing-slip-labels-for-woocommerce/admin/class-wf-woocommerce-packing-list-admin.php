<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.webtoffee.com/
 * @since      2.5.0
 *
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/admin
 * @author     WebToffee <info@webtoffee.com>
 */
class Wf_Woocommerce_Packing_List_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.5.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.5.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/*
	 * module list, Module folder and main file must be same as that of module name
	 * Please check the `register_modules` method for more details
	 */
	public static $modules=array(
		'customizer',
		'uninstall-feedback',
	);

	public static $existing_modules=array();

	public $bulk_actions=array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.5.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    2.5.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wf-woocommerce-packing-list-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    2.5.0
	 */
	public function enqueue_scripts() 
	{
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wf-woocommerce-packing-list-admin.js', array( 'jquery','wp-color-picker'), $this->version, false );
		//order list page bulk action filter
		$this->bulk_actions=apply_filters('wt_print_bulk_actions',$this->bulk_actions);

		$params=array(
			'nonces' => array(
		            'wf_packlist' => wp_create_nonce(WF_PKLIST_PLUGIN_NAME),
		     ),
			'ajaxurl' => admin_url('admin-ajax.php'),
			'no_image'=>Wf_Woocommerce_Packing_List::$no_image,
			'bulk_actions'=>array_keys($this->bulk_actions),
			'print_action_url'=>admin_url('?print_packinglist=true'),
			'msgs'=>array(
				'settings_success'=>__('Settings updated.','wf-woocommerce-packing-list'),
				'all_fields_mandatory'=>__('All fields are mandatory','wf-woocommerce-packing-list'),
				'settings_error'=>__('Unable to update Settings.','wf-woocommerce-packing-list'),
				'select_orders_first'=>__('You have to select order(s) first!','wf-woocommerce-packing-list'),
				'invoice_not_gen_bulk'=>__('One or more order do not have invoice generated. Generate manually?','wf-woocommerce-packing-list'),
			)
		);
		wp_localize_script($this->plugin_name, 'wf_pklist_params', $params);

	}

	/**
	 * Function to add Items to Orders Bulk action dropdown
	 *
	 * @since    2.5.0
	 */
	public function alter_bulk_action($actions)
	{
        return array_merge($actions,$this->bulk_actions);
	}
	

	/**
	 * Function to add print button in order list page action column
	 *
	 * @since    2.5.0
	 */
	public function add_checkout_fields($fields) 
	{
		$additional_options=Wf_Woocommerce_Packing_List::get_option('wf_invoice_additional_checkout_data_fields');
        if(is_array($additional_options) && count(array_filter($additional_options))>0)
        {
            foreach ($additional_options as $value)
            {
                $fields['billing']['billing_' . $value] = array(
                    'text' => 'text',
                    'label' => __(str_replace('_', ' ', $value), 'woocommerce'),
                    'placeholder' => _x('Enter ' . str_replace('_', ' ', $value), 'placeholder','woocommerce'),
                    'required' => false,
                    'class' => array('form-row-wide', 'align-left'),
                    'clear' => true
                );
            }
        }
		return $fields;
	}

	/**
	 * Function to add print button in order list page action column
	 *
	 * @since    2.5.0
	 */
	public function add_print_action_button($actions,$order)
	{
        $order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
        $wf_pklist_print_options=array(
            array(
                'name' => '',
                'action' => 'wf_pklist_print_document',
                'url' => sprintf('#%s',$order_id)
            ),
        );
        return array_merge($actions,$wf_pklist_print_options);
    } 

    /**
	 * Function to add email attachments to order email
	 *
	 * @since    2.5.0
	 */
	public function add_email_attachments($attachments, $status=null, $order=null)
	{
		if(is_object($order) && is_a($order,'WC_Order') && isset($status))
		{
            $order=( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
			$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
			$attachments=apply_filters('wt_email_attachments',$attachments,$order,$order_id,$status);
        }
		return $attachments;	
	}
   
    /**
	 * Function to add action buttons in order email
	 *
	 * @since    2.5.0
	 */
	public function add_email_print_actions($order)
	{
		if(is_object($order) && get_class($order)=='WC_Order')
		{
			$order=( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
			$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
			$html='';
			$html=apply_filters('wt_email_print_actions',$html,$order,$order_id);	
		}
	}

    /**
	 * Function to add action buttons in user dashboard order list page
	 *
	 * @since    2.5.0
	 */
	public function add_fontend_print_actions($order)
	{
		$order=( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
		$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
		$html='';
		$html=apply_filters('wt_frontend_print_actions',$html,$order,$order_id);	
	}


	public static function generate_print_button_data($order,$order_id,$action,$label,$icon_url,$is_show_prompt,$button_location="detail_page")
	{
		$url=wp_nonce_url(site_url('?print_packinglist=true&post='.($order_id).'&type='.$action),WF_PKLIST_PLUGIN_NAME);
		
		$href_attr='';
		$onclick='';
		$confirmation_clss='';
		if($is_show_prompt==1)
		{
			$confirmation_clss='wf_pklist_confirm_'.$action;
			$onclick='onclick=" return wf_Confirm_Notice_for_Manually_Creating_Invoicenumbers(\''.$url.'\','.$is_show_prompt.');"';
		}else
		{
			$href_attr=' href="'.$url.'"';
		}
		if($button_location=="detail_page")
        {
        ?>
		<tr>
			<td>
				<a class="button tips wf-packing-list-link" <?php echo $onclick;?> <?php echo $href_attr;?> target="_blank" data-tip="<?php echo strip_tags($label);?>" >
				<?php
				if($icon_url!="")
				{
				?>
					<img src="<?php echo $icon_url;?>" alt="<?php echo $label;?>" width="14"> 
				<?php
				}
				?>
				<?php echo $label;?>
				</a>
			</td>
		</tr>
		<?php
        }elseif($button_location=="list_page")
        {
        ?>
			<li>
				<a class="<?php echo $confirmation_clss;?>" data-id="<?php echo $order_id;?>" <?php echo $onclick;?> <?php echo $href_attr;?> target="_blank"><?php echo $label;?></a>
			</li>
		<?php
        }
	}

	/**
	 * Function to add action buttons in order list page
	 *
	 * @since    2.5.0
	 */
	public function add_print_actions($column)
	{
		global $post, $woocommerce, $the_order;
		if($column=='order_actions' || $column=='wc_actions')
		{
			$order = ( WC()->version < '2.7.0' ) ? new WC_Order($post->ID) : new wf_order($post->ID);
            $order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
			$html='';
			?>
			<div id="wf_pklist_print_document-<?php echo $order_id;?>" class="wf-pklist-print-tooltip-order-actions">				
				<div class="wf-pklist-print-tooltip-content">
                    <ul>
                    <?php
					$html=apply_filters('wt_print_actions',$html,$order,$order_id);
					?>
					</ul>
                </div>
                <div class="wf_arrow"></div>	
			</div>
			<?php
		}
		return $column;
	}

	/**
	 * Registers meta box and printing options
	 *
	 * @since    2.5.0
	 */
	public function add_meta_boxes()
	{
		add_meta_box('woocommerce-packinglist-box', __('Print Actions','wf-woocommerce-packing-list'), array($this,'create_metabox_content'),'shop_order', 'side', 'default');
	}

	/**
	 * Add plugin action links
	 *
	 * @param array $links links array
	 */
	public function plugin_action_links($links) 
	{
	   $links[] = '<a href="'.admin_url('admin.php?page='.WF_PKLIST_POST_TYPE).'">'.__('Settings','wf-woocommerce-packing-list').'</a>';
	   $links[] = '<a href="https://www.webtoffee.com/product/woocommerce-pdf-invoices-packing-slips/" target="_blank">'.__('Upgrade to premium','wf-woocommerce-packing-list').'</a>';
	   $links[] = '<a href="https://wordpress.org/support/plugin/print-invoices-packing-slip-labels-for-woocommerce" target="_blank">'.__('Support','wf-woocommerce-packing-list').'</a>';
	   $links[] = '<a href="https://wordpress.org/support/plugin/print-invoices-packing-slip-labels-for-woocommerce/reviews/?rate=5#new-post" target="_blank">' . __('Review','wf-woocommerce-packing-list') . '</a>';
	   return $links;
	}


	/**
	 * create content for metabox
	 *
	 * @since    2.5.0
	 */
	public function create_metabox_content()
	{
		global $post;
        $order = ( WC()->version < '2.7.0' ) ? new WC_Order($post->ID) : new wf_order($post->ID);
        $order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
        $html='';
		?>
		<table class="wf_invoice_metabox">
			<?php
			$html=apply_filters('wt_print_metabox',$html,$order,$order_id);
			?>
		</table>
		<?php
	}


	/**
	 * Registers menu options
	 * Hooked into admin_menu
	 *
	 * @since    2.5.0
	 */
	public function admin_menu()
	{
		$menus=array(
			array(
				'menu',
				__('General Settings','wf-woocommerce-packing-list'),
				__('Invoice/Packing','wf-woocommerce-packing-list'),
				'manage_woocommerce',
				WF_PKLIST_POST_TYPE,
				array($this,'admin_settings_page'),
				'dashicons-media-text',
				56
			),
			array(
				'submenu',
				WF_PKLIST_POST_TYPE,
				__('Document settings','wf-woocommerce-packing-list'),
				__('Document settings','wf-woocommerce-packing-list'),
				'manage_options',
				WF_PKLIST_POST_TYPE.'_document_settings_page',
				array($this,'admin_document_settings_page')
			)
		);

		$menus=apply_filters('wt_admin_menu',$menus);
		if(count($menus)>0)
		{
			add_submenu_page(WF_PKLIST_POST_TYPE,__('General Settings','wf-woocommerce-packing-list'),__('General Settings','wf-woocommerce-packing-list'), "manage_woocommerce",WF_PKLIST_POST_TYPE,array($this,'admin_settings_page'));
			foreach($menus as $menu)
			{
				if($menu[0]=='submenu')
				{
					add_submenu_page($menu[1],$menu[2],$menu[3],$menu[4],$menu[5],$menu[6]);
				}else
				{
					add_menu_page($menu[1],$menu[2],$menu[3],$menu[4],$menu[5],$menu[6],$menu[7]);	
				}
			}
		}

		if(function_exists('remove_submenu_page')){
			//remove_submenu_page(WF_PKLIST_POST_TYPE,WF_PKLIST_POST_TYPE);
		}
	}

	/**
	 * function to render printing window
	 *
	 */
    public function print_window() 
    {       
        $attachments = array();
        if(isset($_GET['print_packinglist'])) 
        {
        	//checkes user is logged in
        	if(!is_user_logged_in())
        	{
        		auth_redirect();
        	}
        	$not_allowed_msg=__('You are not allowed to view this page.','wf-woocommerce-packing-list');
        	$not_allowed_title=__('Access denied !!!.','wf-woocommerce-packing-list');

            $client = false;
            //	to check current user has rights to get invoice and packing list
            if(!isset($_GET['attaching_pdf']))
            {
	            $nonce=isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : ''; 
	            if(!(wp_verify_nonce($nonce,WF_PKLIST_PLUGIN_NAME)))
	            {
	                wp_die($not_allowed_msg,$not_allowed_title);
	            }else
	            {
	            	$orders = explode(',', $_GET['post']);
	            }
        	}else 
        	{
        		// to get the orders number
	            if(isset($_GET['email']) && isset($_GET['post']) && isset($_GET['user_print']))
	            {
	                $email_data_get =Wf_Woocommerce_Packing_List::wf_decode($_GET['email']);
	                $order_data_get =Wf_Woocommerce_Packing_List::wf_decode($_GET['post']);
	                $order_data = wc_get_order($order_data_get);
	                if(!$order_data)
	                {
	                	wp_die($not_allowed_msg,$not_allowed_title);
	                }
	                $logged_in_userid=get_current_user_id();
	                $order_user_id=((WC()->version < '2.7.0') ? $order_data->user_id : $order_data->get_user_id());
	                if($logged_in_userid!=$order_user_id) //the current order not belongs to the current logged in user
	                { 
	  	             	if(!current_user_can('manage_options')) //if he is not admin
	                	{
	                		wp_die($not_allowed_msg,$not_allowed_title);
	                	}
	                }

	                //checks the email parameters belongs to the given order
	                if($email_data_get === ((WC()->version < '2.7.0') ? $order_data->billing_email : $order_data->get_billing_email())) 
	                {
	                    $orders=explode(",",$order_data_get); //must be an array
	                }else
	                {
	                    wp_die($not_allowed_msg,$not_allowed_title);
	                }
	            }else
	            {
	            	wp_die($not_allowed_msg,$not_allowed_title);
	            }
        	}
            $orders=array_values(array_filter($orders));
            remove_action('wp_footer', 'wp_admin_bar_render', 1000);
            $action = $_GET['type'];
            //action for mudules to hook print function
            do_action('wt_print_doc',$orders,$action);
            exit();
        }
    }

    /* 
	* Ajax function for saving advanced settings
	*/
	public static function advanced_settings($module_base='',$module_id='')
	{
		$out=array('key'=>'','val'=>'','success'=>false,'msg'=>__('Error','wf-woocommerce-packing-list'));
		$warn_msg=__('All fields are mandatory','wf-woocommerce-packing-list');
		check_ajax_referer(WF_PKLIST_PLUGIN_NAME);
		if(isset($_POST['new_custom_click'])) 
		{
		    //additional fields for checkout
			if(isset($_POST['wf_new_custom_filed'])) 
	        {
	        	if(trim($_POST['wf_new_custom_filed'])!="")
	        	{
		        	$vl=Wf_Woocommerce_Packing_List::get_option('wf_invoice_additional_checkout_data_fields');
		        	$user_created=Wf_Woocommerce_Packing_List::get_option('wf_additional_checkout_data_fields');
		            $user_created=!is_array($user_created) ? array() : $user_created;
		            $user_created[] = $_POST['wf_new_custom_filed'];
		            Wf_Woocommerce_Packing_List::update_option('wf_additional_checkout_data_fields',$user_created);
		            
		            $user_selected_array = ($vl && $vl!= '') ? $vl : array();
		            $current_vl= str_replace(' ', '_', $_POST['wf_new_custom_filed']);       
		            if (!in_array($current_vl,$user_selected_array)) 
		            {
		                $user_selected_array[]=$current_vl;
		                Wf_Woocommerce_Packing_List::update_option('wf_invoice_additional_checkout_data_fields',$user_selected_array);
		                $out=array('key'=>$current_vl,'val'=>$current_vl,'success'=>true);
		            }
	        	}else
	        	{
	        		$out['msg']=$warn_msg;
	        	}
	        }

	        //additional fields on invoice,packingslip etc (This for modules)
	        if(isset($_POST['wf_old_custom_filed']) && isset($_POST['wf_old_custom_filed_meta']) && $module_base!='' && $module_id!="") 
	        {
	            if(trim($_POST['wf_old_custom_filed'])!="" && trim($_POST['wf_old_custom_filed_meta'])!="")
	        	{
		            $key=str_replace(' ', '_',$_POST['wf_old_custom_filed_meta']);
		            $val=$_POST['wf_old_custom_filed'];
		            $vl=Wf_Woocommerce_Packing_List::get_option('wf_additional_data_fields'); //this is plugin main setting so no need to specify module base
		            $data_array =$vl && $vl!="" ? $vl : array();
		            $data_array[$key] = $val;
		            Wf_Woocommerce_Packing_List::update_option('wf_additional_data_fields',$data_array);

		            $vl=Wf_Woocommerce_Packing_List::get_option('wf_'.$module_base.'_contactno_email',$module_id);
		            $data_slected_array =$vl!= '' ? $vl : array();

		            if(!in_array($key,$data_slected_array)) 
		            {
		                $data_slected_array[] = $key;
		                Wf_Woocommerce_Packing_List::update_option('wf_'.$module_base.'_contactno_email',$data_slected_array,$module_id);
		                $out=array('key'=>$key,'val'=>$val,'success'=>true);
		            }
		        }else
		        {
		        	$out['msg']=$warn_msg;
		        }
	        }

	        //Product Meta Fields (This for modules)
	        if(isset($_POST['wf_old_product_custom_filed']) && isset($_POST['wf_old_product_custom_filed_meta']) && $module_base!='' && $module_id!="") 
	        {
	            if(trim($_POST['wf_old_product_custom_filed'])!="" && trim($_POST['wf_old_product_custom_filed_meta'])!="")
	        	{
		            $key=str_replace(' ', '_',$_POST['wf_old_product_custom_filed_meta']);
		            $val=$_POST['wf_old_product_custom_filed'];
		            $vl=Wf_Woocommerce_Packing_List::get_option('wf_product_meta_fields');
		            $data_array = $vl && $vl!="" ? $vl : array();
		            $data_array[$key] = $val;
		            Wf_Woocommerce_Packing_List::update_option('wf_product_meta_fields',$data_array);

		            $vl=Wf_Woocommerce_Packing_List::get_option('wf_'.$module_base.'_product_meta_fields',$module_id);
		            $data_slected_array =$vl && $vl!="" ? $vl : array();

		            if (!in_array($key, $data_slected_array)) {
		                $data_slected_array[] = $key;
		                Wf_Woocommerce_Packing_List::update_option('wf_'.$module_base.'_product_meta_fields',$data_slected_array,$module_id);
		                $out=array('key'=>$key,'val'=>$val,'success'=>true);
		            }
		        }else
		        {
		        	$out['msg']=$warn_msg;
		        }
	        }
	    }
	    echo json_encode($out);
		exit();
	}

	private function dismiss_notice()
	{
		$allowd_items=array('wf_pklist_notice_dissmissed_250');
		if(isset($_GET['wf_pklist_notice_dismiss']) && trim($_GET['wf_pklist_notice_dismiss'])!="")
		{
			if(in_array($_GET['wf_pklist_notice_dismiss'],$allowd_items))
			{
				update_option($_GET['wf_pklist_notice_dismiss'],1);
			}
		}
	}

	/**
	 * Admin document settings page
	 *
	 * @since    2.5.0
	 */
	public function admin_document_settings_page()
	{
		//dismiss the notice if exists
		$this->dismiss_notice();

		//save settings
		if(isset($_POST['wf_settings_ajax_update']) && $_POST['wf_settings_ajax_update']=='document_settings')
		{
		    check_ajax_referer(WF_PKLIST_PLUGIN_NAME);
		    //hook for modules to save their settings
		    do_action('wt_pklist_document_save_settings');

		    echo '<div class="updated"><p><strong>' . __('Settings Updated.','wf-woocommerce-packing-list') . '</strong></p></div>';
	        if(!empty($_SERVER[ 'HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest')
	        {	            
	        	exit();
	        }
	    }
		include WF_PKLIST_PLUGIN_PATH.'admin/partials/wf-woocommerce-packing-list-admin-document-settings.php';
	}

	/**
	 * Admin settings page
	 *
	 * @since    2.5.0
	 */
	public function admin_settings_page()
	{
		//dismiss the notice if exists
		$this->dismiss_notice();

		$the_options=Wf_Woocommerce_Packing_List::get_settings();
		$no_image=Wf_Woocommerce_Packing_List::$no_image;
		$order_statuses = wc_get_order_statuses();
		$wf_generate_invoice_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus');
		wp_enqueue_media();
		wp_enqueue_script('wc-enhanced-select');
		wp_enqueue_style('woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css');

		if(isset($_POST['wf_update_module_status']))
		{
			// Check nonce:
	        check_admin_referer(WF_PKLIST_PLUGIN_NAME);	        
		    $wt_pklist_common_modules=get_option('wt_pklist_common_modules');
		    if($wt_pklist_common_modules===false)
		    {
		        $wt_pklist_common_modules=array();
		    }
		    if(isset($_POST['wt_pklist_common_modules']))
		    {
		        $wt_pklist_post=$_POST['wt_pklist_common_modules'];
		        foreach($wt_pklist_common_modules as $k=>$v)
		        {
		            if(isset($wt_pklist_post[$k]) && $wt_pklist_post[$k]==1)
		            {
		                $wt_pklist_common_modules[$k]=1;
		            }else
		            {
		                $wt_pklist_common_modules[$k]=0;
		            }
		        }
		    }else
		    {
		    	foreach($wt_pklist_common_modules as $k=>$v)
		        {
					$wt_pklist_common_modules[$k]=0;
		        }
		    }
		    update_option('wt_pklist_common_modules',$wt_pklist_common_modules);
		    wp_redirect($_SERVER['REQUEST_URI']); exit();
		}

		//save settings
		if(isset($_POST['wf_settings_ajax_update']) && $_POST['wf_settings_ajax_update']=='plugin_settings')
		{        
	        check_ajax_referer(WF_PKLIST_PLUGIN_NAME);
	        //multi select form fields array. (It will not return a $_POST val if it's value is empty so we need to set default value)
	        $default_val_needed_fields=array(
	        	'wf_invoice_additional_checkout_data_fields'=>array(),
	        	'woocommerce_wf_attach_shipping_label'=>array(),
	        );
	        foreach($the_options as $key => $value) 
	        {
	            if(isset($_POST[$key]))
	            {
	            	$the_options[$key]=$_POST[$key];
	            	if($key=='woocommerce_wf_packinglist_boxes')
	            	{
	            		$the_options[$key]=$this->validate_box_packing_field($_POST[$key]);
	            	}
	            }else
	            {
	            	if(array_key_exists($key,$default_val_needed_fields))
	            	{
	            		$the_options[$key]=$default_val_needed_fields[$key];
	            	}
	            }
	        }
	        Wf_Woocommerce_Packing_List::update_settings($the_options);
	        echo '<div class="updated"><p><strong>' . __('Settings Updated.','wf-woocommerce-packing-list') . '</strong></p></div>';
	        if(!empty($_SERVER[ 'HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest')
	        {	            
	        	exit();
	        }
	    }
	    // save settings
		include WF_PKLIST_PLUGIN_PATH.'admin/partials/wf-woocommerce-packing-list-admin-display.php';
	}

	public function validate_box_packing_field($value)
	{           
        $new_boxes = array();
        foreach ($value as $key => $value) {
            if ($value['length'] != '') {
                $value['enabled'] = isset($value['enabled']) ? true : false;
                $new_boxes[] = $value;
            }
        }
        return $new_boxes;
    }
	public static function generate_form_field($args,$base='')
	{		
		if(is_array($args))
		{
			foreach ($args as $key => $value)
			{
				$type=(isset($value['type']) ? $value['type'] : 'text');
				$field_name=isset($value['field_name']) ? $value['field_name'] : $value['option_name'];

				$form_toggler_p_class="";
				$form_toggler_register="";
				$form_toggler_child="";
				if(isset($value['form_toggler']))
				{
					if($value['form_toggler']['type']=='parent')
					{
						$form_toggler_p_class="wf_form_toggle";
						$form_toggler_register=' wf_frm_tgl-target="'.$value['form_toggler']['target'].'"';
					}
					elseif($value['form_toggler']['type']=='child')
					{
						$form_toggler_child=' wf_frm_tgl-id="'.$value['form_toggler']['id'].'" wf_frm_tgl-val="'.$value['form_toggler']['val'].'" '.(isset($value['form_toggler']['chk']) ? 'wf_frm_tgl-chk="'.$value['form_toggler']['chk'].'"' : '').(isset($value['form_toggler']['lvl']) ? ' wf_frm_tgl-lvl="'.$value['form_toggler']['lvl'].'"' : '');	
					}else
					{
						$form_toggler_child=' wf_frm_tgl-id="'.$value['form_toggler']['id'].'" wf_frm_tgl-val="'.$value['form_toggler']['val'].'" '.(isset($value['form_toggler']['chk']) ? 'wf_frm_tgl-chk="'.$value['form_toggler']['chk'].'"' : '').(isset($value['form_toggler']['lvl']) ? ' wf_frm_tgl-lvl="'.$value['form_toggler']['lvl'].'"' : '');	
						$form_toggler_p_class="wf_form_toggle";
						$form_toggler_register=' wf_frm_tgl-target="'.$value['form_toggler']['target'].'"';				
					}
					
				}
				$fld_attr=(isset($value['attr']) ? $value['attr'] : '');
				$field_only=(isset($value['field_only']) ? $value['field_only'] : false);
				if($field_only===false)
				{
		?>
		<tr valign="top" <?php echo $form_toggler_child; ?>>
	        <th scope="row" >
	        	<label for="<?php echo $field_name;?>"><?php echo isset($value['label']) ? $value['label'] : ''; ?></label></th>
	        <td>
	        	<?php
	        	}
	        	$option_name=$value['option_name'];
	        	$vl=Wf_Woocommerce_Packing_List::get_option($option_name,$base);
	        	$vl=is_string($vl) ? stripslashes($vl) : $vl;
	        	if($type=='text')
				{
	        	?>
	            	<input type="text" <?php echo $fld_attr;?> name="<?php echo $field_name;?>" value="<?php echo $vl;?>" />
	            <?php
	        	}
	        	if($type=='number')
				{
				?>
	            	<input type="number" <?php echo $fld_attr;?> name="<?php echo $field_name;?>" value="<?php echo $vl;?>" />
	            <?php
				}
	        	elseif($type=='textarea')
				{
				?>
	            <textarea <?php echo $fld_attr;?> name="<?php echo $field_name;?>"><?php echo $vl;?></textarea>
	            <?php
				}elseif($type=='order_st_multiselect') //order status multi select
				{
					$order_statuses=isset($value['order_statuses']) ? $value['order_statuses'] : array();
					$field_vl=isset($value['field_vl']) ? $value['field_vl'] : array();
				?>
				<select class="wc-enhanced-select" id='<?php echo $field_name;?>_st' data-placeholder='<?php _e('Choose Order Status','wf-woocommerce-packing-list');?>' name="<?php echo $field_name;?>[]" multiple="multiple" <?php echo $fld_attr;?>>
                    <?php
                    $Pdf_invoice=$vl ? $vl : array();
                    foreach($field_vl as $inv_key => $inv_value) 
                    {
            			echo "<option value=$inv_value".(in_array($inv_value, $Pdf_invoice) ? ' selected="selected"' : '').">$order_statuses[$inv_value]</option>";
                        
                    }
                    ?>
                </select>
				<?php
				}elseif($type=='checkbox') //checkbox
				{
					$field_vl=isset($value['field_vl']) ? $value['field_vl'] : "1";
				?>
					<input class="<?php echo $form_toggler_p_class;?>" type="checkbox" value="<?php echo $field_vl;?>" id="<?php echo $option_name;?>" name="<?php echo $field_name;?>" <?php echo ($field_vl==$vl ? ' checked="checked"' : '') ?> <?php echo $form_toggler_register;?> <?php echo $fld_attr;?>>
					<?php
				}
				elseif($type=='radio') //radio button
				{
					$radio_fields=isset($value['radio_fields']) ? $value['radio_fields'] : array();
					foreach ($radio_fields as $rad_vl=>$rad_label) 
					{
					?>
					<input type="radio" id="<?php echo $option_name.'_'.$rad_vl;?>" name="<?php echo $field_name;?>" class="<?php echo $form_toggler_p_class;?>" <?php echo $form_toggler_register;?> value="<?php echo $rad_vl;?>" <?php echo ($vl==$rad_vl) ? ' checked="checked"' : ''; ?> <?php echo $fld_attr;?> /> <?php echo $rad_label; ?>
					&nbsp;&nbsp;
					<?php
					}
					
				}elseif($type=='uploader') //uploader
				{
					?>
					<div class="wf_file_attacher_dv">
			            <input id="<?php echo $field_name; ?>"  type="text" name="<?php echo $field_name; ?>" value="<?php echo $vl; ?>" <?php echo $fld_attr;?>/>
						
						<input type="button" name="upload_image" class="wf_button button button-primary wf_file_attacher" wf_file_attacher_target="#<?php echo $field_name; ?>" value="<?php _e('Upload','wf-woocommerce-packing-list'); ?>" />
					</div>
					<img class="wf_image_preview_small" src="<?php echo $vl ? $vl : Wf_Woocommerce_Packing_List::$no_image; ?>" />
					<?php
				}elseif($type=='select') //select
				{
					$select_fields=isset($value['select_fields']) ? $value['select_fields'] : array();
					?>
					<select name="<?php echo $field_name;?>" id="<?php echo $field_name;?>" class="<?php echo $form_toggler_p_class;?>" <?php echo $form_toggler_register;?> <?php echo $fld_attr;?>>
					<?php
					foreach ($select_fields as $sel_vl=>$sel_label) 
					{
					?>
						<option value="<?php echo $sel_vl;?>" <?php echo ($vl==$sel_vl) ? ' selected="selected"' : ''; ?>><?php echo $sel_label; ?></option>
					<?php
					}
					?>
					</select>
					<?php
				}elseif($type=='additional_fields') //additional fields
				{
					$module_base=isset($value['module_base']) ? $value['module_base'] : '';
					
					$fields=array();
		            $add_data_flds=array_flip(Wf_Woocommerce_Packing_List::$default_additional_data_fields); 
		            $user_created=Wf_Woocommerce_Packing_List::get_option('wf_additional_data_fields');
		            if(is_array($user_created))  //user created
		            {
		                $fields=array_merge($add_data_flds,$user_created);
		            }else
		            {
		                $fields=$add_data_flds; //default
		            }

		            //additional checkout fields
	                $additional_checkout=Wf_Woocommerce_Packing_List::get_option('wf_additional_checkout_data_fields');
	            	$additional_checkout=array_combine($additional_checkout,$additional_checkout); //creating an array with key and val are same
	            	$fields=array_merge($fields,$additional_checkout);

		            $user_selected_arr=$vl && is_array($vl) ? $vl : array();
					?>
					<div class="wf_select_multi">
			            <select class="wc-enhanced-select" name="wf_<?php echo $module_base;?>_contactno_email[]" multiple="multiple">
			            <?php
			            
			            foreach ($fields as $id => $name) 
			            { 
			                ?>
			                <option value="<?php echo $id;?>" <?php echo in_array($id,$user_selected_arr) ? 'selected' : '';?>>
			                    <?php echo $name;?>
			                </option>
			                <?php
			            }
			            ?>						 
			            </select>
			            <br>
			            <button type="button" class="button button-secondary" data-wf_popover="1" data-title="<?php _e('Checkout Meta Key Fetcher','wf-woocommerce-packing-list'); ?>" data-ajax-action="wf_<?php echo $module_base;?>_advanced_settings" data-content="<?php _e('Field Name','wf-woocommerce-packing-list'); ?>: <input type='text' name='wf_old_custom_filed' style='width:100%'/> <br> <?php _e('Meta Key','wf-woocommerce-packing-list'); ?>: <input type='text' name='wf_old_custom_filed_meta' style='width:100%'/> " style="margin-top:5px; float: right;">
			                <?php _e('Add Existing Order Meta Field','wf-woocommerce-packing-list'); ?>                       
			             </button>
			            <?php
			        	if(isset($value['help_text']))
						{
			            ?>
			            <span class="wf_form_help" style="display:inline;"><?php echo $value['help_text']; ?></span>
			            <?php
			            	unset($value['help_text']);
			        	}
			        	?>
			        </div>
					<?php
				}elseif($type=='product_meta') //Product Meta
				{
					?>
					<div class="wf_select_multi">
			            <select class="wc-enhanced-select" name="wf_<?php echo $module_base;?>_product_meta_fields[]" multiple="multiple">
			                <?php
			                $user_selected_arr=$vl && is_array($vl) ? $vl : array();
			                $wf_product_meta_fields=Wf_Woocommerce_Packing_List::get_option('wf_product_meta_fields');
			                if (is_array($wf_product_meta_fields))
			                {
			                    foreach ($wf_product_meta_fields as $key => $val){
			                        echo '<option value="'.$key.'"'.(in_array($key,$user_selected_arr) ? ' selected="selected"' : '').'>' . $val . '</option>';
			                    }
			                }
			                ?>						 
			            </select>
			            <br>
			            <button type="button" class="button button-secondary" data-wf_popover="1" data-title="Product Meta Key Fetcher" data-ajax-action="wf_<?php echo $module_base;?>_advanced_settings" data-content="<?php _e('Field Name','wf-woocommerce-packing-list'); ?>: <input type='text' name='wf_old_product_custom_filed' style='width:100%'/> <br> <?php _e('Meta Key','wf-woocommerce-packing-list'); ?>: <input type='text' name='wf_old_product_custom_filed_meta' style='width:100%'/> " style="margin-top:5px; float: right;"><?php _e('Add Product Meta','wf-woocommerce-packing-list'); ?></button>
			        	<?php
			        	if(isset($value['help_text']))
						{
			            ?>
			            <span class="wf_form_help" style="display:inline;"><?php echo $value['help_text']; ?></span>
			            <?php
			            	unset($value['help_text']);
			        	}
			        	?>
			        </div>
					<?php
				}
				if(isset($value['help_text']))
				{
	            ?>
	            <span class="wf_form_help"><?php echo $value['help_text']; ?></span>
	            <?php
	        	}
	        	if($field_only===false)
				{
	        	?>
	        </td>
	        <td></td>
	    </tr>
	    <?php
	    		}
	    	}
		}
	}

	/**
	 * Envelope settings tab content with tab div.
	 * relative path is not acceptable in view file
	 */
	public static function envelope_settings_tabcontent($target_id,$view_file="",$html="",$variables=array(),$need_submit_btn=0)
	{
		extract($variables);
	?>
		<div class="wf-tab-content" data-id="<?php echo $target_id;?>">
			<?php
			if($view_file!="" && file_exists($view_file))
			{
				include_once $view_file;
			}else
			{
				echo $html;
			}
			?>
			<?php 
			if($need_submit_btn==1)
			{
				include plugin_dir_path(WF_PKLIST_PLUGIN_FILENAME)."admin/views/admin-settings-save-button.php";
			}
			?>
		</div>
	<?php
	}

	/**
	 * Envelope settings subtab content with subtab div.
	 * relative path is not acceptable in view file
	 */
	public static function envelope_settings_subtabcontent($target_id,$view_file="",$html="",$variables=array(),$need_submit_btn=0)
	{
		extract($variables);
	?>
		<div class="wf_sub_tab_content" data-id="<?php echo $target_id;?>">
			<?php
			if($view_file!="" && file_exists($view_file))
			{
				include_once $view_file;
			}else
			{
				echo $html;
			}
			?>
			<?php 
			if($need_submit_btn==1)
			{
				include plugin_dir_path(WF_PKLIST_PLUGIN_FILENAME)."admin/views/admin-settings-save-button.php";
			}
			?>
		</div>
	<?php
	}

	/**
	 Registers modules: public+admin	 
	 */
	public function admin_modules()
	{ 
		$wt_pklist_admin_modules=get_option('wt_pklist_admin_modules');
		if($wt_pklist_admin_modules===false)
		{
			$wt_pklist_admin_modules=array();
		}
		foreach (self::$modules as $module) //loop through module list and include its file
		{
			$is_active=1;
			if(isset($wt_pklist_admin_modules[$module]))
			{
				$is_active=$wt_pklist_admin_modules[$module]; //checking module status
			}else
			{
				$wt_pklist_admin_modules[$module]=1; //default status is active
			}
			$module_file=plugin_dir_path( __FILE__ )."modules/$module/$module.php";
			if(file_exists($module_file) && $is_active==1)
			{
				self::$existing_modules[]=$module; //this is for module_exits checking
				require_once $module_file;
			}else
			{
				$wt_pklist_admin_modules[$module]=0;	
			}
		}
		$out=array();
		foreach($wt_pklist_admin_modules as $k=>$m)
		{
			if(in_array($k,self::$modules))
			{
				$out[$k]=$m;
			}
		}
		update_option('wt_pklist_admin_modules',$out);
	}

	public static function module_exists($module)
	{
		return in_array($module,self::$existing_modules);
	}
}
