<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<style type="text/css">
.wf_filters_doc{ border:solid 1px #ccc; }
.wf_filters_doc td{ padding:5px 5px; }
.wf_filters_doc td p{ margin:0px; padding:0px; }
.wf_filter_doc_params{ color:#b46b6b; }
</style>
<div class="wf-tab-content" data-id="<?php echo $target_id;?>">
	<ul class="wf_sub_tab">
		<li style="border-left:none; padding-left: 0px;" data-target="help-links"><a><?php _e('Help Links','wf-woocommerce-packing-list'); ?></a></li>
		<li data-target="filters"><a><?php _e('Filters','wf-woocommerce-packing-list');?></a></li>
	</ul>
	<div class="wf_sub_tab_container">		
		<div class="wf_sub_tab_content" data-id="help-links" style="display:block;">
			<h3><?php _e('Help Links','wf-woocommerce-packing-list'); ?></h3>
			<ul class="wf-help-links">
			    <li>
			        <img src="<?php echo WF_PKLIST_PLUGIN_URL;?>assets/images/documentation.png">
			        <h3><?php _e('Documentation','wf-woocommerce-packing-list'); ?></h3>
			        <p><?php _e('Refer to our documentation to set and get started','wf-woocommerce-packing-list'); ?></p>
			        <a target="_blank" href="https://www.webtoffee.com/category/documentation/" class="button button-primary">
			            <?php _e('Documentation','wf-woocommerce-packing-list'); ?>        
			        </a>
			    </li>
			    <li>
			        <img src="<?php echo WF_PKLIST_PLUGIN_URL;?>assets/images/support.png">
			        <h3><?php _e('Help and Support','wf-woocommerce-packing-list'); ?></h3>
			        <p><?php _e('We would love to help you on any queries or issues.','wf-woocommerce-packing-list'); ?></p>
			        <a target="_blank" href="https://www.webtoffee.com/support/" class="button button-primary">
			            <?php _e('Contact Us','wf-woocommerce-packing-list'); ?>
			        </a>
			    </li>               
			</ul>
		</div>
		<div class="wf_sub_tab_content" data-id="filters">
			<?php
			include WF_PKLIST_PLUGIN_PATH.'/admin/data/data.filters-help.php';
			?>
			<h3><?php _e('Filters','wf-woocommerce-packing-list'); ?></h3>
			<p>
				<?php _e("Some useful `filters` to extend plugin's functionality",'wf-woocommerce-packing-list');?>
			</p>
			<table class="wp-list-table fixed striped wf_filters_doc">
				<?php
				if(isset($wf_filters_help_doc) && is_array($wf_filters_help_doc))
				{
					foreach($wf_filters_help_doc as $key => $value) 
					{
						?>
						<tr>
							<td style="font-weight:bold;"><?php echo $key;?></td>
							<td>
								<?php
								if(isset($value['description']) && trim($value['description'])!="")
								{
								?>
								<p>
									<?php _e($value['description'],'wf-woocommerce-packing-list');?>
								</p>
								<?php
								}
								if(isset($value['params']) && trim($value['params'])!="")
								{
								?>
								<p class="wf_filter_doc_params">
									<?php echo $value['params'];?>
								</p>
								<?php
								}
								?>
							</td>
						</tr>
						<?php
					}
				}
				?>
			</table>
		</div>
	</div>
</div>