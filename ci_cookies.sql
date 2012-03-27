--
-- Table structure for table `ci_cookies`
--

CREATE TABLE IF NOT EXISTS `ci_cookies` (
  `id` int(11) NOT NULL auto_increment,
  `cookie_id` varchar(255) default NULL,
  `netid` varchar(255) default NULL,
  `ip_address` varchar(255) default NULL,
  `user_agent` varchar(255) default NULL,
  `orig_page_requested` varchar(120) default NULL,
  `php_session_id` varchar(40) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

