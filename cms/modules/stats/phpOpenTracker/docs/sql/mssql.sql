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
-- | Author: Christopher Hughes <christopher.hughes@cancom.com>          |
-- +---------------------------------------------------------------------+

-- $Id: mssql.sql,v 1.1 2003/09/01 17:19:33 sven_weih Exp $

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[pot_accesslog]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[pot_accesslog]
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[pot_add_data]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[pot_add_data]
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[pot_documents]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[pot_documents]
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[pot_exit_targets]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[pot_exit_targets]
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[pot_hostnames]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[pot_hostnames]
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[pot_operating_systems]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[pot_operating_systems]
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[pot_referers]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[pot_referers]
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[pot_user_agents]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[pot_user_agents]
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[pot_visitors]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[pot_visitors]
GO

CREATE TABLE pot_accesslog (
  [accesslog_id] [int] NOT NULL,
  [document_id]  [int] NOT NULL,
  [timestamp]    [int] NOT NULL,
  [exit_target_id] [int] DEFAULT '0',
  [entry_document] [int] NOT NULL
)
GO
CREATE INDEX pot_accesslog_accesslog_id ON pot_accesslog(accesslog_id)
GO
CREATE INDEX pot_accesslog_timestamp ON pot_accesslog(timestamp)
GO
CREATE INDEX pot_accesslog_document_id ON pot_accesslog(document_id)
GO
CREATE INDEX pot_accesslog_exit_target_id ON pot_accesslog(exit_target_id)
GO

CREATE TABLE pot_add_data (
  [accesslog_id] [int]          NOT NULL,
  [data_field]   [varchar](32)  NOT NULL,
  [data_value]   [varchar](255) NOT NULL
)
GO

CREATE INDEX pot_add_data_accesslog_id ON pot_add_data(accesslog_id)
GO

CREATE TABLE pot_documents (
  [data_id]      [int]    PRIMARY KEY,
  [string]       [varchar](255) NOT NULL,
  [document_url] [varchar](255) NOT NULL
)
GO

CREATE TABLE pot_exit_targets (
  [data_id] [int]    PRIMARY KEY,
  [string]  [varchar](255) NOT NULL
)
GO

CREATE TABLE pot_hostnames (
  [data_id] [int]    PRIMARY KEY,
  [string]  [varchar](255) NOT NULL
)
GO

CREATE TABLE pot_operating_systems (
  [data_id] [int]    PRIMARY KEY,
  [string]  [varchar](255) NOT NULL
)
GO

CREATE TABLE pot_referers (
  [data_id] [int]    PRIMARY KEY,
  [string]  [varchar](255) NOT NULL
)
GO

CREATE TABLE pot_user_agents (
  [data_id] [int]    PRIMARY KEY,
  [string]  [varchar](255) NOT NULL
)
GO

CREATE TABLE pot_visitors (
  [accesslog_id]        [int] PRIMARY KEY,
  [visitor_id]          [int] NOT NULL,
  [client_id]           [int] NOT NULL,
  [operating_system_id] [int] NOT NULL,
  [user_agent_id]       [int] NOT NULL,
  [host_id]             [int] NOT NULL,
  [referer_id]          [int] NOT NULL,
  [timestamp]           [int] NOT NULL,
  [returning_visitor]   [int] NOT NULL
)
GO

CREATE INDEX pot_visitors_client_time   ON pot_visitors(client_id, "timestamp")
GO
