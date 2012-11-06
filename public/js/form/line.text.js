var line_text = function (liId, theForm, fieldProp, uniqueId)
{
	var li = addLi(liId).appendTo(theForm);
		
	$("<label/>", {
		text: "Text",
		"for": "form_input",
	}).appendTo(li);
		
	$("<input/>", {
		type: "text",
		id: "form_input",
		placeholder: "Type something..."
	}).appendTo(li);

	formButton("delete_form_text", "Delete", "delete_li").appendTo(li);
	formButton("edit_form_text", "Edit").appendTo(li);
	
	$("<div/>", {
		"style": "clear:both;"
	}).appendTo(li);
};

var line_number = function (liId, theForm, fieldProp, uniqueId)
{
	var li = addLi(liId).appendTo(theForm);
	
	$("<label/>", {
		text: "Number",
		"for": "form_number",
	}).appendTo(li);
	
	$("<input/>", {
		type: "text",
		id: "form_number",
		placeholder: "Type something..."
	}).appendTo(li);
	
	formButton("delete_form_number", "Delete", "delete_li").appendTo(li);
	formButton("edit_form_number", "Edit").appendTo(li);
	
	$("<div/>", {
		"style": "clear:both;"
	}).appendTo(li);
};

var line_paragraph = function (liId, theForm, fieldProp, uniqueId)
{
	var li = addLi(liId).appendTo(theForm);
	
	$("<label/>", {
		text: "Paragraph",
		"for": "form_paragraph",
	}).appendTo(li);
	
	$("<textarea/>", {
		type: "text",
		id: "form_paragraph",
		placeholder: "Type something..."
	}).appendTo(li);
	
	formButton("delete_form_paragraph", "Delete", "delete_li").appendTo(li);
	formButton("edit_form_paragraph", "Edit").appendTo(li);
	
	$("<div/>", {
		"style": "clear:both;"
	}).appendTo(li);
};

var line_checkbox = function (liId, theForm, fieldProp, uniqueId)
{
	var li = addLi(liId).appendTo(theForm);
	
	$("<label/>", {
		text: "Checkboxes ",
	}).appendTo(li);
	
	var checkbox_span = $("<span/>", {
		id: "span_checkbox",
	}).appendTo(li);
	
	for (var i=0;i<=2;i++)
	{ 
		var checkbox = $("<label/>", {
			text: "Checkbox " + i,
			"class":"checkbox",
		}).appendTo(checkbox_span);
		
		$("<input/>", {
			type: "checkbox",
			id: "form_checkbox_" + i,
		}).appendTo(checkbox);
	}
	
	formButton("delete_form_checkbox", "Delete", "delete_li").appendTo(li);
	formButton("edit_form_checkbox", "Edit").appendTo(li);
	
	$("<div/>", {
		"style": "clear:both;"
	}).appendTo(li);
};

var line_radio = function (liId, theForm, fieldProp, uniqueId)
{
	var li = addLi(liId).appendTo(theForm);
	
	$("<label/>", {
		text: "Multiple Choices ",
	}).appendTo(li);
	
	var radio_span = $("<span/>", {
		id: "span_radio",
	}).appendTo(li);
	
	for (var i=0;i<=2;i++)
	{ 
		var radio = $("<label/>", {
			text: "Radio " + i,
			"class":"radio",
		}).appendTo(radio_span);
				
		$("<input/>", {
			type: "radio",
			id: "form_radio_" + i,
		}).appendTo(radio);
	}
	
	formButton("delete_form_radio", "Delete", "delete_li").appendTo(li);
	formButton("edit_form_radio", "Edit").appendTo(li);
	
	$("<div/>", {
		"style": "clear:both;"
	}).appendTo(li);
};

var line_dropdown = function (liId, theForm, fieldProp, uniqueId)
{
	var li = addLi(liId).appendTo(theForm);
	
	$("<label/>", {
		text: "Drop Down",
		"for": "form_dropdown",
	}).appendTo(li);
	
	var radio = $("<select/>", {
		"class":"dropdown",
		id: "form_dropdown_" + i,
	}).appendTo(li);
	
	for (var i=0;i<=2;i++)
	{
		$("<option/>", {
			text: "Dropdown " + i,
		}).appendTo(radio);
	}
	
	formButton("delete_form_dropdown", "Delete", "delete_li").appendTo(li);
	formButton("edit_form_dropdown", "Edit").appendTo(li);
	
	$("<div/>", {
		"style": "clear:both;"
	}).appendTo(li);
};

var addLi = function(liId){
	var list = $("<li/>", {
		"id": liId
	});
	return(list);
};

var formButton = function(editFormButton, textForm, deleteLi){
	return(
		$("<button/>", {
			type: "button",
			"class": "btn btn-link pull-right " + deleteLi,
			id: editFormButton,
			text: textForm
		})
	);
};