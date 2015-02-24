//require <jquery.packed.js>
(function(){
	var $ = jQuery;
	
	/*
	 * Attach a listener to the table_name field on the edit record form so 
	 * that the ckeditor's schemabrowser plugin will work on the correct table.
	 */
	$('#table_name').change(function(){

		CKEDITOR.instances.email_body.element.setAttribute('data-xf-schemabrowser-tablename', $(this).val());
	});

})();