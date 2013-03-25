#
# +---------------------------------------------------------------------+
# | phpOpenTracker - The Website Traffic and Visitor Analysis Solution  |
# +---------------------------------------------------------------------+
# | Copyright (c) 2000-2003 Sebastian Bergmann. All rights reserved.    |
# +---------------------------------------------------------------------+
# | This source file is subject to the phpOpenTracker Software License, |
# | Version 1.0, that is bundled with this package in the file LICENSE. |
# | If you did not receive a copy of this file, you may either read the |
# | license online at http://phpOpenTracker.de/license/1_0.txt, or send |
# | a note to license@phpOpenTracker.de, so we can mail you a copy.     |
# +---------------------------------------------------------------------+
# | Author: Sebastian Bergmann <sebastian@phpOpenTracker.de>            |
# +---------------------------------------------------------------------+
#
# $Id: mysql.sql,v 1.1 2003/09/01 17:19:33 sven_weih Exp $
#

DROP TABLE IF EXISTS pot_add_data;
DROP TABLE IF EXISTS pot_accesslog;
DROP TABLE IF EXISTS pot_documents;
DROP TABLE IF EXISTS pot_exit_targets;
DROP TABLE IF EXISTS pot_hostnames;
DROP TABLE IF EXISTS pot_operating_systems;
DROP TABLE IF EXISTS pot_referers;
DROP TABLE IF EXISTS pot_user_agents;
DROP TABLE IF EXISTS pot_visitors;

CREATE TABLE pot_accesslog (
  accesslog_id      INT(11)                NOT NULL,
  timestamp         INT(10)    UNSIGNED    NOT NULL,
  document_id       INT(11)                NOT NULL,
  exit_target_id    INT(11)    DEFAULT '0' NOT NULL,
  entry_document    TINYINT(3) UNSIGNED    NOT NULL,

  KEY accesslog_id   (accesslog_id),
  KEY timestamp      (timestamp),
  KEY document_id    (document_id),
  KEY exit_target_id (exit_target_id)
) DELAY_KEY_WRITE=1;

CREATE TABLE pot_add_data (
  accesslog_id INT(11)      NOT NULL,
  data_field   VARCHAR(32)  NOT NULL,
  data_value   VARCHAR(255) NOT NULL,

  KEY accesslog_id (accesslog_id)
) DELAY_KEY_WRITE=1;

CREATE TABLE pot_documents (
  data_id      INT(11)      NOT NULL,
  string       VARCHAR(255) NOT NULL,
  document_url VARCHAR(255) NOT NULL,

  PRIMARY KEY (data_id)
) DELAY_KEY_WRITE=1;

CREATE TABLE pot_exit_targets (
  data_id INT(11)      NOT NULL,
  string  VARCHAR(255) NOT NULL,

  PRIMARY KEY (data_id)
) DELAY_KEY_WRITE=1;

CREATE TABLE pot_hostnames (
  data_id INT(11)      NOT NULL,
  string  VARCHAR(255) NOT NULL,

  PRIMARY KEY (data_id)
) DELAY_KEY_WRITE=1;

CREATE TABLE pot_operating_systems (
  data_id INT(11)      NOT NULL,
  string  VARCHAR(255) NOT NULL,

  PRIMARY KEY (data_id)
) DELAY_KEY_WRITE=1;

CREATE TABLE pot_referers (
  data_id INT(11)      NOT NULL,
  string  VARCHAR(255) NOT NULL,

  PRIMARY KEY (data_id)
) DELAY_KEY_WRITE=1;

CREATE TABLE pot_user_agents (
  data_id INT(11)      NOT NULL,
  string  VARCHAR(255) NOT NULL,

  PRIMARY KEY (data_id)
) DELAY_KEY_WRITE=1;

CREATE TABLE pot_visitors (
  accesslog_id        INT(11)             NOT NULL,
  visitor_id          INT(11)             NOT NULL,
  client_id           INT(10)    UNSIGNED NOT NULL,
  operating_system_id INT(11)             NOT NULL,
  user_agent_id       INT(11)             NOT NULL,
  host_id             INT(11)             NOT NULL,
  referer_id          INT(11)             NOT NULL,
  timestamp           INT(10)    UNSIGNED NOT NULL,
  returning_visitor   TINYINT(3) UNSIGNED NOT NULL,

  PRIMARY KEY         (accesslog_id),
  KEY client_time     (client_id, timestamp)
) DELAY_KEY_WRITE=1;
