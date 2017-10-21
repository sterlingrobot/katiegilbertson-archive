<?php
/**
 * @brief A false interface used to document that methods that can be implemented in 
 * the application delegate class to affect the functioning of the Email module.
 */
interface ApplicationDelegate {


	/**
	 * @brief A hook that is called just before an email is sent.  This gives you
	 * an opportunity to add or modify data that is being sent.
	 *
	 * <p>It's best not to modify any information in the @c $email or @c $template since
	 * these are loaded once per batch of emails.  Hence if you modify any of the information
	 * in them it will affect all subsequent emails sent in the same batch.</p>
	 *
	 * <p>You may want to modify data in the @c $recipient, which is used to populate
	 * macros in the email body.</p>
	 *
	 * @param Dataface_Record $email The email record that is being sent.  This includes the
	 *  email content prior to the recipient macros being filled in.  This record encapsulates
	 *  a single row from the @c xataface__email_newsletters table.  This value is never @c null.
	 *
	 * @param Dataface_Record $template The template record that was used as a basis for this
	 * email.  If no template was used then this value will be @c null.  You may want to use 
	 * the template as a marker for you to perform custom code that affects the recipient.
	 *
	 * @param Dataface_Record $recipient The recipient record.  This encapsulates a row from the
	 * entity table upon which the found set we are sending email to was formed.  This record is
	 * used to draw values for the embed macros so you can affect the values of the embed macros
	 * by modifying them in this record.
	 *
	 * @returns void
	 *
	 * @see TableDelegate::decorateEmailContext()
	 *
	 * <h3>Example</h3>
	 * <p>This example looks for a particular template, and if the email uses this
	 * template, it resets the recipient's password to 'changeme'.  Presumably
	 * this template draws on the 'password' field to send it to the recipient 
	 * as part of the email.</p>
	 *
	 * @code
	 * function decorateEmailContext(Dataface_Record $email, Dataface_Record $template, Dataface_Record $recipient){
	 *     if ( $template and $template->val('template_id') == 10 ){
	 *         // template with id=10 is the reset password email.
	 *         // Let's reset the recipient's password and store the new
	 *         // password in a calculated field that we created for this purpose.
	 *         $recipient->setValue('password', 'changeme');
	 *         $recipient->save();
	 *     }
	 * }
	 * @endcode
	 *
	 * @since 0.3
	 */
	public function decorateEmailContext( 	Dataface_Record $email, 
											Dataface_Record $template, 
											Dataface_Record $recipient
										);
										
										
	
	/**
	 * @brief A hook that can optionally override the "opt-out" message that is added at the end
	 * of an email.
	 *
	 * <p>If this is not defined then a default message will be used with a link to the page
	 * where the user can opt out of the email list.</p>
	 *
	 * @param Dataface_Record $recipient The recipient record where the email is being sent.
	 * @param string $url The URL to the opt-out form.
	 * @returns array An associative array with one or more of the following keys: @code
	 *	html  :  <String>   // The HTML version of the message.
	 *	text  :  <String>   // The plain text version of the message
	 * @endcode
	 *
	 * <h3>Example</h3>
	 *
	 * @code
	 * function getEmailOptOutMessage(Dataface_Record $recipient, $url){
	 *
	 *     return array(
	 *         'html' => '<hr/><p>Click <a href="'.$url.'">here</a> to opt out of our list.</p>',
	 *         'text' => "\r\n\r\n----------------------\r\n To opt out, go to $url \r\n"
	 *     );
	 * }
	 * @endcode
	 *
	 * <h3>Default Opt Out Messages</h3>
	 *
	 * <p>Note that you can override the default opt-out message in the conf.ini file also using
	 * the @ref opt_out_html or @ref opt_out_text directives in the @e modules_Email section.</p>
	 *
	 * @see TableDelegate::getEmailOptOutMessage()
	 * @since 0.3
	 */
	public function getEmailOptOutMessage(	Dataface_Record $recipient,
											$url
										);
										
										
	/**
	 * @brief Checks whether a particular Email address is blacklisted.  This will complement
	 * or supplement the existing blacklist table check depending on the return value of this
	 * method.
	 *
	 * <p>If it returns a boolean value (true or false), then this value will be treated
	 * as definitive (i.e. will completely override the built-in blacklist).  If it returns
	 * any other value (e.g. 1, 0, null, ", etc...), then the result is ignored, and the standard
	 * blacklist is used.</p>
	 *
	 * @param String $email The email address the check against the blacklist.
	 * @returns mixed Either a boolean value (true or false) to indicate whether the email
	 * address is blacklisted, or any other value (e.g. null) to indicate that "we don't know".
	 *
	 * @since 0.3.1
	 */									
	public function Email__isBlackListed( $email );
	
	
	/**
	 * @brief Trigger fired after an email address opts into the email list.
	 * @param String $email The email address that was opted in. (i.e. added to the blacklist)
	 * @since 0.3.2
	 */
	public function Email__afterOptIn( $email );
	
	/**
	 * @brief Trigger fired after an email address opts out of the email list.
	 * @param String $email The email address that was opted out (i.e. added to the blacklist).
	 * @since 0.3.2
	 */
	public function Email__afterOptOut( $email );
	
	
	/**
	 * @brief Trigger fired after an email is attempted to be sent, but fails.
	 * @param Dataface_Record $recipient Record to which delivery was attempted.  
	 *  This will be a record of the table on which the "Send Email" option was selected.  
	 *  It should implement the Person ontology.
	 * @param Dataface_Record $email The email record from the xataface__email_newsletters
	 *  table.
	 * @returns void
	 * @since 0.3.2
	 */
    public function Email__onFail( Dataface_Record $recipient, Dataface_Record $email );


	
	/**
	 * @brief Trigger fired after an email is successfully sent.
	 * @param Dataface_Record $recipient Record that was sent to.  This will be a record
	 *  of the table on which the "Send Email" option was selected.  It should implement
	 *  the Person ontology.
	 * @param Dataface_Record $email The email record from the xataface__email_newsletters
	 *  table.
	 * @returns void
	 * @since 0.3.2
	 */
	public function Email__onSuccess( Dataface_Record $recipient, Dataface_Record $email );
	
	
	
}