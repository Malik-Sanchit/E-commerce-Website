<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<ul class="wf_sub_tab">
    <li style="border-left:none; padding-left: 0px;" data-target="general"><a><?php _e('General','wf-woocommerce-packing-list'); ?></a></li>
    <?php
    $title_arr=array();
    $title_arr=apply_filters("wf_pklist_module_settings_tabhead",$title_arr);
    if($title_arr)
    {
        foreach($title_arr as $k=>$v)
        {
            if(is_array($v))
            {
                $v=(isset($v[2]) ? $v[2] : '').$v[0].' '.(isset($v[1]) ? $v[1] : '');
            }
        ?>
            <li data-target="<?php echo $k;?>">
                <a><?php echo $v; ?></a>
            </li>
        <?php
        }   
    }
    ?>
    <li data-target="invoice-number"><a><?php _e('Invoice Number','wf-woocommerce-packing-list');?></a></li>
</ul>
<div class="wf_sub_tab_container">
    <?php include plugin_dir_path( __FILE__ ).'general.php'; ?>
    <?php include plugin_dir_path( __FILE__ ).'invoice-number.php'; ?>
    <?php do_action('wf_pklist_module_out_settings_form',array(
        'module_id'=>$module_base
    ));  ?>
</div>