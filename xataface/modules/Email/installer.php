<?php
class modules_Email_installer {
	
	function update_1003(){
	
		$sql[] = "create table if not exists `xataface__email_newsletters` (
			`id` int(11) not null auto_increment,
			`subject` varchar(128) not null,
			`cc` varchar(128) default null,
			`from` varchar(128) default null,
			`template_id` int(11) default null,
			`content` text,
			`ignore_blacklist` tinyint(1) default 0,
			
			primary key (`id`))";
			
		$sql[] = "ALTER TABLE  `xataface__email_newsletters` ADD  `posted_by` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL";
		
	
		$sql[] = "CREATE TABLE IF NOT EXISTS `xataface__email_templates` (
		  `template_id` int(11) NOT NULL auto_increment,
		  `table_name` varchar(255) default NULL,
		  `template_name` varchar(255)  default NULL,
		  `email_subject` varchar(255)  default NULL,
		  `email_body` text,
		  `template_instructions` text,
		  PRIMARY KEY  (`template_id`),
		  UNIQUE KEY `template_name` (`template_name`)
		)";
		
		$sql[] = "CREATE TABLE IF NOT EXISTS `xataface__email_jobs` (
			job_id int(11) not null auto_increment primary key,
			email_id int(11),
			email_table varchar(255),
			join_table varchar(255),
			recipients_table varchar(255),
			email_column varchar(255),
			active tinyint(1) default 0,
			complete tinyint(1) default 0,
			total_emails int(11) default 0,
			sent_emails int(11) default 0,
			successful_emails int(11) default 0,
			failed_emails int(11) default 0,
			blacklisted_emails int(11) default 0,
			start_time int(11) default 0,
			end_time int(11) default 0,
			unique key (email_id)
		)";
		foreach ($sql as $q){
			try {
				df_q($q);
			} catch (Exception $ex){}
		}
		//df_q($sql);
		df_clear_views();
		df_clear_cache();
		

	}
	
	function update_1004(){
	
	
		$sql[] = "create table if not exists `xataface__email_log` (
			`log_id` int(11) not null auto_increment primary key,
			`messageid` int(11) not null,
			`recipient_email` varchar(128) not null,
			`sent` tinyint(1) default 0,
			`date_created` datetime default null,
			`date_sent` datetime default null,
			`success` tinyint(1) default null,
			`comment` varchar(255) default null,
			key (`messageid`))";
			
		try {
			df_q($sql);
		} catch (Exception $ex){}
		df_clear_views();
		df_clear_cache();
	}
	
	function update_1005(){
	
	
		$sql[] = "alter table xataface__email_log add primary_key varchar(255) after recipient_email";
			
		try {
			df_q($sql);
		} catch (Exception $ex){}
		df_clear_views();
		df_clear_cache();
	}
	
	function update_1006(){
	
	
		$sql[] = "alter table xataface__email_jobs add cancelled tinyint(1) default 0 after `active`";
			
		try {
			df_q($sql);
		} catch (Exception $ex){}
		df_clear_views();
		df_clear_cache();
	}
	
	function update_1007(){
	
	
		$sql[] = "alter table xataface__email_templates add email_from varchar(255)  after `email_subject`";
		$sql[] = "alter table xataface__email_templates add email_cc varchar(255)  after `email_from`";
			
		try {
			df_q($sql);
		} catch (Exception $ex){}
		df_clear_views();
		df_clear_cache();
	}
	
	function update_1008(){
	
	
		$sql[] = "alter table xataface__email_log add uniqid varchar(255)  after `comment`";
		$sql[] = "alter table xataface__email_log add key(`uniqid`)";	
		try {
			df_q($sql);
		} catch (Exception $ex){}
		df_clear_views();
		df_clear_cache();
	}
	
	function update_3312(){
	
	
		$sql[] = "create table if not exists xataface__email_attachments (
			attachment_id int(11) not null auto_increment primary key,
			email_id int(11),
			file varchar(255),
			key (`email_id`))";
		try {
			df_q($sql);
		} catch (Exception $ex){ echo $ex->getMessage();}
		df_clear_views();
		df_clear_cache();	
		
	}
	
	

}