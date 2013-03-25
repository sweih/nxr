/*
+---------------------------------------------------------------------+
| phpOpenTracker - The Website Traffic and Visitor Analysis Solution  |
+---------------------------------------------------------------------+
| Copyright (c) 2000-2003 Sebastian Bergmann. All rights reserved.    |
+---------------------------------------------------------------------+
| This source file is subject to the phpOpenTracker Software License, |
| Version 1.0, that is bundled with this package in the file LICENSE. |
| If you did not receive a copy of this file, you may either read the |
| license online at http://phpOpenTracker.de/license/1_0.txt, or send |
| a note to license@phpOpenTracker.de, so we can mail you a copy.     |
+---------------------------------------------------------------------+
| Authors: Cornelia Boenigk <c@cornelia-boenigk.de>                   |
|          Jean-Christian Imbeault <jc@mega-bucks.co.jp>              |
+---------------------------------------------------------------------+

$Id: postgresql.sql,v 1.1 2003/09/01 17:19:33 sven_weih Exp $
*/

CREATE TABLE "pot_accesslog" (
  "accesslog_id"   integer NOT NULL,
  "timestamp"      integer NOT NULL,
  "document_id"    integer NOT NULL,
  "exit_target_id" integer DEFAULT 0 NOT NULL,
  "entry_document" boolean NOT NULL
);

CREATE INDEX pot_accesslog_timestamp      on pot_accesslog(timestamp);
CREATE INDEX pot_accesslog_document_id    on pot_accesslog(document_id);
CREATE INDEX pot_accesslog_exit_target_id on pot_accesslog(exit_target_id);

CREATE TABLE "pot_add_data" (
  "accesslog_id" integer                NOT NULL,
  "data_field"   character varying(32)  NOT NULL,
  "data_value"   character varying(255) NOT NULL
);

CREATE TABLE "pot_documents" (
  "data_id"      integer                PRIMARY KEY,
  "string"       character varying(255) NOT NULL,
  "document_url" character varying(255) NOT NULL
);

CREATE TABLE "pot_exit_targets" (
  "data_id" integer                PRIMARY KEY,
  "string"  character varying(255) NOT NULL
);

CREATE TABLE "pot_hostnames" (
  "data_id" integer                PRIMARY KEY,
  "string"  character varying(255) NOT NULL
);

CREATE TABLE "pot_operating_systems" (
  "data_id" integer                PRIMARY KEY,
  "string"  character varying(255) NOT NULL
);

CREATE TABLE "pot_referers" (
  "data_id" integer                PRIMARY KEY,
  "string"  character varying(255) NOT NULL
);

CREATE TABLE "pot_user_agents" (
  "data_id" integer                PRIMARY KEY,
  "string"  character varying(255) NOT NULL
);

CREATE TABLE "pot_visitors" (
  "accesslog_id"        integer PRIMARY KEY,
  "visitor_id"          integer NOT NULL,
  "client_id"           integer NOT NULL,
  "operating_system_id" integer NOT NULL,
  "user_agent_id"       integer NOT NULL,
  "host_id"             integer NOT NULL,
  "referer_id"          integer NOT NULL,
  "timestamp"           integer NOT NULL,
  "returning_visitor"   boolean NOT NULL
);

CREATE INDEX pot_visitors_client_time on pot_visitors(client_id,timestamp);

CREATE OR REPLACE FUNCTION pot_documents_duplicate_check() RETURNS
TRIGGER AS '
BEGIN
  PERFORM 1 FROM pot_documents WHERE data_id=NEW.data_id LIMIT 1;
  IF FOUND THEN
    RETURN null;
  END IF;
  RETURN NEW;
END;
' LANGUAGE 'plpgsql' WITH (iscachable);

CREATE OR REPLACE FUNCTION pot_hostnames_duplicate_check() RETURNS
TRIGGER AS '
BEGIN
  PERFORM 1 FROM pot_hostnames WHERE data_id=NEW.data_id LIMIT 1;
  IF FOUND THEN
    RETURN null;
  END IF;
  RETURN NEW;
END;
' LANGUAGE 'plpgsql' WITH (iscachable);

CREATE OR REPLACE FUNCTION pot_operating_systems_duplicate_check()
RETURNS TRIGGER AS '
BEGIN
  PERFORM 1 FROM pot_operating_systems WHERE data_id=NEW.data_id LIMIT 1;
  IF FOUND THEN
    RETURN null;
  END IF;
  RETURN NEW;
END;
' LANGUAGE 'plpgsql' WITH (iscachable);

CREATE OR REPLACE FUNCTION pot_referers_duplicate_check() RETURNS
TRIGGER AS '
BEGIN
  PERFORM 1 FROM pot_referers WHERE data_id=NEW.data_id LIMIT 1;
  IF FOUND THEN
    RETURN null;
  END IF;
  RETURN NEW;
END;
' LANGUAGE 'plpgsql' WITH (iscachable);

CREATE OR REPLACE FUNCTION pot_user_agents_duplicate_check() RETURNS
TRIGGER AS '
BEGIN
  PERFORM 1 FROM pot_user_agents WHERE data_id=NEW.data_id LIMIT 1;
  IF FOUND THEN
    RETURN null;
  END IF;
  RETURN NEW;
END;
' LANGUAGE 'plpgsql' WITH (iscachable);

create trigger pot_documents_duplicate_check_trig
 BEFORE INSERT ON pot_documents
  for each ROW
   EXECUTE PROCEDURE pot_documents_duplicate_check();

create trigger pot_hostnames_duplicate_check_trig
 BEFORE INSERT ON pot_hostnames
  for each ROW
   EXECUTE PROCEDURE pot_hostnames_duplicate_check();

create trigger pot_operating_systems_duplicate_check_trig
 BEFORE INSERT ON pot_operating_systems
  for each ROW
   EXECUTE PROCEDURE pot_operating_systems_duplicate_check();

create trigger pot_referers_duplicate_check_trig
 BEFORE INSERT ON pot_referers
  for each ROW
   EXECUTE PROCEDURE pot_referers_duplicate_check();

create trigger pot_user_agents_duplicate_check_trig
 BEFORE INSERT ON pot_user_agents
  for each ROW
   EXECUTE PROCEDURE pot_user_agents_duplicate_check();
