<?php
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
/**
 * @brief A Dataface_FormTool wrapper for building datepicker widgets in Dataface_QuickForm forms.
 *
 * All widget types require a wrapper of this kind to implement the glue between the field and the 
 * database records.  This particular wrapper only implements the buildWidget() method but
 * it could also implement pushValue() and pullValue() methods to define how data should be treated
 * when passing between Dataface_RecordObjects and the HTML_QuickForm widget.
 *
 * Note that the modules_datepicker class actually registers this class with Dataface_FormTool so that
 * it knows of its existence.
 *
 */
class Dataface_FormTool_datepicker {

	/**
	 * Defines how a datepicker widget should be built.
	 *
	 * @param Dataface_Record $record The Dataface_Record that is being edited.
	 * @param array &$field The field configuration data structure that the widget is being generated for.
	 * @param HTML_QuickForm The form to which the field is to be added.
	 * @param string $formFieldName The name of the field in the form.
	 * @param boolean $new Whether this widget is being built for a new record form.
	 * @return HTML_QuickForm_element The element that can be added to a form.
	 *
	 */
	function &buildWidget($record, &$field, $form, $formFieldName, $new=false){
		$mt = Dataface_ModuleTool::getInstance();
		$mod = $mt->loadModule('modules_datepicker');
	
		$widget =& $field['widget'];
		if ( !@$widget['format'] ){
			$widget['format'] = 'yy-mm-dd';
		}
		$factory =& Dataface_FormTool::factory();
		$atts = array(
			'class' => 'xf-datepicker',
			'data-xf-date-format' => $widget['format'],
			'df:cloneable'=>1
		);
		
		
		
		
		$jt = Dataface_JavascriptTool::getInstance();
		$jt->addPath(dirname(__FILE__).'/js', $mod->getBaseURL().'/js');
		
		$ct = Dataface_CSSTool::getInstance();
		$ct->addPath(dirname(__FILE__).'/css', $mod->getBaseURL().'/css');
		
		// Add our javascript
		$jt->import('xataface/widgets/datepicker.js');
		
		$el =& $factory->addElement('text', $formFieldName, $widget['label'], $atts);
		if ( PEAR::isError($el) ) return $el;
		$el->setProperties($widget);
		
		
		
	
		return $el;
	}
	

}