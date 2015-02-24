/*
 * Xataface Datepicker Module
 * Copyright (C) 2011  Steve Hannah <steve@weblite.ca>
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Library General Public
 * License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Library General Public License for more details.
 * 
 * You should have received a copy of the GNU Library General Public
 * License along with this library; if not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor,
 * Boston, MA  02110-1301, USA.
 *
 */

//require <jquery.packed.js>
//require <jquery-ui.min.js>
//require-css <jquery-ui/jquery-ui.css>

(function(){
	var $ = jQuery;
	
	/**
	 * When defining the javascript for a widget, we always wrap it in
	 * registerXatafaceDecorator so that it will be run whenever any new content is
	 * loaded ino the page.  This makes it compatible with the grid widget.
	 *
	 * If you don't do this, the widget will only be installed on widgets at page load time
	 * so when new rows are added via the grid widget, the necessary javascript won't be installed
	 * on those widgets.
	 */
	registerXatafaceDecorator(function(node){
		// node is the root node that is being decorated.  All
		// queries should be done relative to this node.
		
		$('input.xf-datepicker', node).each(function(){
			$(this).datepicker({
				dateFormat: ''+$(this).attr('data-xf-date-format'),
				altFormat: ''+$(this).attr('data-xf-date-format')
			});
			
		});
		
		
	
	});

})();