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

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[pot_search_engines]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[pot_search_engines]
GO

CREATE TABLE pot_search_engines (
  [accesslog_id]  [int]          PRIMARY KEY,
  [search_engine] [varchar](64)  NOT NULL,
  [keywords]      [varchar](255) NOT NULL
)
GO
