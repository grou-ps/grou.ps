-- phpMyAdmin SQL Dump
-- version 2.11.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 26, 2008 at 10:56 PM
-- Server version: 4.1.20
-- PHP Version: 4.3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `gnippetsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE IF NOT EXISTS `admins` (
  `admin_id` bigint(20) NOT NULL auto_increment,
  `member_id` bigint(20) NOT NULL default '0',
  `date` date NOT NULL default '0000-00-00',
  `is_founder` set('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `acceptation_num` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`admin_id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8511 ;

-- --------------------------------------------------------

--
-- Table structure for table `all_stuff`
--

CREATE TABLE IF NOT EXISTS `all_stuff` (
  `stuff_id` bigint(20) NOT NULL auto_increment,
  `stuff_type` enum('health','business','love','profile') collate utf8_unicode_ci NOT NULL default 'health',
  `member_id` bigint(20) NOT NULL default '0',
  `content` text collate utf8_unicode_ci NOT NULL,
  `link_id` bigint(20) NOT NULL default '0',
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`stuff_id`),
  KEY `stuff_type` (`stuff_type`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9212 ;

-- --------------------------------------------------------

--
-- Table structure for table `analytics`
--

CREATE TABLE IF NOT EXISTS `analytics` (
  `analysis_id` bigint(20) NOT NULL auto_increment,
  `type` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `member_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `client_ip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `opt_obj_1` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `opt_obj_2` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `opt_obj_3` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `opt_obj_4` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `opt_obj_5` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `opt_obj_6` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `opt_obj_7` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `opt_obj_8` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `opt_obj_9` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `opt_obj_10` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`analysis_id`),
  KEY `type` (`type`),
  KEY `member_name` (`member_name`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=112 ;

-- --------------------------------------------------------

--
-- Table structure for table `cache_imgsize`
--

CREATE TABLE IF NOT EXISTS `cache_imgsize` (
  `ser` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `width` bigint(20) NOT NULL default '0',
  `height` bigint(20) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` bigint(20) NOT NULL auto_increment,
  `author_id` bigint(20) NOT NULL default '0',
  `member_id` bigint(20) NOT NULL default '0',
  `comment` text collate utf8_unicode_ci NOT NULL,
  `added_on` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=177 ;

-- --------------------------------------------------------

--
-- Table structure for table `extra_modules`
--

CREATE TABLE IF NOT EXISTS `extra_modules` (
  `module_id` bigint(20) NOT NULL auto_increment,
  `title` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `weight` smallint(5) unsigned NOT NULL default '0',
  `link` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `type` enum('internal','external') collate utf8_unicode_ci NOT NULL default 'internal',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `gnippet_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`module_id`),
  KEY `gnippet_id` (`gnippet_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `global_messages`
--

CREATE TABLE IF NOT EXISTS `global_messages` (
  `gmid` int(11) NOT NULL auto_increment,
  `title` varchar(250) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `message` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `language` varchar(100) NOT NULL default '',
  `added` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`gmid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `gnippets_ads`
--

CREATE TABLE IF NOT EXISTS `gnippets_ads` (
  `group_id` bigint(20) NOT NULL default '0',
  `adcode` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `t` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `group_defaults`
--

CREATE TABLE IF NOT EXISTS `group_defaults` (
  `gid` bigint(20) NOT NULL default '0',
  `module` varchar(100) NOT NULL default '',
  `upd` datetime NOT NULL default '0000-00-00 00:00:00',
  KEY `gid` (`gid`,`upd`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `internal_messages`
--

CREATE TABLE IF NOT EXISTS `internal_messages` (
  `msg_id` bigint(20) NOT NULL auto_increment,
  `from` bigint(20) NOT NULL default '0',
  `to` bigint(20) NOT NULL default '0',
  `msg` text collate utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `in_reply_to` bigint(20) NOT NULL default '0',
  `serial` varchar(26) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`msg_id`),
  KEY `to` (`to`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=304 ;

-- --------------------------------------------------------

--
-- Table structure for table `maillogs`
--

CREATE TABLE IF NOT EXISTS `maillogs` (
  `log_id` bigint(20) NOT NULL auto_increment,
  `subject` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `to_addr` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `sent_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `random_1` varchar(10) collate utf8_unicode_ci NOT NULL default '',
  `random_2` varchar(10) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mail_message`
--

CREATE TABLE IF NOT EXISTS `mail_message` (
  `message_id` bigint(20) NOT NULL auto_increment,
  `group_id` bigint(20) NOT NULL default '0',
  `message` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `add_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`message_id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE IF NOT EXISTS `members` (
  `member_id` bigint(20) NOT NULL auto_increment,
  `member_login` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `member_password` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `email` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `subscription_date` date NOT NULL default '0000-00-00',
  `openid` set('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  PRIMARY KEY  (`member_id`),
  KEY `member_login` (`member_login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=52178 ;

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

CREATE TABLE IF NOT EXISTS `memberships` (
  `membership_id` bigint(20) NOT NULL auto_increment,
  `member_id` bigint(20) NOT NULL default '0',
  `subscribed_on` date NOT NULL default '0000-00-00',
  `member_name` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `website` varchar(120) collate utf8_unicode_ci NOT NULL default '',
  `blog` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `flickr` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `delicious` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `avatar` tinyint(4) NOT NULL default '0',
  `membership_name` varchar(75) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`membership_id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=107324 ;

-- --------------------------------------------------------

--
-- Table structure for table `membership_requests`
--

CREATE TABLE IF NOT EXISTS `membership_requests` (
  `request_id` bigint(20) NOT NULL auto_increment,
  `gnippet_id` bigint(20) NOT NULL default '0',
  `member_id` bigint(20) NOT NULL default '0',
  `profile_id` bigint(20) NOT NULL default '0',
  `member_name` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `comment` text collate utf8_unicode_ci NOT NULL,
  `opening_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`request_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21004 ;

-- --------------------------------------------------------

--
-- Table structure for table `migration_attempt`
--

CREATE TABLE IF NOT EXISTS `migration_attempt` (
  `attempt_id` bigint(20) NOT NULL auto_increment,
  `member_id` bigint(20) NOT NULL default '0',
  `group_id` bigint(20) NOT NULL default '0',
  `migration_type` set('yahoo') collate utf8_unicode_ci NOT NULL default '',
  `username` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `password` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `groupname` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `attempt_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`attempt_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `password_recovery`
--

CREATE TABLE IF NOT EXISTS `password_recovery` (
  `recovery_id` bigint(20) NOT NULL auto_increment,
  `email` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `serial` varchar(12) collate utf8_unicode_ci NOT NULL default '',
  `date` timestamp NOT NULL default '0000-00-00 00:00:00',
  `recovered` set('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `recovery_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`recovery_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=523 ;

-- --------------------------------------------------------

--
-- Table structure for table `person_watch`
--

CREATE TABLE IF NOT EXISTS `person_watch` (
  `watch_id` bigint(20) NOT NULL auto_increment,
  `group_id` bigint(20) NOT NULL default '0',
  `watcher_id` bigint(20) NOT NULL default '0',
  `watched_id` bigint(20) NOT NULL default '0',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`watch_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pins`
--

CREATE TABLE IF NOT EXISTS `pins` (
  `pin_id` bigint(20) NOT NULL auto_increment,
  `member_id` bigint(20) NOT NULL default '0',
  `group_id` bigint(20) NOT NULL default '0',
  `pin` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`pin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1104 ;

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE IF NOT EXISTS `profiles` (
  `profile_id` bigint(20) NOT NULL auto_increment,
  `member_id` bigint(20) NOT NULL default '0',
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `favorite_songs` text collate utf8_unicode_ci NOT NULL,
  `favorite_singers` text collate utf8_unicode_ci NOT NULL,
  `favorite_movies` text collate utf8_unicode_ci NOT NULL,
  `favorite_actors` text collate utf8_unicode_ci NOT NULL,
  `favorite_books` text collate utf8_unicode_ci NOT NULL,
  `favorite_authors` text collate utf8_unicode_ci NOT NULL,
  `favorite_sportsmen` text collate utf8_unicode_ci NOT NULL,
  `favorite_artists` text collate utf8_unicode_ci NOT NULL,
  `favorite_cities` text collate utf8_unicode_ci NOT NULL,
  `favorite_colors` text collate utf8_unicode_ci NOT NULL,
  `motto` text collate utf8_unicode_ci NOT NULL,
  `adored_people` text collate utf8_unicode_ci NOT NULL,
  `birthday` date NOT NULL default '0000-00-00',
  `religion` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `nationality` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `second_nationality` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `ethnic_race` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `marital_status` set('married','never_married','divorced') collate utf8_unicode_ci NOT NULL default '',
  `children` int(11) NOT NULL default '0',
  `sexe` set('male','female') collate utf8_unicode_ci NOT NULL default '',
  `sexual_orientation` set('heterosexual','homosexual','bisexual','asexual','other','no_answer') collate utf8_unicode_ci NOT NULL default '',
  `occupation` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `hobbies` text collate utf8_unicode_ci NOT NULL,
  `fobbies` text collate utf8_unicode_ci NOT NULL,
  `contact_aim` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `contact_yahoo` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `contact_icq` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `contact_jabber` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `contact_msn` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `tags` text collate utf8_unicode_ci NOT NULL,
  `show_tags` set('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `show_favourites` set('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `show_lovestuff` set('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `show_businessstuff` set('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `show_healthstuff` set('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  PRIMARY KEY  (`profile_id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=18134 ;

-- --------------------------------------------------------

--
-- Table structure for table `quotes`
--

CREATE TABLE IF NOT EXISTS `quotes` (
  `quote_id` bigint(20) NOT NULL auto_increment,
  `member_id` bigint(20) NOT NULL default '0',
  `quote` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `added_on` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`quote_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=383 ;

-- --------------------------------------------------------

--
-- Table structure for table `talks_mailgroups`
--

CREATE TABLE IF NOT EXISTS `talks_mailgroups` (
  `mailgroup_id` bigint(20) NOT NULL auto_increment,
  `gnippet_id` bigint(20) NOT NULL default '0',
  `mailgroup` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`mailgroup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=194 ;

-- --------------------------------------------------------

--
-- Table structure for table `talks_messages`
--

CREATE TABLE IF NOT EXISTS `talks_messages` (
  `msg_id` bigint(20) NOT NULL auto_increment,
  `clean` set('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `gnippet_id` bigint(20) NOT NULL default '0',
  `subject` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `message` text collate utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `in_reply_to` bigint(20) NOT NULL default '0',
  `mail_id` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `mail_sender` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `sender_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`msg_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1332 ;

-- --------------------------------------------------------

--
-- Table structure for table `watchlist`
--

CREATE TABLE IF NOT EXISTS `watchlist` (
  `watch_id` bigint(20) NOT NULL auto_increment,
  `group_id` bigint(20) NOT NULL default '0',
  `subject_id` bigint(20) NOT NULL default '0',
  `object_id` bigint(20) NOT NULL default '0',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`watch_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=54 ;

-- --------------------------------------------------------

--
-- Table structure for table `wiki`
--

CREATE TABLE IF NOT EXISTS `wiki` (
  `page_id` bigint(20) NOT NULL auto_increment,
  `overrides_id` bigint(20) NOT NULL default '0',
  `replicates_id` bigint(20) NOT NULL default '0',
  `author_id` bigint(20) NOT NULL default '0',
  `gnippet_id` bigint(20) NOT NULL default '0',
  `page_title` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `page_content` longtext collate utf8_unicode_ci NOT NULL,
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_locked` set('Y','N') collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`page_id`),
  KEY `gnippet_id` (`gnippet_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9446 ;

-- --------------------------------------------------------

--
-- Table structure for table `write_permissions`
--

CREATE TABLE IF NOT EXISTS `write_permissions` (
  `group_id` bigint(20) NOT NULL default '0',
  `module` enum('blogs','map','wiki','photos','links','talks') NOT NULL default 'blogs',
  `res` enum('everyone','members','admins') NOT NULL default 'members',
  KEY `group_id` (`group_id`),
  KEY `module` (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

