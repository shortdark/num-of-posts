jQuery(document).ready(function($) {
	
	$(".sdpvs_form").submit(function(e) {
		$("#sdpvs_loading").show();
		$("#sdpvs_load_content").attr('disabled', true);

		var sdpvs_buttondata = $(this).serialize();
		// var whichdata = $(this).val("whichdata");
		// alert(whichdata);

		var data = {
			action : "sdpvs_get_results",
			whichdata : sdpvs_buttondata,
			security : sdpvs_vars.ajax_nonce,
		};

		$.post(ajaxurl, data, function(response) {
			$('#sdpvs_listcontent').html(response);
			$("#sdpvs_listcontent").show();
			$("#sdpvs_loading").hide();
			$("#sdpvs_load_content").attr('disabled', false);
		});
		return false;

	});

	$(document).mouseup(function(e) {
		var container = $("#sdpvs_listcontent");

		// if the target of the click isn't the container nor a descendant of the container
		if (!container.is(e.target) && container.has(e.target).length === 0) {
			container.hide();
		}
	});

});
