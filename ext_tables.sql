#
# Table structure for table 'tx_reserve_domain_model_facility'
#
CREATE TABLE tx_reserve_domain_model_facility (
	name tinytext,
	short_name tinytext,
	periods int(11) DEFAULT '0' NOT NULL,
	from_name tinytext,
	from_email tinytext,
	reply_to_name tinytext,
	reply_to_email tinytext,
	confirmation_mail_subject tinytext,
	confirmation_mail_html text,
	reservation_mail_subject tinytext,
	reservation_mail_html text,
	qr_code_size int(11) unsigned DEFAULT '350' NOT NULL,
	qr_code_label_size int(11) unsigned DEFAULT '16' NOT NULL,
	qr_code_logo int(11) unsigned DEFAULT '0' NOT NULL,
	qr_code_logo_width int(11) unsigned DEFAULT '150' NOT NULL,
	cancelable tinyint(4) DEFAULT '0' NOT NULL,
	cancelable_until_minutes int(11) unsigned DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'tx_reserve_domain_model_period'
#
CREATE TABLE tx_reserve_domain_model_period (
	facility int(11) DEFAULT '0' NOT NULL,
	date int(11) DEFAULT '0' NOT NULL,
	begin int(11) DEFAULT '0' NOT NULL,
	end int(11) DEFAULT '0' NOT NULL,
	max_participants int(11) DEFAULT '0' NOT NULL,
	max_participants_per_order int(11) DEFAULT '0' NOT NULL,
	booking_begin int(11) DEFAULT '0' NOT NULL,
	booking_end int(11) DEFAULT '0',
	orders int(11) DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'tx_reserve_domain_model_order'
#
CREATE TABLE tx_reserve_domain_model_order (
	booked_period int(11) DEFAULT '0' NOT NULL,
	activated tinyint(4) DEFAULT '0' NOT NULL,
	first_name varchar(64) DEFAULT '' NOT NULL,
	last_name varchar(64) DEFAULT '' NOT NULL,
	activation_code varchar(64) DEFAULT '' NOT NULL,
	email tinytext,
	phone tinytext,
	address tinytext,
	zip varchar(10) DEFAULT '' NOT NULL,
	city tinytext,
	organization tinytext,
	remarks tinytext,
	reservations int(11) DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'tx_reserve_domain_model_reservation'
#
CREATE TABLE tx_reserve_domain_model_reservation (
	customer_order int(11) DEFAULT '0' NOT NULL,
	first_name varchar(64) DEFAULT '' NOT NULL,
	last_name varchar(64) DEFAULT '' NOT NULL,
	code varchar(64) DEFAULT '0' NOT NULL,
	used tinyint(4) DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'tx_reserve_domain_model_email'
#
CREATE TABLE tx_reserve_domain_model_email (
	subject tinytext,
	body text,
	receiver_type int(11) DEFAULT '0' NOT NULL,
	from_name tinytext,
	from_email tinytext,
	reply_to_name tinytext,
	reply_to_email tinytext,
	custom_receivers text,
	periods text,
	locked int(11) DEFAULT '0' NOT NULL,
	command_data mediumtext,
);
