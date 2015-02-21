<?php
/**
@mainpage Xataface Email Module

<img src="http://media.weblite.ca/files/photos/Screen_shot_2012-01-27_at_11.43.26_AM.png?max_width=640"/>

@section Synopsis

This module allows users to send batch emails to entire found sets of records.  It supports mail-merge also so that fields from each record can be embedded into the email, customizing each email to its respective recipient.


@section toc Table of Contents

-# @ref requirements
-# @ref license
-# @ref installation
-# @ref basic_usage
-# @ref templates
-# @ref mailmerge
-# @ref blacklists
-# @ref email_logs
-# @ref triggers
-# @ref permissions
-# @ref support

@section requirements Requirements

-# Xataface 2.0 (or SVN development trunk rev 3121 or higher)
-# The <a href="http://xataface.com/dox/modules/ckeditor/latest/">CKeditor Module</a> 0.3 or higher.
-# The <a href="http://xataface.com/dox/modules/ajax_upload/latest/">AJAX Upload Module</a> 0.1 or higher.

@section license License

This module is distributed under the <a href="http://www.gnu.org/licenses/gpl-2.0.html">GNU Public License version 2</a>

@section installation Installation


-# Install the <a href="http://xataface.com/dox/modules/ckeditor/latest/">CKeditor module</a> (used for editing the body of email messages).
-# Install the <a href="http://xataface.com/dox/modules/ajax_upload/latest/">AJAX Upload modules</a>.  (used for adding attachments to email messages).
-# Copying the Email directory into your application's (or xataface's) @e modules directory.  I.e. the path should be <em>modules/Email</em>.
-# Adding the following line to the [_modules] section of your @e conf.ini file: @code
modules_Email=modules/Email/Email.php
@endcode
-# Any tables for which you want email to be enabled must implement the 'Person' ontology.  To do this you just need to add the following section to the table's @e fields.ini file:
@code
[__implements__]
    Person=1
@endcode
You may also need to specify which field of the table stores the email address.  You can do this by adding the @c email directive to the field definition for the email field in the @e fields.ini file:
@code
[email_address]
    email=1
@endcode
-# If you want to allow attachments, create a directory to store the attachments, and make it writable by the web server.  Then add the following section to your conf.ini file: @code
[modules_Email]
	attachments=path/to/attachments
	attachments_url=url/to/attachments
@endcode


At this point, you should see an @e email action added to your list view and to the details view of your application when browsing the table.

@section basic_usage Basic Usage

Once the module has been @ref installation "installed", you can go to the list view for any table that implements the @c Person ontology and you should notice an email action appearing in the @e resultlist_actions group of actions (e.g. along side the export XML, CSV, etc.. actions).  Clicking on this action will take you to a form to send an email to all of the users in the current found set.

<img src="http://media.weblite.ca/files/photos/Screen_shot_2012-01-27_at_2.58.33_PM.png"/>

If you want to send an email to every record in the table, then just click the email link when you haven't performed any filters or searches to your found set.  If you just want to send an email to a subset of the table, then you can first perform a search on the table to narrow the results to those records that you want to send an email to, then click the @e email link.

If you only want to send an email to a single record you can click on the record to access it's details view, and click on the @e email link there. 

@subsection email_form The Email Form

After you click on the @e email link from either the details view or the list veiw, you'll see an email form as follows:

<img src="http://media.weblite.ca/files/photos/Screen_shot_2012-01-27_at_11.43.26_AM.png?max_width=640"/>

This form includes the following fields:

-# @b Template - Allows you to load a pre-written email template to start your email.  See @ref templates for more on templates.
-# @b Subject - Text field to enter the subject of the email.
-# @b From - Text field to enter the @e from email address.
-# @b Cc  - Text field to enter an optional email address to use as the bcc field for each email address.  Beware that a copy of *every* email that is sent in this batch will be sent to this address so if you're sending to 100 records, than this Bcc address will receive 100 emails - one corresponding to each email that is sent.
-# <b>Email Body</b> - A CKeditor field used to edit the HTML contents of the email.
-# <b>Ignore Blacklist</b>


@attention Before sending your message it is a good idea to review the Email addresses that it will be sent to.  You can view the selected addresses by clicking the @e "Show Email Addresses" link.

Once you have filled in your message and you are ready to send it, just click the "Send" button.


This will take you to a progress page with a progress bar.  It will show you the status of your job while it progresses.

<img src="http://media.weblite.ca/files/photos/Screen_shot_2012-01-27_at_3.00.50_PM.png?max_width=640"/>

@section templates Templates

Templates are useful for pre-designing emails that you may need to send periodically.  You can enter the @e "email body", @e from, @e cc, and @e subject fields in a template, then load them at the time that you send the emails via the @e "template" field on the email form. 

@subsection creating_new_templates Creating A New Template

-# Click on "Control Panel" in the upper right (location may vary depending on your theme).
-# Click on "Manage Email" in the control panel.
-# Click on "Manage Email Templates"
-# Click "New Record" to add a new template.  This will bring up the new record form for the templates table.  

This form contains the following fields:

-# <b>Table name</b> - A select list to choose which table the template is intended to be used with.
-# <b>Template name</b> - A text field to enter a unique name by which the template can be referenced.
-# <b>Email Subject</b> - A text field to enter a default subject that can be used by emails that use this template.
-# <b>Default From</b> - A text field to enter the default @e from address for emails sent with this template.
-# <b>Default cc</b> - A text field to enter the default @e cc address for emails sent with this template.
-# <b>Email Body</b> - A WYSIWYG HTML editor field to enter the template content.  This will be used as the default content for email messages sent with this template.
-# <b>Template Instructions</b> - Optional instructions for users who use this template.  If you have designated particular fields to be used in this template or are performing some back-end triggers upon use of this template you may want to include that here for the benefit of users.  When a user selects this template on the email form, these instructions will be displayed to the user just above the @e "email body" field.


Fill in the form and click "Save" when you are done.

@subsection embedding_mailmerge_fields Using Mailmerge Fields in Templates

It is sometimes desirable to customize emails with different information for each recipient.  This can be achieved by embedding fields in your template that will be replaced by content from the recipients' table upon sending the mail.  For example, you may want your email to begin with:

Dear {$firstname} {$lastname},

You can embed these types of fields into both templates and directly into emails using the "Insert Field" button.

In order to embed fields into a template, you must first specify which table the template will be used with using the @e "Table Name" pull-down list.  The table that you select dictate which fields you will be able to add to the template.

On the template form you may notice a button on the toolbar to insert a field  (hover over the button to see the tool-tip text).  

<img src="http://media.weblite.ca/files/photos/Screen_shot_2012-01-27_at_12.51.20_PM.png"/>

After you have selected a table in the @e "Table Name" select list, click on this button to reveal a field browser for that table as follows:

<img src="http://media.weblite.ca/files/photos/Screen_shot_2012-01-27_at_12.52.01_PM.png?max_width=640"/>

Notice that when you click on a field in this browser, a macro is added to your email template at the last caret position.  The macro will look something like:

@code
{$fieldname}
@endcode

These will be replaced with the content from the corresponding field at the time the email is sent.  Each recipient will then see custom data that is specific to them.


@subsection using_templates Using Your Template

Now that you have created a template, you can use this every time you want to send an email of this type.

To test this out, go to a table that has email enabled and try to send a new email.

On the new email form, click on the "Template ID" field and select the template that you created.

<img src="http://media.weblite.ca/files/photos/Screen_shot_2012-01-27_at_3.04.45_PM.png?max_width=640"/>

@attention If you do not see your template listed double check to make sure that your template was created for the same table as you are sending an email to this time.

After you have selected a template, you should see the "From", "CC", "Subject", and "Body" fields prepopulated with the contents you specified in your template.  You can now make changes to the content as necessary before sending the email.

When you are satisfied with your message, you can click "Send".

@section mailmerge Using Mail-merge Fields

Mail-merge is a feature that allows you to embed content from each individual record with email sent with the mailer module.  E.g. If you want to personalize each message so that a message to Steve would say "Dear Steve" and a message to Michelle would say "Dear Michelle".  To do this you would embed a macro field into your email body such as:
@code
Dear {$firstname}
@endcode

You can actually embed any field from the recipients table (i.e. the table from which the email found set is formed).

The usage for this feature on the mail form is identical to the usage on the template form see @ref embedding_mailmerge_fields for more information.


@section blacklists Opt-Out and Blacklists

Whenever you are sending bulk emails you should give your recipients the ability to opt out of your list.  The email module supports this behavior by including a link at the foot of every email sent that says:

<blockquote>If you don't want to receive email updates from us, you can opt out of our mailing list by clicking here .</blockquote>

If the user clicks on this link, it will give them the option to add their name to the "Do Not Send" list so that they won't receive any further emails from you.

<img src="http://media.weblite.ca/files/photos/Screen_shot_2012-01-27_at_3.06.09_PM.png?max_width=640"/>

Once they click this, they receive a confirmation that they have opted out of the list:

<img src="http://media.weblite.ca/files/photos/Screen_shot_2012-01-27_at_3.06.23_PM.png?max_width=640"/>

@attention It is important to keep the @c email_opt_out action accessible to the public so that users can opt out of your emails.  This is the way it works by default but if you add rules in your @c beforeHandleRequest() method that redirect users, you may want to make sure that you leave requests for the @c email_opt_out action alone.

If the user changes their mind, they can opt back in by visiting the same URL at a later time. 

<img src="http://media.weblite.ca/files/photos/Screen_shot_2012-01-27_at_3.06.36_PM.png?max_width=640"/>

By default all emails that you send will be checked against this "Do Not Send" list (or blacklist) to make sure that they haven't opted out.  If they have opted out, the email will be skipped and it will be logged so that you are aware that the email was not sent.

@subsection ignore_blacklist Ignoring the Black List

The email form includes a checkbox labelled "Ignore Black List" that, when checked, will cause your email to disregard the blacklist rules and send the email even if the address is on the black list.  It is not a good idea to abuse this ability.  If people don't want to receive email from you, you had better respect their wishes.  This override is there in case you absolutely need to force the email to get through despite the recipient's wishes.

@subsection manage_blacklist Managing the Black List

You can view the black list at any time in the email management section of the control panel.  There you can also add and remove addresses from the list manually.


<img src="http://media.weblite.ca/files/photos/Screen_shot_2012-01-27_at_3.12.58_PM.png?max_width=640"/>


@section email_logs Viewing the Email Log

The Email module logs all emails that are ever sent (unless you delete the log).  You can view the email history in the "View History" section of the email management section of the Control Panel.  

<img src="http://media.weblite.ca/files/photos/Screen_shot_2012-01-27_at_3.14.27_PM.png?max_width=640"/>

This section shows a list of all of the past email messages that were sent along with statistics to show how many messages where sent as part of the batch, how many succeeded, how many failed, and how many were cancelled due to the blacklist.

Clicking on a row will reveal more details about the mailout.

<img src="http://media.weblite.ca/files/photos/Screen_shot_2012-01-27_at_3.14.43_PM.png?max_width=640"/>

Each history details record also contains a log of each individual email that was sent. 

<img src="http://media.weblite.ca/files/photos/Screen_shot_2012-01-27_at_3.14.52_PM.png?max_width=640"/>


This log can also be accessed immediately after sending an email by clicking on the "View Log Details" link at the foot of the progress screen.

@section triggers Delegate Class Triggers

This module supports some callbacks in both the Application Delegate class and the table delegate class.  See the documentation
for those classes for more details.

-# @ref ApplicationDelegate
-# @ref TableDelegate

@section configuration Configuration Directives

Every email that is sent has an opt-out message and link appended to the end of the email so that the recipient is able to opt out of your mailing list.  You can override this default message to a custom message by either defining some directives in the conf.ini file or by implementing some delegate class methods.

To override the opt-out message in an HTML message you should define the @c opt_out_html directive in the @e modules_Email section of the conf.ini file.

To override the opt-out message in a Plain-text message, you should define the @c opt_out_text directive in the @e modules_Email section of the conf.ini file.

Both of these directives should contain a single variable $url which will be replaced by the link to opt out of the mailing list at run-time:

<h3>Example</h3>

@code
[modules_Email]
    opt_out_html = "<hr/><p>Click <a href='$url'>here</a> to opt out of our list.</p>"
    opt_out_text = "\r\n\r\n----------------------\r\n To opt out, go to $url \r\n"
    
@endcode



@section permissions Relevant Permissions

In order to access the email function a user needs to be granted the @c email permission on the table/record  of the recipients table - and at the application level. 

This permission covers most of the functions that need to be performed as part of the email module.

The @c "view schema" permission is required to allow the user to use the embed macro feature.  It is a good idea to limit this feature to administrators that you trust as this provides detailed information about the fields that are contained in your database.

@section support Support

@see http://xataface.com/forum



*/
?>