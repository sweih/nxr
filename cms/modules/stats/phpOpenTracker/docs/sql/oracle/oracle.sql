--
-- +---------------------------------------------------------------------+
-- | phpOpenTracker - The Website Traffic and Visitor Analysis Solution  |
-- +---------------------------------------------------------------------+
-- | Copyright (c) 2000-2003 Sebastian Bergmann. All rights reserved.    |
-- +---------------------------------------------------------------------+
-- | This source file is subject to the phpOpenTracker Software License, |
-- | Version 1.0, that is bundled with this package in the file LICENSE. |
-- | If you did not receive a copy of this file, you may either read the |
-- | license online at http://phpOpenTracker.de/license/1_0.txt, or send |
-- | a note to license@phpOpenTracker.de, so we can mail you a copy.     |
-- +---------------------------------------------------------------------+
-- | Author: Thomas Fromm <tf@tfromm.com>                                |
-- +---------------------------------------------------------------------+
--
-- $Id: oracle.sql,v 1.1 2003/09/01 17:19:33 sven_weih Exp $
--

CREATE TABLE pot_accesslog (
  accesslog_id number(11) NOT NULL,
  document_id  number(11) NOT NULL,
  timestamp    number(10) NOT NULL,
  exit_target_id number(11) DEFAULT '0',
  entry_document number(1) NOT NULL
);

CREATE INDEX pot_accesslog_accesslog_id   ON pot_accesslog(accesslog_id);
CREATE INDEX pot_accesslog_timestamp      ON pot_accesslog(timestamp);
CREATE INDEX pot_accesslog_document_id    ON pot_accesslog(document_id);
CREATE INDEX pot_accesslog_exit_target_id ON pot_accesslog(exit_target_id);

CREATE TABLE pot_add_data (
  accesslog_id number(10)    NOT NULL,
  data_field   varchar2(32)  NOT NULL,
  data_value   varchar2(255) NOT NULL
);

CREATE INDEX pot_add_data_accesslog_id ON pot_add_data(accesslog_id);

CREATE TABLE pot_documents (
  data_id      number(11)    PRIMARY KEY,
  string       varchar2(255) NOT NULL,
  document_url varchar2(255) NOT NULL
);

CREATE TABLE pot_exit_targets (
  data_id number(11)    PRIMARY KEY,
  string  varchar2(255) NOT NULL
);

CREATE TABLE pot_hostnames (
  data_id number(11)    PRIMARY KEY,
  string  varchar2(255) NOT NULL
);

CREATE TABLE pot_operating_systems (
  data_id number(11)    PRIMARY KEY,
  string  varchar2(255) NOT NULL
);

CREATE TABLE pot_referers (
  data_id number(11)    PRIMARY KEY,
  string  varchar2(255) NOT NULL
);

CREATE TABLE pot_user_agents (
  data_id number(11)    PRIMARY KEY,
  string  varchar2(255) NOT NULL
);

CREATE TABLE pot_visitors (
  accesslog_id        number(11) PRIMARY KEY,
  visitor_id          number(11) NOT NULL,
  client_id           number(10) NOT NULL,
  operating_system_id number(11) NOT NULL,
  user_agent_id       number(11) NOT NULL,
  host_id             number(11) NOT NULL,
  referer_id          number(11) NOT NULL,
  timestamp           number(10) NOT NULL,
  returning_visitor   number(1)  NOT NULL
);

CREATE INDEX pot_visitors_client_time ON pot_visitors(client_id, timestamp);
