$(document).ready(function()
{
	$('#add_form_text').live("click", function(e){
		e.preventDefault();
		
		var formId = $('#add_form_data');
		var formField = formId.attr('class');
		var formJson = formId.serializeFormJSON();
		
		$('#' + formField + ' label').text(formJson.text_label);
		$('#' + formField + ' input#form_input').attr("placeholder", formJson.placeholder_text);
		
		var hasAttr = $('#' + formField + ' input').attr("required");
			
		if(formJson.required === 'yes'){
			if(hasAttr === undefined ){
				$('#' + formField + ' input#form_input').attr("required", "true");
			}
			$('#' + formField + ' input[name="required"]').attr("value", "1");
		}
		
		if(formJson.required === 'no'){
			if(hasAttr == 'required' ){
				$('#' + formField + ' input#form_input').removeAttr("required");
			}
			$('#' + formField + ' input[name="required"]').attr("value", "0");
		}
	});

});