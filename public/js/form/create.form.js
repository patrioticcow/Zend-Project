$(document).ready(function() {
	
	var uniqueId = 1;
	var theForm = $('#the_form');
	var secondTab = $('#add_form_element li:eq(1) a');
	var fieldProp = $('#field_properties');
	
	$('#add_form_element a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});
	
	$('#single_line_text').click(function (e) {
		
		var liId = "edit_form_text" + uniqueId;
		
		var li = $("<li/>", {
			"id": liId
		}).appendTo(theForm);
		
		$("<label/>", {
			text: "Text",
			"for": "form_input",
		}).appendTo(li);
		
		$("<input/>", {
			type: "text",
			id: "form_input",
			placeholder: "Type something..."
		}).appendTo(li);

		$("<button/>", {
			type: "button",
			"class": "btn btn-link pull-right",
			id: "edit_form_input",
			text: "Edit"
		}).appendTo(li);
		
		$('#'+ liId +' #edit_form_input').click(function (e) {
			secondTab.tab('show');
			$.get('/form/input', { name: "Edit Input Field"}).done(function(data) {
				fieldProp.html(data);
		    });
		});
		
		uniqueId++;
	});
	
	$('#line_number').click(function (e) {
		
		var liId = "edit_form_number" + uniqueId;
		
		var li = $("<li/>", {
			"id": liId
		}).appendTo(theForm);
		
		$("<label/>", {
			text: "Number",
			"for": "form_number",
		}).appendTo(li);
		
		$("<input/>", {
			type: "text",
			id: "form_number",
			placeholder: "Type something..."
		}).appendTo(li);

		$("<button/>", {
			type: "button",
			"class": "btn btn-link pull-right",
			id: "edit_form_number",
			text: "Edit"
		}).appendTo(li);
		
		$('#'+ liId +' #edit_form_number').click(function (e) {
			secondTab.tab('show');
			$.get('/form/input', { name: "Edit Number Field"}).done(function(data) {
				fieldProp.html(data);
		    });
		});
		
		uniqueId++;
	});
	
	$('#line_paragraph').click(function (e) {
		
		var liId = "edit_form_paragraph" + uniqueId;
		
		var li = $("<li/>", {
			"id": liId
		}).appendTo(theForm);
		
		$("<label/>", {
			text: "Paragraph",
			"for": "form_paragraph",
		}).appendTo(li);
		
		$("<textarea/>", {
			type: "text",
			id: "form_paragraph",
			placeholder: "Type something..."
		}).appendTo(li);

		$("<button/>", {
			type: "button",
			"class": "btn btn-link pull-right",
			id: "edit_form_paragraph",
			text: "Edit"
		}).appendTo(li);
		
		$('#'+ liId +' #edit_form_paragraph').click(function (e) {
			secondTab.tab('show');
			$.get('/form/input', { name: "Edit Textarea Field"}).done(function(data) {
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