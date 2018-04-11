/* <div>
	<label class="required">
		__name__label__
	</lavel>
	<div id="comment_files__name__">
	
	</div>*/


function addFileInput() {
	var prototype = $('#comment_files').data('prototype');
	var count = $('#comment_files > .form-group').length; //replace .form-group with div without bootstrap
	

	
	var newForm = prototype.replace(/__name__/g, count);
	
	var group = $('.form-group', newForm); //replace .form-group with input and add: .parent() after ): ).parent()
	
	$('#comment_files').append(group);
}
	

var button = $('<button>Add file</button>');
button.addClass('btn btn-success'); //remove this line with bootstrap
button.attr('type', 'button'); 
button.on('click', addFileInput);



$("#comment_files").after(button);


