#
# Table structure for table 'tx_reserve_domain_model_facility'
#
CREATE TABLE tx_reserve_domain_model_facility (
   uid int(11) NOT NULL auto_increment,
   pid int(11) DEFAULT '0' NOT NULL,
   tstamp int(11) DEFAULT '0' NOT NULL,
   crdate int(11) DEFAULT '0' NOT NULL,
   cruser_id int(11) DEFAULT '0' NOT NULL,
   sys_language_uid int(11) DEFAULT '0' NOT NULL,
   l10n_parent int(11) DEFAULT '0' NOT NULL,
   l10n_diffsource mediumtext,
   l10n_source int(11) DEFAULT '0' NOT NULL,
   deleted tinyint(4) DEFAULT '0' NOT NULL,
   hidden tinyint(4) DEFAULT '0' NOT NULL,
   starttime int(11) DEFAULT '0' NOT NULL,
   endtime int(11) DEFAULT '0' NOT NULL,
   sorting int(11) DEFAULT '0' NOT NULL,

   name tinytext,

   PRIMARY KEY (uid),
   KEY parent (pid),
   KEY sys_language_uid_l10n_parent (sys_language_uid,l10n_parent)
);