<?php
/**
@mainpage Datepicker Module


<img src="http://media.weblite.ca/files/photos/Screen%20shot%202011-06-07%20at%2010.39.53%20AM.png?max_width=640"/>

@section synopsis Synopsis

The Datepicker module adds two date widgets for use in your applications:

-# @e datepicker - A popup calendar widget for choosing dates only.
-# @e datetimepicker - A popup calendar widget for choosing dates and times.


@section license License

@code
Xataface Datepicker Module
Copyright (c) 2011, Steve Hannah <shannah@sfu.ca>, All Rights Reserved

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Library General Public
License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Library General Public License for more details.

You should have received a copy of the GNU Library General Public
License along with this library; if not, write to the
Free Software Foundation, Inc., 51 Franklin St, Fifth Floor,
Boston, MA  02110-1301, USA.
@endcode

@section requirements Requirements

Xataface 1.4 or higher

@section download Download

@subsection packages Packages

None yet

@subsection svn SVN

<a href="http://weblite.ca/svn/dataface/modules/datepicker/trunk">http://weblite.ca/svn/dataface/modules/datepicker/trunk</a>

@section installation Installation


-# Copy the datepicker directory into your modules directory. i.e.: @code
modules/datepicker
@endcode
-# Add the following to the [_modules] section of your app's conf.ini @code
modules_datepicker=modules/datepicker/datepicker.php
@endcode

@see http://xataface.com/wiki/modules For more information about Xataface module development and installation.

@section usage Usage

@subsection datepickerwidget The @c datepicker Widget

Set any date field to use the @c datepicker widget for editing.  E.g. field
definition in the fields.ini file:
@code
[mydatefield]
    widget:type=datepicker
@endcode

Then your field will use the datepicker widget:

<img src="http://media.weblite.ca/files/photos/Screen%20shot%202011-06-07%20at%2010.39.53%20AM.png?max_width=640"/>

@see http://jqueryui.com/demos/datepicker/ For more information about the datepicker widget.

@subsection datetimepickerwidet The @c datetimepicker Widget

Set any datetime field to use the @c datetimepicker widget for editing.  E.g. field definition in the fields.ini file:
@code
[mydatetimefield]
	widget:type=datetimepicker
@endcode

Then your field will use the @c datetimepicker widget.

@see http://www.projectcodegen.com/JQueryDateTimePicker.aspx For more information about the datetime picker widget.


@section configoptions Configuration Options

Both the @datepicker and @c datetimepicker widgets can take configuration options to customize their behavior.  These options can be specified in the fields.ini file using the @c widget: prefix.  E.g:

@code
widget:dateFormat = "yy/mm/dd"
@endcode

@see http://jqueryui.com/demos/datepicker/ For a full list of options for the @c datepicker.
@see http://www.projectcodegen.com/JQueryDateTimePicker.aspx For a full list of options for the @c datetimepicker.


@see http://xataface.com/wiki/fields.ini_file For more information about fields.ini file directives.
@see http://xataface.com/wiki/widget%3Atype For more information about the widget:type directive.

@section more More Reading

TBA


@section support Support

<a href="http://xataface.com/forum">Xataface Forum</a>




*/
?>