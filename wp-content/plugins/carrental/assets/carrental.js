/*
Car Rental WP Plugin - 2015

v 3.0.0

*/
 
jQuery(document).ready(function($) {
	
	if ($('#ecalypse-news-div').length > 0) {
		$('#ecalypse-feed-dialog').dialog({
      autoOpen: false,
	  minWidth: 600,
      show: {
        effect: "blind",
        duration: 200
      },
	  modal: true
    });
		
		
		// load ecalypse news
		jQuery.ajax({
			url: ajaxurl,
			global: false,
			type: "POST",
			dataType: 'json',
			data: ({
				action: 'carrental_load_ecalypse_news'
			}),
			complete: function(data){
				$('#ecalypse-news-ajax-loader').remove();
				if (!data || !data.responseJSON) {
					$('#ecalypse-news-div').append('<div class="alert alert-warning">There are no news for you. Have a nice day!</div>');
				} else {
					$.each(data.responseJSON, function (k,v) {
						$('#ecalypse-news-div').append('<div class="panel panel-info feed-box" style="border-color:#'+v['bg_color']+';" data-type="'+v['type']+'" data-id="'+v['id']+'">\
							  <div class="panel-heading" style="background-color:#'+v['bg_color']+';border-color:#'+v['bg_color']+';color:#'+v['text_color']+';"><a href="#" class="feed-read-more feed-name">'+v['name']+'</a><span class="news-date">'+v['date_from']+'</span></div>\
							  <div class="panel-body">'+v['preview']+'\
								<div class="feed-text">'+v['text']+'</div>\
								<div class="feed-buttons"><a class="btn btn-default feed-read-more" href="#"><strong>Read more</strong></a></div>\
							  </div>\
							</div>').hide().delay( 100 ).show('slow');
					});
					
				}
			},
			async: true
		}); 
	}
	
	$(document).on('click', '.feed-buttons-close', function(e) {
		e.preventDefault();
		$('#ecalypse-feed-dialog').dialog("close");
	});
	
	$(document).on('click', '.feed-buttons-delete', function(e) {
		e.preventDefault();
		var dataid = $('#ecalypse-feed-dialog').attr('data-id');
		jQuery.ajax({
			url: ajaxurl,
			global: false,
			type: "POST",
			dataType: 'json',
			data: ({
				action: 'carrental_feed_actions',
				type: 'delete',
				id: dataid
			}),
			complete: function(){
				$('#ecalypse-feed-dialog').dialog("close");
				$('.feed-box[data-id='+dataid+']').remove();
			},
			async: true
		}); 
	});
	
	$(document).on('click', '.feed-buttons-confirm', function(e) {
		e.preventDefault();
		var dataid = $('#ecalypse-feed-dialog').attr('data-id');
		jQuery.ajax({
			url: ajaxurl,
			global: false,
			type: "POST",
			dataType: 'json',
			data: ({
				action: 'carrental_feed_actions',
				type: 'confirm',
				id: dataid
			}),
			complete: function(){
				$('#ecalypse-feed-dialog').dialog("close");
				$('.feed-box[data-id='+dataid+']').remove();
			},
			async: true
		}); 
		$('#ecalypse-feed-dialog').dialog("close");
	});
	
	$(document).on('click', '.feed-read-more', function(e) {
		e.preventDefault();
		el = $(this).closest('.feed-box');
		$('#ecalypse-feed-dialog').attr('title', el.find('.feed-name').text());
		$('#ecalypse-feed-dialog').attr('data-id', el.attr('data-id'));
		$('#ecalypse-feed-dialog').dialog('option', 'title', el.find('.feed-name').text());
		buttons = '<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix"><div class="ui-dialog-buttonset">';
		if (el.attr('data-type') == 2) {
			// confirm dialog
			buttons += '<a class="btn btn-default feed-buttons-confirm mr" href="#"><strong>Confirm reading</strong></a>';
		} else {
			buttons += '<a class="btn btn-default feed-buttons-delete mr" href="#"><strong>Delete</strong></a>';
		}
		buttons += '<a class="btn btn-default feed-buttons-close" href="#"><strong>Close</strong></a></div></div>';
		
		$('#ecalypse-feed-dialog').html( el.find('.feed-text').html() + buttons);
		$('#ecalypse-feed-dialog').dialog("open");
	});
	
	$('#carrental-fleet-add-form').hide();
	$('#carrental-extras-add-form').hide();
	$('#carrental-branches-add-form').hide();
	$('#carrental-booking-add-form').hide();
	$('#carrental-pricing-add-form').hide();
	$('#carrental-language-add-form').hide();
	$('#carrental-language-primary-form').hide();
	
	$('#carrental-fleet-parameters').dataTable({  stateSave: true, "ordering": false, "paging": false});
  $('#carrental-fleet').dataTable({  stateSave: true, "ordering": false, "paging": false});
	$('#carrental-extras').dataTable({ stateSave: true });
	$('#carrental-branches').dataTable({ stateSave: true, "ordering": false, "paging": false });
	$('#carrental-booking').dataTable({ stateSave: true, "searching": false });
	$('#carrental-pricing').dataTable({ stateSave: true });
	if (jQuery.fn.DataTable.TableTools) { 
		TableTools.DEFAULTS.aButtons = [ "print" ];
	}
	$('#carrental-newsletter').dataTable({
		stateSave: true,
		"dom": 'T<"clear">lfrtip'
	});
	
	$('.data_table_select_all').click(function(){
		table = $('#'+$(this).attr('data-id'));
		table.find(':checkbox').prop('checked', $(this).is(':checked'));
		$('.data_table_select_all[data-id="'+$(this).attr('data-id')+'"]').prop('checked', $(this).is(':checked'));
	});
	
	
	$("#carrental-fleet-add-button").click(function() {
	  $("#carrental-fleet-add-form").toggle("slow");
	});
	
	$("#carrental-fleet-parameter-add-button").click(function() {
	  $("#carrental-fleet-parameter-add-form").toggle("slow");
	});

	$("#carrental-extras-add-button").click(function() {
	  $("#carrental-extras-add-form").toggle("slow");
	});
	
	$("#carrental-branches-add-button").click(function() {
	  $("#carrental-branches-add-form").toggle("slow");
	});
	
	$("#carrental-pricing-add-button").click(function() {
	  $("#carrental-pricing-add-form").toggle("slow");
	});
	
	$("#carrental-booking-add-button").click(function() {
	  $("#carrental-booking-add-form").toggle("slow");
	});
	
	$("#carrental-language-add-button").click(function() {
	  $("#carrental-language-add-form").toggle("slow");
	});
	
	$("#carrental-language-primary-button").click(function() {
	  $("#carrental-language-primary-form").toggle("slow");
	});
	
	$('#carrental-add-pricing-scheme').click(function() {
		carrental_add_pricing();
	});
	
	$('#carrental-add-additional-parameter').click(function() {
		carrental_add_additional_parameter();
	});
	
	$('.carrental-insert-parameter-link').click(function(e) {
		e.preventDefault();
		carrental_insert_existing_parameter($(this));
	});
	
	$(document).on('click', '.fleet-delete-parameter', function() {
		carrental_remove_additional_parameter(jQuery(this));
	});
	
	$(document).on('blur', '.fleet-parameter-name', function() {
		carrental_blur_additional_parameter(jQuery(this));
	});
	
	$(".additional_parameters_tab").sortable({
		stop: carrental_sort_additional_parameter
	});
	
	$('#carrental-add-fleet-parameter-value').click(function() {
		carrental_add_fleet_parameter_value();
	});
	
	$(document).on('click', '.fleet-delete-parameter-value', function() {
		carrental_remove_fleet_parameter_value(jQuery(this));
	});
	
	$(document).on('blur', '.fleet-parameter-value', function() {
		carrental_blur_fleet_parameter_value(jQuery(this));
	});
	
	$('#carrental-fleet-parameter-form').on('submit', function() {
		if ($('.fleet_parameter_name_input.lng_gb').val() == '') {
			alert('Sorry, Name of the parameter in english should not be empty.');
			return false;
		}
	});
	
	$("#carrental-hour-range-box-show").click(function() {
	  $("#carrental-hour-range-box").toggle("fast");
	});
	
	
	$('#carrental-add-av-currencies').click(function() {
		$('#carrental-av-currencies-insert').after($("#carrental-av-currencies").html());
	});
	
	$('#carrental-add-vehicle-category').click(function() {
		$('#carrental-vehicle-cats-insert').before('<tr>' + $("#carrental-vehicle-cats").html() + '</tr>');
	});
	
	$('#carrental-add-day-range').click(function() {
		$('#day-range-row-before').before('<tr>' + $("#day-range-row").html() + '</tr>');
	});
	
	$('#carrental-add-hour-range').click(function() {
		$('#hour-range-row-before').before('<tr>' + $("#hour-range-row").html() + '</tr>');
	});
	
	$('#carrental-fleet-add-form form').on('submit', function() {
		if ($('#carrental-type').val() == '') {
			alert('Sorry, Name of the vehicle should not be empty.');
			return false;
		}
	});
	
	$('#carrental-extras-add-form form').on('submit', function() {
		if ($('#carrental-name').val() == '') {
			alert('Sorry, Name of the item should not be empty.');
			return false;
		}
	});
	
	$('#carrental-branches-add-form form').on('submit', function() {
		if ($('#carrental-name').val() == '') {
			alert('Sorry, Name of the branch should not be empty.');
			return false;
		}
	});
	
	$('#carrental-pricing-add-form form').on('submit', function() {
		if ($('#carrental-name').val() == '') {
			alert('Sorry, Name of the scheme should not be empty.');
			return false;
		}
	});
	
	$(document).on('keyup', '[name=days\\[from\\]\\[\\]]', function() {
		carrental_check_ranges('days');
	});
	
	$(document).on('keyup', '[name=days\\[to\\]\\[\\]]', function() {
		carrental_check_ranges('days');
	});
	
	$(document).on('keyup', '[name=hours\\[from\\]\\[\\]]', function() {
		carrental_check_ranges('hours');
	});
	
	$(document).on('keyup', '[name=hours\\[to\\]\\[\\]]', function() {
		carrental_check_ranges('hours');
	});
	
	$('[name=currency]').on('change', function() {
		carrental_pricing_set_currency();
	});
	
	$('[name=type]').on('click', function() {
		if ($(this).val() == 1) {
			$('.type-onetime').show();
			$('.type-timerelated').hide();
		} else {
			$('.type-onetime').hide();
			$('.type-timerelated').show();
		}
		carrental_pricing_set_currency();
	});
	
	// Init
	$('#carrental-prices').hide();
	$('#days-range-help').hide();
	$('#hours-range-help').hide();
	
	carrental_add_pricing();
	carrental_add_additional_parameter();
	carrental_pricing_set_currency();
	
	$("#pricing_sort").sortable();
	//$("#pricing_sort").disableSelection();
	//$("#additional_parameters_sort").disableSelection();
			    
	$(document).on('click', '.pricing_datepicker', function() {
		$(this).datepicker({ dateFormat: 'yy-mm-dd' }).datepicker('show');
	});
	
	$(document).on('click', '.carrental_show_ranges', function() {
		var $dialog = $('<div>Loading...</div>')
				.load($(this).attr('href'))
				.dialog({
					autoOpen: true,
					title: 'Details',
					width: 700,
					height: 400,
					resizable: true
				});
		return false;
	});
	
	// Translations
	$('.carrental_translations_email_customers').hide();
	$('.carrental_translations_email_reminder_customers').hide();
	$('.carrental_translations_email_thank_you').hide();
	$('.carrental_translations_email_status_pending').hide();
	$('.carrental_translations_email_status_pending_other').hide();
	$('.carrental_translations_email_status_confirmed').hide();
	$('.carrental_translations_terms').hide();
	$('.carrental_translations_theme').hide();
	
	$(".carrental_translations_email_customers_toggle").click(function() {
	  $(".carrental_translations_email_customers").toggle("fast", function() {
			if ($(this).is(':hidden')) {
				$(".carrental_translations_email_customers_toggle").find('span').html('▼');
			} else {
				$(".carrental_translations_email_customers_toggle").find('span').html('▲');
			}	
		});
	});
	
	
	$(".carrental_translations_email_reminder_customers_toggle").click(function() {
	  $(".carrental_translations_email_reminder_customers").toggle("fast", function() {
			if ($(this).is(':hidden')) {
				$(".carrental_translations_email_reminder_customers_toggle").find('span').html('▼');
			} else {
				$(".carrental_translations_email_reminder_customers_toggle").find('span').html('▲');
			}	
		});
	});
	
	$(".carrental_translations_email_status_pending_toggle").click(function() {
	  $(".carrental_translations_email_status_pending").toggle("fast", function() {
			if ($(this).is(':hidden')) {
				$(".carrental_translations_email_status_pending_toggle").find('span').html('▼');
			} else {
				$(".carrental_translations_email_status_pending_toggle").find('span').html('▲');
			}	
		});
	});
	
	$(".carrental_translations_email_thank_you_toggle").click(function() {
	  $(".carrental_translations_email_thank_you").toggle("fast", function() {
			if ($(this).is(':hidden')) {
				$(".carrental_translations_email_thank_you_toggle").find('span').html('▼');
			} else {
				$(".carrental_translations_email_thank_you_toggle").find('span').html('▲');
			}	
		});
	});
	
	$(".carrental_translations_email_status_pending_other_toggle").click(function() {
	  $(".carrental_translations_email_status_pending_other").toggle("fast", function() {
			if ($(this).is(':hidden')) {
				$(".carrental_translations_email_status_pending_other_toggle").find('span').html('▼');
			} else {
				$(".carrental_translations_email_status_pending_other_toggle").find('span').html('▲');
			}	
		});
	});
	
	$(".carrental_translations_email_status_confirmed_toggle").click(function() {
	  $(".carrental_translations_email_status_confirmed").toggle("fast", function() {
			if ($(this).is(':hidden')) {
				$(".carrental_translations_email_status_confirmed_toggle").find('span').html('▼');
			} else {
				$(".carrental_translations_email_status_confirmed_toggle").find('span').html('▲');
			}	
		});
	});
	
	$(".carrental_translations_terms_toggle").click(function() {
	  $(".carrental_translations_terms").toggle("fast", function() {
			if ($(this).is(':hidden')) {
				$(".carrental_translations_terms_toggle").find('span').html('▼');
			} else {
				$(".carrental_translations_terms_toggle").find('span').html('▲');
			}	
		});
	});
	
	$(".carrental_translations_theme_toggle").click(function() {
	  $(".carrental_translations_theme").toggle("fast", function() {
			if ($(this).is(':hidden')) {
				$(".carrental_translations_theme_toggle").find('span').html('▼');
			} else {
				$(".carrental_translations_theme_toggle").find('span').html('▲');
			}	
		});
	});
	
	
	// Fleet translations
	$('.fleet_description').hide();
	$('.fleet_description_gb').show();
	
	$('.edit_fleet_description').click(function() {
		var lang = $(this).attr('data-value');
		$('.fleet_description').hide();
		$('.edit_fleet_description').parent().removeClass('active');
		$('.fleet_description_' + lang).show();
		$(this).parent().addClass('active');
	});
	
	$('.edit_fleet_parameters').click(function() {
		var lang = $(this).attr('data-value');
		$('.additional_parameters_tab').hide();
		$('.edit_fleet_parameters').parent().removeClass('active');
		$('#additional_parameters_sort_' + lang).show();
		$(this).parent().addClass('active');
	});
	
	$('.edit_fleet_parameter_value').click(function() {
		var lang = $(this).attr('data-value');
		$('.fleet_parameter_values_tab').hide();
		$('.edit_fleet_parameter_value').parent().removeClass('active');
		$('#fleet_parameter_values_sort_' + lang).show();
		$(this).parent().addClass('active');
	});
	carrental_add_fleet_parameter_value();
	
	$('.edit_fleet_parameter_name').click(function() {
		var lang = $(this).attr('data-value');
		$('.fleet_parameter_name_input').hide();
		$('.edit_fleet_parameter_name').parent().removeClass('active');
		$('.fleet_parameter_name_input.lng_' + lang).show();
		$(this).parent().addClass('active');
	});
	
	$('.carrental-fleet-parameter-type').change(function() {
		type = $('input.carrental-fleet-parameter-type:checked').attr('data-type');
		$('.carrental_fleet_parameter_type_block').hide();
		$('.carrental_fleet_parameter_type_block.type_'+type).show();
	});
	
	$('.edit_extras_name_desc').click(function() {
		var lang = $(this).attr('data-value');
		$('.carrental_extras_translations').hide();
		$('.edit_extras_name_desc').parent().removeClass('active');
		$('.carrental_extras_translations[data-lng=' + lang+']').show();
		$(this).parent().addClass('active');
	});
	
	// Disclamer translations
	$('.disclaimer').hide();
	$('.disclaimer_gb').show();
	
	$('.edit_disclaimer').click(function() {
		var lang = $(this).attr('data-value');
		$('.disclaimer').hide();
		$('.edit_disclaimer').parent().removeClass('active');
		$('.disclaimer_' + lang).show();
		$(this).parent().addClass('active');
	});
	
	
	$('.days-check-all').click(function() {
		if ($(this).is(':checked')) {
			$('.days-check').prop('checked', true);
		} else {
			$('.days-check').prop('checked', false);
		}
	});
	
	// Batch processing (fleet, extras, branches, pricing, booking)
	$(document).on('click', '.batch_processing, .data_table_select_all', function() {
		var values = new Array();
		var values_delete = new Array();
		$('.batch_processing').each(function() {
			if ($(this).is(':checked')) {
				values.push($(this).val());
				if (parseInt($(this).attr('data-usage')) == 0) {
					values_delete.push($(this).val());
				}
			}
		});
		
		$('[name=batch_processing_values]').val(values.join(','));
		$('.batch_processing_count').html(((values.length > 0) ? values.length + ' ' : ''));
		
		$('[name=batch_processing_values_delete]').val(values_delete.join(','));
		$('.batch_processing_count_delete').html(((values_delete.length > 0) ? values_delete.length + ' ' : ''));

	});
	
	
});

function carrental_check_ranges(name) {
	var arr = [];
		
	jQuery('[name=' + name + '\\[from\\]\\[\\]]').each(function(i) {
		arr.push(jQuery(this).val());
	});
	
	jQuery('[name=' + name + '\\[to\\]\\[\\]]').each(function(i) {
		arr.push(jQuery(this).val());
	});
	
	arr.sort(function(a,b){return a - b});
	//$('#days-range-checker').html(arr);
	
	var results = [];
	for (var i = 0; i < arr.length - 1; i++) {
	  if (arr[i + 1] == arr[i]) {
	    results.push(arr[i]);
	  }
	}
	
	if (results != '') {
		jQuery('#' + name + '-range-help').show('fast');
	} else {
		jQuery('#' + name + '-range-help').hide('fast');
	}
	
}

function carrental_add_pricing() {
	var html = jQuery("#carrental-prices").html();
	jQuery('#carrental-prices-insert').before(html);
}

function carrental_add_additional_parameter() {
	jQuery.each(jQuery('.additional_parameters_tab'), function(k, v) {
		lng = jQuery(v).attr('data-lng');
		var html = jQuery("#carrental-additional-parameters-"+lng).html();
		last_i = 0;
		jQuery.each(jQuery('#additional_parameters_sort_'+lng+' div.row'), function(kk,vv){
			if (parseInt(jQuery(vv).attr('data-row-i')) > last_i) {
				last_i = parseInt(jQuery(vv).attr('data-row-i'));
			}
		});
		last_i++;
		
		html = html.replace('data-row-i="0"', 'data-row-i="'+last_i+'"');
		jQuery('#carrental-additional-parameters-insert-'+lng).before(html.replace(/0/g,last_i));
	});
	
	//var html = jQuery("#carrental-additional-parameters").html();	
	/*last_i = jQuery('#additional_parameters_sort div.row:last input:first').attr('name');
	last_i = last_i.substring(22);
	last_i = parseInt(last_i.substring(0, last_i.indexOf(']'))) + 1;*/
	
}

function carrental_pricing_set_currency() {
	jQuery('.addon-currency').html(jQuery('[name=currency]').val());	
}

function carrental_remove_additional_parameter(el) {	
	i = el.closest('.row').attr('data-row-i');
	jQuery('.additional_parameters_tab .row[data-row-i='+i+']').remove();	
}

function carrental_blur_additional_parameter(el) {
	i = el.closest('.row').attr('data-row-i');
	if (el.val() == '') {
		return;
	}
	jQuery.each(jQuery('.additional_parameters_tab .row[data-row-i='+i+'] .fleet-parameter-name'), function(k, v) {
		if (jQuery(v).val() == '') {
			jQuery(v).attr('placeholder', el.val());
		}
	});
}

function carrental_sort_additional_parameter(event, ui) {
	parent = jQuery(ui.item).parent();
	sorted = jQuery(this).sortable('toArray', {attribute: 'data-row-i'});
	jQuery.each(jQuery('.additional_parameters_tab[data-lng!='+parent.attr('data-lng')+']'), function(k, v){
		var lang = jQuery(this).attr('data-lng');
		after = jQuery('#carrental-additional-parameters-'+lang);
		jQuery.each(sorted, function(kk, vv){
			if (vv == '') {
				return;
			}			
			jQuery('#additional_parameters_sort_'+lang+' .row[data-row-i='+vv+']').insertAfter(after);
			after = jQuery('#additional_parameters_sort_'+lang+' .row[data-row-i='+vv+']');
			
		});
	});
}

function carrental_insert_existing_parameter(el) {	
	row = el.parent().parent().find('.row:last');
	if (row.attr('data-row-i') == '0') {
		return;
	}
	if (row.find('.fleet-parameter-name').val() == '') {
		row.find('.fleet-parameter-name').val(el.text());
	} else {
		row.find('.fleet-parameter-name').val(row.find('.fleet-parameter-name').val() + ' ' + el.text());
	}
	carrental_blur_additional_parameter(row.find('.fleet-parameter-name'));
}

function carrental_add_fleet_parameter_value() {
	jQuery.each(jQuery('.fleet_parameter_values_tab'), function(k, v) {
		lng = jQuery(v).attr('data-lng');
		var html = jQuery("#carrental-fleet-parameter-value-"+lng).html();
		last_i = 0;
		jQuery.each(jQuery('#fleet_parameter_values_sort_'+lng+' div.row'), function(kk,vv){
			if (parseInt(jQuery(vv).attr('data-row-i')) > last_i) {
				last_i = parseInt(jQuery(vv).attr('data-row-i'));
			}
		});
		last_i++;
		
		html = html.replace('data-row-i="0"', 'data-row-i="'+last_i+'"');
		jQuery('#carrental-fleet-parameter-values-insert-'+lng).before(html.replace(/0/g,last_i));
	});
}

function carrental_blur_fleet_parameter_value(el) {
	i = el.closest('.row').attr('data-row-i');
	if (el.val() == '') {
		return;
	}
	jQuery.each(jQuery('.fleet_parameter_values_tab .row[data-row-i='+i+'] .fleet-parameter-value'), function(k, v) {
		if (jQuery(v).val() == '') {
			jQuery(v).attr('placeholder', el.val());
		}
	});
}

function carrental_remove_fleet_parameter_value(el) {	
	i = el.closest('.row').attr('data-row-i');
	jQuery('.fleet_parameter_values_tab .row[data-row-i='+i+']').remove();	
}