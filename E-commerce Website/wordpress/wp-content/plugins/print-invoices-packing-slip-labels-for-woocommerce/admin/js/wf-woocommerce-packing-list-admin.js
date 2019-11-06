(function( $ ) {
	'use strict';

	$(function() {

		$('.wf_pklist_notice').click(function(){
			var ntce_vl=$(this).attr('data-pklist-notice-option');
			$.ajax({
				type:'get',
				data:{'wf_pklist_notice_dismiss':ntce_vl},
			});
		});

		/* bulk action */
		$("#doaction, #doaction2").click(function(e){
			var actionselected=$(this).attr("id").substr(2);
	        var action = $('select[name="' + actionselected + '"]').val();
	        if($.inArray(action,wf_pklist_params.bulk_actions) !== -1 ) 
	        {
	        	e.preventDefault();
	        	var checked_orders=$('tbody th.check-column input[type="checkbox"]:checked');
	        	if(checked_orders.length==0)
	        	{
	        		alert(wf_pklist_params.msgs.select_orders_first);
	        		return false;
	        	}else
	        	{
	        		var order_id_arr=new Array();
	        		checked_orders.each(function(){
	        			order_id_arr.push($(this).val());
	        		});
	        		var confirmation_needed_ord=$('.wf_pklist_confirm_'+action);/* any orders needed confirmation */
	        		var is_confirmation_needed=false;
	        		if(confirmation_needed_ord.length>0)
	        		{
	        			confirmation_needed_ord.each(function(){ /* check the confirmation needed orders are checked */
	        				var id=$(this).attr('data-id');
	        				if($.inArray(id,order_id_arr)!==-1)
	        				{
	        					is_confirmation_needed=true;
	        				}
	        			});
	        		}
	        		var action_url=wf_pklist_params.print_action_url+'&type='+action+'&post='+(order_id_arr.join(','))+'&_wpnonce='+wf_pklist_params.nonces.wf_packlist;
	        		if(is_confirmation_needed)
	        		{
	        			if(confirm(wf_pklist_params.msgs.invoice_not_gen_bulk))
                  		{
                  			window.open(action_url,'_blank');
	                        setTimeout(function(){
	                            window.location.reload(true);  
	                           },1000);
                  		}
	        		}else
	        		{
	        			window.open(action_url,'_blank');
	        		}
	        	}
	        }

		});

		/* Box packing - --------------------- */
		$('.woocommerce_wf_packinglist_boxes .insert').click(function () {
        var tbody = $('.woocommerce_wf_packinglist_boxes').find('tbody');
        var size = tbody.find('tr').size();
        var dimension_unit = $('#dimension_unit').val();
        var weight_unit = $('#weight_unit').val();
        var code = '<tr class="new">'
			+'<th class="check-column" style="padding: 0px; vertical-align: middle;"><input type="checkbox" /></th>'
	                +'<td><input type="text" name="woocommerce_wf_packinglist_boxes[' + size + '][name]" />'  + '</td>'
				+'<td><input type="text" name="woocommerce_wf_packinglist_boxes[' + size + '][length]" /> ' + dimension_unit + '</td>'
				+'<td><input type="text" name="woocommerce_wf_packinglist_boxes[' + size + '][width]" /> ' + dimension_unit + '</td>'
				+'<td><input type="text" name="woocommerce_wf_packinglist_boxes[' + size + '][height]" /> ' + dimension_unit + '</td>'
				+'<td><input type="text" name="woocommerce_wf_packinglist_boxes[' + size + '][box_weight]" /> ' + weight_unit + '</td>'
				+'<td><input type="text" name="woocommerce_wf_packinglist_boxes[' + size + '][max_weight]" /> ' + weight_unit + '</td>'
				+'<td><input type="checkbox" name="woocommerce_wf_packinglist_boxes[' + size + '][enabled]" /></td>'
			+'</tr>';
	        tbody.append(code);
	        return false;
	    });
	    
	    $('.woocommerce_wf_packinglist_boxes .remove').click(function () {
	        var tbody = $('.woocommerce_wf_packinglist_boxes').find('tbody');
	        tbody.find('.check-column input:checked').each(function () {
	            $(this).closest('tr').hide().find('input').val('');
	        });
	        return false;
	    });
	    /* Box packing - --------------------- */


		$('#reset_invoice_button').click(function () {
	        $('[name=woocommerce_wf_invoice_start_number]').prop("readonly", false).css({'background':'#fff','width':'100%'});
	        $('.wf_current_invoice_number').val($('[name=woocommerce_wf_invoice_start_number]').val());
	        $(this).hide();
	    });
	    $('[name=woocommerce_wf_invoice_start_number]').change(function(){
	    	$('.wf_current_invoice_number').val($(this).val());
	    });

	    /* hide tooltip menu on body click */
	    $('body').on('click',function(e){
	    	if($(e.target).hasClass('wf_pklist_print_document')===false)
	    	{
	    		$('.wf-pklist-print-tooltip-order-actions').hide();
	    	}
	    });

	    /* tooltip action buttons in order listing page */
	    $('.wf_pklist_print_document').click(function(e){
	    	e.preventDefault();
	    	$('.wf-pklist-print-tooltip-order-actions').hide();
	    	var trgt=$(this).attr('href')
	    	trgt=trgt.replace('#','-');
	    	var trgt_elm=$('#wf_pklist_print_document'+trgt);
	    	if(trgt_elm.length>0)
	    	{
	    		var pos=$(this).position();
	    		var post=pos.top;
	    		var posl=pos.left;
	    		var w=(trgt_elm.width()+2)*-1;
	    		trgt_elm.css({'left':posl,'top':post,'margin-left':w+'px'}).show();
	    	}
	    });

	    var wf_tab_view=
	    {
	    	Set:function()
	    	{
	    		var wf_nav_tab=$('.wf-tab-head .nav-tab');
			 	if(wf_nav_tab.length>0)
			 	{
				 	wf_nav_tab.click(function(){
				 		var wf_tab_hash=$(this).attr('href');
				 		wf_nav_tab.removeClass('nav-tab-active');
				 		$(this).addClass('nav-tab-active');
				 		wf_tab_hash=wf_tab_hash.charAt(0)=='#' ? wf_tab_hash.substring(1) : wf_tab_hash;
				 		var wf_tab_elm=$('div[data-id="'+wf_tab_hash+'"]');
				 		$('.wf-tab-content').hide();
				 		if(wf_tab_elm.length>0 && wf_tab_elm.is(':hidden'))
				 		{	 			
				 			wf_tab_elm.fadeIn();
				 		}
				 	});
				 	$(window).on('hashchange', function (e) {
					    var location_hash=window.location.hash;
					 	if(location_hash!="")
					 	{
					    	wf_tab_view.showTab(location_hash);
					    }
					}).trigger('hashchange');

				 	var location_hash=window.location.hash;
				 	if(location_hash!="")
				 	{
				 		wf_tab_view.showTab(location_hash);
				 	}else
				 	{
				 		wf_nav_tab.eq(0).click();
				 	}		 	
				}
				this.subTab();
	    	},
	    	showTab:function(location_hash)
	    	{
	    		var wf_tab_hash=location_hash.charAt(0)=='#' ? location_hash.substring(1) : location_hash;
		 		if(wf_tab_hash!="")
		 		{
		 			var wf_tab_elm=$('div[data-id="'+wf_tab_hash+'"]');
			 		if(wf_tab_elm.length>0 && wf_tab_elm.is(':hidden'))
			 		{	 			
			 			$('a[href="#'+wf_tab_hash+'"]').click();
			 		}
		 		}
	    	},
	    	subTab:function()
	    	{
	    		$('.wf_sub_tab li').click(function(){
					var trgt=$(this).attr('data-target');
					var prnt=$(this).parent('.wf_sub_tab');
					var ctnr=prnt.siblings('.wf_sub_tab_container');
					prnt.find('li a').css({'color':'#0073aa','cursor':'pointer'});
					$(this).find('a').css({'color':'#ccc','cursor':'default'});
					ctnr.find('.wf_sub_tab_content').hide();
					ctnr.find('.wf_sub_tab_content[data-id="'+trgt+'"]').fadeIn();
				});
				$('.wf_sub_tab').each(function(){
					var elm=$(this).children('li').eq(0);
					elm.click();
				});
	    	}
	    }
	    wf_tab_view.Set();

	});

})( jQuery );

var wf_settings_form=
{
	Set:function()
	{
		jQuery('.wf_settings_form').submit(function(e){
			e.preventDefault();
			var submit_action=jQuery(this).find('.wf_update_action').val();
			var data=jQuery(this).serialize();
			var url=jQuery(this).attr('action');
			var submit_btn=jQuery(this).find('input[type="submit"]');
			var spinner=submit_btn.siblings('.spinner');
			spinner.css({'visibility':'visible'});
			submit_btn.css({'opacity':'.5','cursor':'default'}).prop('disabled',true);			
			jQuery.ajax({
				url:url,
				type:'POST',
				data:data+'&wf_settings_ajax_update='+submit_action+'&_wpnonce='+wf_pklist_params.nonces.wf_packlist,
				success:function(data)
				{
					spinner.css({'visibility':'hidden'});
					submit_btn.css({'opacity':'1','cursor':'pointer'}).prop('disabled',false);
					wf_notify_msg.success(wf_pklist_params.msgs.settings_success);
				},
				error:function () 
				{
					spinner.css({'visibility':'hidden'});
					submit_btn.css({'opacity':'1','cursor':'pointer'}).prop('disabled',false);
					wf_notify_msg.error(wf_pklist_params.msgs.settings_error);
				}
			});
		});
	}
}

var wf_form_toggler=
{
	Set:function()
	{
		this.runToggler();
		jQuery('select.wf_form_toggle').change(function(){
			wf_form_toggler.toggle(jQuery(this));
		});
		jQuery('input[type="radio"].wf_form_toggle').click(function(){
			if(jQuery(this).is(':checked'))
			{
				wf_form_toggler.toggle(jQuery(this));
			}
		});
		jQuery('input[type="checkbox"].wf_form_toggle').click(function(){
			wf_form_toggler.toggle(jQuery(this),1);
		});
	},
	runToggler:function(prnt)
	{
		prnt=prnt ? prnt : jQuery('body');
		prnt.find('select.wf_form_toggle').each(function(){
			wf_form_toggler.toggle(jQuery(this));
		});
		prnt.find('input[type="radio"].wf_form_toggle, input[type="checkbox"].wf_form_toggle').each(function(){
			if(jQuery(this).is(':checked'))
			{
				wf_form_toggler.toggle(jQuery(this));
			}
		});
		prnt.find('input[type="checkbox"].wf_form_toggle').each(function(){
			wf_form_toggler.toggle(jQuery(this),1);
		});
	},
	toggle:function(elm,checkbox)
	{
		var vl=elm.val();
		var trgt=elm.attr('wf_frm_tgl-target');
		jQuery('[wf_frm_tgl-id="'+trgt+'"]').hide();
		
		if(elm.css('display')!='none') /* if parent is visible. `:visible` method. it will not work on JS tabview */
		{
			var elms=this.getElms(elm,trgt,vl,checkbox);
			elms.show().find('th label').css({'margin-left':'0px'})
			elms.each(function(){
				var lvl=jQuery(this).attr('wf_frm_tgl-lvl');
				var mrgin=15;
				if (typeof lvl!== typeof undefined && lvl!== false) {
				    mrgin=lvl*mrgin;
				}
				jQuery(this).find('th label').animate({'margin-left':mrgin+'px'});
			});
		}

		/* in case of greater than 1 level */
		jQuery('[wf_frm_tgl-id="'+trgt+'"]').each(function(){
			wf_form_toggler.runToggler(jQuery(this));
		});
	},
	getElms:function(elm,trgt,vl,checkbox)
	{
		
		return jQuery('[wf_frm_tgl-id="'+trgt+'"]').filter(function(){
				if(jQuery(this).attr('wf_frm_tgl-val')==vl)
				{
					if(checkbox)
					{
						if(elm.is(':checked'))
						{
							if(jQuery(this).attr('wf_frm_tgl-chk')=='true')
							{
								return true;
							}else
							{
								return false;
							}
						}else
						{
							if(jQuery(this).attr('wf_frm_tgl-chk')=='false')
							{
								return true;
							}else
							{
								return false;
							}
						}
					}else
					{
						return true;
					}
				}else
				{
					return false;
				}
			});
	}
}
var wf_file_attacher={

	Set:function()
	{
		var file_frame;
		jQuery(".wf_file_attacher").click(function(event){
			event.preventDefault();
			if(jQuery(this).data('file_frame'))
			{
				
			}else
			{
				// Create the media frame.
				var file_frame = wp.media.frames.file_frame = wp.media({
					title: jQuery( this ).data( 'invoice_uploader_title' ),
					button: {
						text: jQuery( this ).data( 'invoice_uploader_button_text' ),
					},
					// Set to true to allow multiple files to be selected
					multiple: false
				});
				jQuery(this).data('file_frame',file_frame);
				var wf_file_target=jQuery(this).attr('wf_file_attacher_target');
				var wf_file_preview=jQuery(this).parent('.wf_file_attacher_dv').siblings('.wf_image_preview_small');
				var elm=jQuery(this);

				// When an image is selected, run a callback.
				jQuery(this).data('file_frame').on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					var attachment =file_frame.state().get('selection').first().toJSON();
					// Send the value of attachment.url back to shipment label printing settings form
					jQuery(wf_file_target).val(attachment.url);
					if(wf_file_preview.length>0)
					{
						wf_file_preview.attr('src',attachment.url);
					}
				});
				// Finally, open the modal				
			}
			jQuery(this).data('file_frame').open();
		});
		function wf_update_preview_img(wf_file_target,wf_file_preview)
		{
			if(jQuery(wf_file_target).val()=="")
			{ 
				wf_file_preview.attr('src',wf_pklist_params.no_image);
			}else
			{
				wf_file_preview.attr('src',jQuery(wf_file_target).val());
			}
		}
		jQuery(".wf_file_attacher").each(function(){
			var wf_file_target=jQuery(this).attr('wf_file_attacher_target');
			var wf_file_preview=jQuery(this).parent('.wf_file_attacher_dv').siblings('.wf_image_preview_small');
			if(wf_file_preview.length>0)
			{ 
				wf_update_preview_img(wf_file_target,wf_file_preview);
				jQuery(wf_file_target).change(function(){
					wf_update_preview_img(wf_file_target,wf_file_preview);
				});
			}
		});
	}
}
var wf_notify_msg=
{
	error:function(message)
	{
		var er_elm=jQuery('<div class="notify_msg" style="background:#dd4c27; border:solid 1px #dd431c;">'+message+'</div>');				
		this.setNotify(er_elm);
	},
	success:function(message)
	{
		var suss_elm=jQuery('<div class="notify_msg" style="background:#1de026; border:solid 1px #2bcc1c;">'+message+'</div>');				
		this.setNotify(suss_elm);
	},
	setNotify:function(elm)
	{
		jQuery('body').append(elm);
		elm.stop(true,true).animate({'opacity':1,'top':'50px'},1000);
		setTimeout(function(){
			elm.animate({'opacity':0,'top':'100px'},1000,function(){
				elm.remove();
			});
		},3000);
	}
}


var wf_popover={
	Set:function()
	{
		jQuery('[data-wf_popover="1"]').click(function(){
			var cr_elm=jQuery(this);
			if(cr_elm.attr('data-popup-opened')==1)
			{
				var pp_elm=jQuery('.wf_popover');
				var pp_lft=pp_elm.offset().left-50;
				jQuery('[data-wf_popover="1"]').attr('data-popup-opened',0);
				pp_elm.stop(true,true).animate({'left':pp_lft,'opacity':0},300,function(){
					jQuery(this).css({'display':'none'});
				});
				return false;
			}else
			{
				jQuery('[data-wf_popover="1"]').attr('data-popup-opened',0);
				cr_elm.attr('data-popup-opened',1);
			}
			if(jQuery('.wf_popover').length==0)
			{
				var template='<div class="wf_popover"><h3 class="wf_popover-title"></h3>'
				+'<form class="wf_popover-content"></form><div class="wf_popover-footer">'
				+'<button name="new_custom_click" type="button" id="new_custom_click" class="button button-primary">ADD</button>'
				+'<button name="popover_close" type="button" class="button button-secondary popover_close">CLOSE</button>'
				+'<span class="spinner" style="margin-top:5px"></span>'
				+'</div></div>';
				jQuery('body').append(template);
				wf_popover.regclosePop();
				wf_popover.sendData();
			}
			
			var pp_elm=jQuery('.wf_popover');
			var action_field='<input type="hidden" name="action" value="'+cr_elm.attr('data-ajax-action')+'"  />';
			pp_elm.css({'display':'block'}).find('.wf_popover-content').html(cr_elm.attr('data-content')).append(action_field);
			var cr_elm_w=cr_elm.width();
			var cr_elm_h=cr_elm.height();
			var pp_elm_w=pp_elm.width();
			var pp_elm_h=pp_elm.height();
			var cr_elm_pos=cr_elm.offset();
			var cr_elm_pos_t=cr_elm_pos.top-((pp_elm_h-cr_elm_h)/2);
			var cr_elm_pos_l=cr_elm_pos.left+cr_elm_w;	    			
			pp_elm.find('.wf_popover-title').html(cr_elm.attr('data-title'));			
			pp_elm.css({'display':'block','opacity':0,'top':cr_elm_pos_t,'left':cr_elm_pos_l}).stop(true,true).animate({'left':cr_elm_pos_l+50,'opacity':1});
			jQuery('[name="new_custom_click"]').data('select-elm',cr_elm.parents('.wf_select_multi').find('select'));
		});
	},
	sendData:function()
	{	    		
		jQuery('[name="new_custom_click"]').click(function(){
	        
	        var empty_fields=0;
	        jQuery('.wf_popover-content input[type="text"]').each(function(){
	        	if(jQuery.trim(jQuery(this).val())=="")
	        	{
					empty_fields++;
	        	}
	        });
	        if(empty_fields>0){
	        	alert(wf_pklist_params.msgs.all_fields_mandatory);
	        	jQuery('.wf_popover-content input[type="text"]:eq(0)').focus();
	        	return false;
	        }
	        var elm=jQuery(this);
	        var sele_elm=elm.data('select-elm');
	        jQuery('.wf_popover-footer .spinner').css({'visibility':'visible'});
	        jQuery('.wf_popover-footer .button').attr('disabled','disabled').css({'opacity':.5});
	        var data=jQuery('.wf_popover-content').serialize();

	        data+='&_wpnonce='+wf_pklist_params.nonces.wf_packlist+'&new_custom_click';		
	        jQuery.ajax({
				url:wf_pklist_params.ajaxurl,
				data:data,
				dataType:'json',
            	type: 'POST',
				success:function(data)
				{
					jQuery('.wf_popover-footer .spinner').css({'visibility':'hidden'});
					jQuery('.wf_popover-footer .button').removeAttr('disabled').css({'opacity':1});
					if(data.success==true)
					{
						var newOption = new Option(data.val,data.key, true, true);
						sele_elm.append(newOption).trigger('change');
					}else
					{
						alert(data.msg);
						jQuery('.wf_popover-footer .spinner').css({'visibility':'hidden'});
						jQuery('.wf_popover-footer .button').removeAttr('disabled').css({'opacity':1});
					}
				},
				error:function()
				{
					jQuery('.wf_popover-footer .spinner').css({'visibility':'hidden'});
					jQuery('.wf_popover-footer .button').removeAttr('disabled').css({'opacity':1});
				}
			});
		});
	},
	regclosePop:function()
	{
		jQuery('.nav-tab ').click(function(){
			jQuery('.wf_popover').css({'display':'none'});
		});
		jQuery('.popover_close').click(function(){
			wf_popover.closePop();
		});
	},
	closePop:function()
	{
		var pp_elm=jQuery('.wf_popover');
		if(pp_elm.length>0)
		{
			var pp_lft=pp_elm.offset().left-50;
			jQuery('[data-wf_popover="1"]').attr('data-popup-opened',0);
			pp_elm.stop(true,true).animate({'left':pp_lft,'opacity':0},300,function(){
				jQuery(this).css({'display':'none'});
			});
		}
	}
};
var wf_accord=
{
	Set:function()
	{
		jQuery('.wf_side_panel .wf_side_panel_hd').click(function(e){ 
			e.stopPropagation();
			if(e.target.className=='wf_side_panel_hd' || e.target.className=='dashicons dashicons-arrow-right' || e.target.className=='dashicons dashicons-arrow-down')
			{ 
				var elm=jQuery(this);
				var prnt_dv=elm.parents('.wf_side_panel');
				var cnt_dv=prnt_dv.find('.wf_side_panel_content');
				if(prnt_dv.attr('data-disabled')==1)
				{
					cnt_dv.hide();
					return false;
				}				
				if(cnt_dv.is(':visible'))
				{
					elm.find('.dashicons').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-right');
					cnt_dv.hide();
				}else
				{
					elm.find('.dashicons').removeClass('dashicons-arrow-right').addClass('dashicons-arrow-down');
					cnt_dv.show().css({'opacity':0}).stop(true,true).animate({'opacity':1});
				}
			}
		});
	}
}
var wf_color=
{
	Set:function()
	{
		jQuery('.wf-color-field').wpColorPicker({
	 		'change':function(event, ui)
	 		{
	 			jQuery(event.target).val(ui.color.toString());
	 			jQuery(event.target).click();
	 		}
	 	});
		jQuery('.wf-color-field').each(function(){
			jQuery('<input type="button" class="button button-small wf-color-default" value="Default">').insertAfter(jQuery(this).parents('.wp-picker-container').find('.wp-picker-clear'));
		});
		jQuery('.wf-color-default').click(function(){
			var inpt_fld=jQuery(this).parents('.wp-picker-container').find('.wf-color-field');
			var def_val=inpt_fld.attr('data-default');
			inpt_fld.val(def_val);
			inpt_fld.iris('color',def_val);
		});
	}
}
var wf_slideSwitch=
{
	Set:function()
	{
		jQuery('.wf_slide_switch').each(function(){
			jQuery(this).wrap('<label class="wf_switch"></label>');
			jQuery('<span class="wf_slider wf_round"></span>').insertAfter(jQuery(this));
		});
	}
};
function wf_Confirm_Notice_for_Manually_Creating_Invoicenumbers(url,a)
{
    if(a == 1)
    {
        if(confirm ('Invoice number has not been generated yet. Do you want to manually generate one ?'))
        {                       
            window.open(url, "Print", "width=800, height=600");
            setTimeout(function () {
                window.location.reload(true);
            }, 1000);
        } else {
            return false;
        }
    }
    else
    {
        window.open(url, "Print", "width=800, height=600");                           
    }
    return false;
}
wf_popup={
	Set:function()
	{
		this.regPopupOpen();
		this.regPopupClose();
		jQuery('body').prepend('<div class="wf_cst_overlay"></div>');
	},
	regPopupOpen:function()
	{
		jQuery('[data-wf_popup]').click(function(e){			
			var elm_class=jQuery(this).attr('data-wf_popup');
			var elm=jQuery('.'+elm_class);
			if(elm.length>0)
			{
				e.preventDefault();
				wf_popup.showPopup(elm);
			}
		});
	},
	showPopup:function(popup_elm)
	{
		var pw=popup_elm.outerWidth();
		var wh=jQuery(window).height();
		var ph=wh-150;
		popup_elm.css({'margin-left':((pw/2)*-1),'display':'block','top':'20px'}).animate({'top':'50px'});
		popup_elm.find('.wf_pklist_popup_body').css({'max-height':ph+'px','overflow':'auto'});
		jQuery('.wf_cst_overlay').show();
	},
	hidePopup:function()
	{
		jQuery('.wf_pklist_popup_close').click();
	},
	regPopupClose:function(popup_elm)
	{
		jQuery(document).keyup(function(e){
			if(e.keyCode==27)
			{
				wf_popup.hidePopup();
			}
		});
		jQuery('.wf_pklist_popup_close, .wf_pklist_popup_cancel').unbind('click').click(function(){
			jQuery('.wf_cst_overlay, .wf_pklist_popup').hide();
		});
	}
}
jQuery(document).ready(function(){
	wf_popup.Set();
	wf_file_attacher.Set();
	wf_form_toggler.Set();
	wf_popover.Set();
	wf_settings_form.Set();
	wf_accord.Set();
	wf_color.Set();
	wf_slideSwitch.Set();
});
