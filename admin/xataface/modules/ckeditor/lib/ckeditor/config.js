/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	 config.toolbar_Basic = [

        { name: 'document',    items : [ 'Source','Find','Replace'] },

        { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike'] },

        { name: 'styles',      items : [ 'Font','FontSize' ] },

        { name: 'colors',      items : [ 'TextColor','BGColor' ] },

        { name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent'] },

        { name: 'links',       items : [ 'Link','Unlink' ] },

        { name: 'insert',       items : [ 'Table' ] },

        { name: 'tools',       items : [ 'Maximize'] }

    ];

};
