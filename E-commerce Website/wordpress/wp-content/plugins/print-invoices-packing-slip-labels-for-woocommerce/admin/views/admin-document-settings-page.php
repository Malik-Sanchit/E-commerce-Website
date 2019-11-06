<?php
if (!defined('ABSPATH')) {
	exit;
}
?>
<div class="wf-tab-content" data-id="wf-other-documents">
<form method="post" action="<?php echo esc_url($_SERVER["REQUEST_URI"]);?>" class="wf_settings_form">
	<input type="hidden" value="document_settings" class="wf_update_action" />
    <?php
    // Set nonce:
    if (function_exists('wp_nonce_field'))
    {
        wp_nonce_field(WF_PKLIST_PLUGIN_NAME);
    }
	$html='';
	$html=apply_filters('wf_pklist_document_setting_fields',$html);
	echo $html;
	?>
	<?php 
	include "admin-settings-save-button.php";
	?>
</form>
</div>