<?php
/**
 * Uninstall Feedback
 *
 * @link       
 * @since 2.5.0     
 *
 * @package  Wf_Woocommerce_Packing_List  
 */
if (!defined('ABSPATH')) {
    exit;
}
class Wf_Woocommerce_Packing_List_Uninstall_Feedback
{
	protected $api_url='http://feedback.webtoffee.com/wp-json/wfinvoice/v1/uninstall';
    protected $current_version=WF_PKLIST_VERSION;
    protected $auth_key='wfinvoice_uninstall_1234#';
    protected $plugin_id='printinvoice';
    public function __construct()
	{
        add_action('admin_footer', array($this,'deactivate_scripts'));
        add_action('wp_ajax_wfinvoice_submit_uninstall_reason', array($this,"send_uninstall_reason"));
        add_filter('plugin_action_links_'.plugin_basename(WF_PKLIST_PLUGIN_FILENAME),array($this,'plugin_action_links'));
    }
    public function plugin_action_links($links) 
	{
		if(array_key_exists('deactivate',$links))
		{
            $links['deactivate']=str_replace('<a', '<a class="wfinvoice-deactivate-link"',$links['deactivate']);
        }
		return $links;
	}
    private function get_uninstall_reasons()
    {

        $reasons = array(
            array(
                'id' => 'could-not-understand',
                'text' => __('I couldn\'t understand how to make it work', 'wf-woocommerce-packing-list'),
                'type' => 'textarea',
                'placeholder' => __('Would you like us to assist you?', 'wf-woocommerce-packing-list')
            ),
            array(
                'id' => 'found-better-plugin',
                'text' => __('I found a better plugin', 'wf-woocommerce-packing-list'),
                'type' => 'text',
                'placeholder' => __('Which plugin?', 'wf-woocommerce-packing-list')
            ),
            array(
                'id' => 'not-have-that-feature',
                'text' => __('The plugin is great, but I need specific feature that you don\'t support', 'wf-woocommerce-packing-list'),
                'type' => 'textarea',
                'placeholder' => __('Could you tell us more about that feature?', 'wf-woocommerce-packing-list')
            ),
            array(
                'id' => 'is-not-working',
                'text' => __('The plugin is not working', 'wf-woocommerce-packing-list'),
                'type' => 'textarea',
                'placeholder' => __('Could you tell us a bit more whats not working?', 'wf-woocommerce-packing-list')
            ),
            array(
                'id' => 'looking-for-other',
                'text' => __('It\'s not what I was looking for', 'wf-woocommerce-packing-list'),
                'type' => 'textarea',
                'placeholder' => 'Could you tell us a bit more?'
            ),
            array(
                'id' => 'did-not-work-as-expected',
                'text' => __('The plugin didn\'t work as expected', 'wf-woocommerce-packing-list'),
                'type' => 'textarea',
                'placeholder' => __('What did you expect?', 'wf-woocommerce-packing-list')
            ),
            array(
                'id' => 'other',
                'text' => __('Other', 'wf-woocommerce-packing-list'),
                'type' => 'textarea',
                'placeholder' => __('Could you tell us a bit more?', 'wf-woocommerce-packing-list')
            ),
        );

        return $reasons;
    }

    public function deactivate_scripts()
    {
        global $pagenow;
        if('plugins.php' != $pagenow)
        {
            return;
        }
        $reasons = $this->get_uninstall_reasons();
        ?>
        <div class="wfinvoice-modal" id="wfinvoice-wfinvoice-modal">
            <div class="wfinvoice-modal-wrap">
                <div class="wfinvoice-modal-header">
                    <h3><?php _e('If you have a moment, please let us know why you are deactivating:', 'wf-woocommerce-packing-list'); ?></h3>
                </div>
                <div class="wfinvoice-modal-body">
                    <ul class="reasons"><?php foreach ($reasons as $reason) { ?>
                            <li data-type="<?php echo esc_attr($reason['type']); ?>" data-placeholder="<?php echo esc_attr($reason['placeholder']); ?>">
                                <label><input type="radio" name="selected-reason" value="<?php echo $reason['id']; ?>"><?php echo $reason['text']; ?></label>
                            </li><?php } ?>
                    </ul>
                </div>
                <div class="wfinvoice-modal-footer">
                    <a href="#" class="dont-bother-me"><?php _e('I rather wouldn\'t say', 'wf-woocommerce-packing-list'); ?></a>
                    <button class="button-primary wfinvoice-model-submit"><?php _e('Submit & Deactivate', 'wf-woocommerce-packing-list'); ?></button>
                    <button class="button-secondary wfinvoice-model-cancel"><?php _e('Cancel', 'wf-woocommerce-packing-list'); ?></button>
                </div>
            </div>
        </div>
        <style type="text/css">
            .wfinvoice-modal {
                position: fixed;
                z-index: 99999;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                background: rgba(0,0,0,0.5);
                display: none;
            }
            .wfinvoice-modal.modal-active {display: block;}
            .wfinvoice-modal-wrap {
                width: 50%;
                position: relative;
                margin: 10% auto;
                background: #fff;
            }
            .wfinvoice-modal-header {
                border-bottom: 1px solid #eee;
                padding: 8px 20px;
            }
            .wfinvoice-modal-header h3 {
                line-height: 150%;
                margin: 0;
            }
            .wfinvoice-modal-body {padding: 5px 20px 20px 20px;}
            .wfinvoice-modal-body .input-text,.wfinvoice-modal-body textarea {width:75%;}
            .wfinvoice-modal-body .reason-input {
                margin-top: 5px;
                margin-left: 20px;
            }
            .wfinvoice-modal-footer {
                border-top: 1px solid #eee;
                padding: 12px 20px;
                text-align: right;
            }
        </style>
        <script type="text/javascript">
            (function ($) {
                $(function () {
                    var modal = $('#wfinvoice-wfinvoice-modal');
                    var deactivateLink = '';
                    $('#the-list').on('click', 'a.wfinvoice-deactivate-link', function (e) {
                        e.preventDefault();
                        modal.addClass('modal-active');
                        deactivateLink = $(this).attr('href');
                        modal.find('a.dont-bother-me').attr('href', deactivateLink).css('float', 'left');
                    });
                    modal.on('click', 'button.wfinvoice-model-cancel', function (e) {
                        e.preventDefault();
                        modal.removeClass('modal-active');
                    });
                    modal.on('click', 'input[type="radio"]', function () {
                        var parent = $(this).parents('li:first');
                        modal.find('.reason-input').remove();
                        var inputType = parent.data('type'),
                                inputPlaceholder = parent.data('placeholder'),
                                reasonInputHtml = '<div class="reason-input">' + (('text' === inputType) ? '<input type="text" class="input-text" size="40" />' : '<textarea rows="5" cols="45"></textarea>') + '</div>';

                        if (inputType !== '') {
                            parent.append($(reasonInputHtml));
                            parent.find('input, textarea').attr('placeholder', inputPlaceholder).focus();
                        }
                    });

                    modal.on('click', 'button.wfinvoice-model-submit', function (e) {
                        e.preventDefault();
                        var button = $(this);
                        if (button.hasClass('disabled')) {
                            return;
                        }
                        var $radio = $('input[type="radio"]:checked', modal);
                        var $selected_reason = $radio.parents('li:first'),
                                $input = $selected_reason.find('textarea, input[type="text"]');

                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'wfinvoice_submit_uninstall_reason',
                                reason_id: (0 === $radio.length) ? 'none' : $radio.val(),
                                reason_info: (0 !== $input.length) ? $input.val().trim() : ''
                            },
                            beforeSend: function () {
                                button.addClass('disabled');
                                button.text('Processing...');
                            },
                            complete: function () {
                                window.location.href = deactivateLink;
                            }
                        });
                    });
                });
            }(jQuery));
        </script>
        <?php
    }

    public function send_uninstall_reason()
    {
        global $wpdb;
        if (!isset($_POST['reason_id'])) {
            wp_send_json_error();
        }
        //$current_user = wp_get_current_user();
        $data = array(
            'reason_id' => sanitize_text_field($_POST['reason_id']),
            'plugin' =>$this->plugin_id,
            'auth' =>$this->auth_key,
            'date' => gmdate("M d, Y h:i:s A"),
            'url' => '',
            'user_email' => '',
            'reason_info' => isset($_REQUEST['reason_info']) ? trim(stripslashes($_REQUEST['reason_info'])) : '',
            'software' => $_SERVER['SERVER_SOFTWARE'],
            'php_version' => phpversion(),
            'mysql_version' => $wpdb->db_version(),
            'wp_version' => get_bloginfo('version'),
            'wc_version' => (!defined('WC_VERSION')) ? '' : WC_VERSION,
            'locale' => get_locale(),
            'multisite' => is_multisite() ? 'Yes' : 'No',
            'wfinvoice_version' =>$this->current_version,
        );
        // Write an action/hook here in webtoffe to recieve the data
        $resp = wp_remote_post($this->api_url, array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => false,
            'body' => $data,
            'cookies' => array()
                )
        );
        wp_send_json_success();
    }
}
new Wf_Woocommerce_Packing_List_Uninstall_Feedback();