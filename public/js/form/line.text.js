
var line_text = function (uniqueId, theForm, secondTab, fieldProp)
{
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

		uniqueId++;
};