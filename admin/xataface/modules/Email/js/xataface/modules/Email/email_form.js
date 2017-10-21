//require <xataface/IO.js>
//require <xatajax.form.core.js>
//require-css <xataface/modules/Email/email_form.css>

(function(){
	var $ = jQuery;
	$(document).ready(function(){
	
		CKEDITOR.plugins.registered['save']=
		{
		 init : function( editor )
		 {
			var command = editor.addCommand( 'save',
			   {
				  modes : { wysiwyg:1, source:1 },
				  exec : function( editor ) {
					 alert("This button is disabled");
				  }
			   }
			);
			editor.ui.addButton( 'Save',{label : 'YOUR LABEL',command : 'save'});
		 }
		}
	
	
		$('#new_xataface__email_newsletters_record_form').submit(function(){
			return confirm('Send email messages now?');
		});
		
		/**
		 * @class Email
		 * @memberOf xataface.modules
		 * @description Utility functions for the Email module.
		 */
		var Email = XataJax.load('xataface.modules.Email');
		Email.loadTemplate = loadTemplate;
		
		var IO = XataJax.load('xataface.IO');
		
		
		/**
		 * @function
		 * @memberOf xataface.modules.Email
		 * @description
		 * Loads the template content from the templateId that has been specified
		 * in the template id field.  
		 *
		 * <p>This is intended to be used only on the new and edit record forms for
		 * the email_newsletters table.  It should be registered as an <code>onchange</code>
		 * handler for the template_id field.  This registration is via the <code>widget:atts:onchage</code>
		 * directive in the fields.ini file.
		 * </p>
		 *
		 * @param {HTMLElement} The <code>template_id</code> field in the form.
		 */
		function loadTemplate(templateIdField){
			var templateId = $(templateIdField).val();
			if ( !templateId ) return;
			var recordId = 'xataface__email_templates?template_id='+templateId;
			IO.load(recordId, function(res){
				res = res[0];
				$('#template-instructions').text(res.template_instructions);
				var contentField = XataJax.form.findField(templateIdField, 'content');
				if ( contentField ){
					CKEDITOR.instances.content.setData(res.email_body);
					//$(contentField).val(res.email_body);
				} else {
					alert("no content field");
				}
				
				var subjectField = XataJax.form.findField(templateIdField, 'subject');
				if(  subjectField ){
					$(subjectField).val(res.email_subject);
				} else {
					alert("No subject field");
				}
				
				var fromField = XataJax.form.findField(templateIdField, 'from');
				if ( fromField ){
					$(fromField).val(res.email_from);
				}
				
				var ccField = XataJax.form.findField(templateIdField, 'cc');
				if ( ccField ){
					$(ccField).val(res.email_cc);
				}
				
				
				
			});
			
		
		}
	});
	

})();