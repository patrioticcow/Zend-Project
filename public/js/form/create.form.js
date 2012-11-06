$(document).ready(function() {
	
	var uniqueId = 1;
	var theForm = $('#the_form');
	var secondTab = $('#add_form_element li:eq(1) a');
	var fieldProp = $('#field_properties');
	
	$('.delete_li').live("click", function () {
		$(this).parent().remove();
	});
	
	$('#add_form_element a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});
	
	/**
	 * single_line_text
	 */
	$('#single_line_text').click(function (e) {
		
		var liId = "edit_form_text" + uniqueId;
		
		line_text(liId, theForm, fieldProp, uniqueId);
		
		$('#'+ liId +' #edit_form_text').click(function (e) {
			secondTab.tab('show');
			$.get('/form/input', { name: "Edit Input Field", id: liId}).done(function(data) {
				fieldProp.html(data);
		    });
		});
		
		uniqueId++;
	});
	
	/**
	 * line_number
	 */
	$('#line_number').click(function (e) {
		var liId = "edit_form_number" + uniqueId;
		
		line_number(liId, theForm, fieldProp, uniqueId);
		
		$('#'+ liId +' #edit_form_number').click(function (e) {
			secondTab.tab('show');
			$.get('/form/input', { name: "Edit Number Field", id: liId}).done(function(data) {
				fieldProp.html(data);
		    });
		});
		
		uniqueId++;
	});
	
	/**
	 * line_paragraph
	 */
	$('#line_paragraph').click(function (e) {
		
		var liId = "edit_form_paragraph" + uniqueId;
		
		line_paragraph(liId, theForm, fieldProp, uniqueId);
		
		$('#'+ liId +' #edit_form_paragraph').click(function (e) {
			secondTab.tab('show');
			$.get('/form/input', { name: "Edit Textarea Field", id: liId}).done(function(data) {
				fieldProp.html(data);
			});
		});
		
		uniqueId++;
	});
	
	/**
	 * line_checkbox
	 */
	$('#line_checkbox').click(function (e) {
		
		var liId = "edit_form_checkbox" + uniqueId;
		
		line_checkbox(liId, theForm, fieldProp, uniqueId);
		
		$('#'+ liId +' #edit_form_checkbox').click(function (e) {
			secondTab.tab('show');
			$.get('/form/input', { name: "Edit Checkbox Field", id: liId}).done(function(data) {
				fieldProp.html(data);
		    });
		});
		
		uniqueId++;
	});
	
	/**
	 * line_radio
	 */
	$('#line_radio').click(function (e) {
		
		var liId = "edit_form_radio" + uniqueId;
		
		line_radio(liId, theForm, fieldProp, uniqueId);
		
		$('#'+ liId +' #edit_form_radio').click(function (e) {
			secondTab.tab('show');
			$.get('/form/input', { name: "Edit Radio Field", id: liId}).done(function(data) {
				fieldProp.html(data);
		    });
		});
		
		uniqueId++;
	});
	
	/**
	 * line_dropdown
	 */
	$('#line_dropdown').click(function (e) {
		
		var liId = "edit_form_dropdown" + uniqueId;
		
		line_dropdown(liId, theForm, fieldProp, uniqueId);
		
		$('#'+ liId +' #edit_form_dropdown').click(function (e) {
			secondTab.tab('show');
			$.get('/form/input', { name: "Edit Drop Down Field", id: liId}).done(function(data) {
				fieldProp.html(data);
			});
		});
		
		uniqueId++;
	});
	
	
	
	// maybe i'll do a plugin
	/*
	var defaults = {
			test : 'test'
	};
	
	var formElements = function(options){
		var config = $.extend({}, defaults, options);
		
		console.log(config.test);
	};
	*/

});