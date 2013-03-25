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
# $Id: pma.sql,v 1.1 2003/09/01 17:19:33 sven_weih Exp $
#

INSERT INTO PMA_relation (master_db, master_table, master_field, foreign_db, foreign_table, foreign_field) VALUES ('phpOpenTracker', 'pot_accesslog', 'accesslog_id', 'phpOpenTracker', 'pot_visitors', 'accesslog_id');
INSERT INTO PMA_relation (master_db, master_table, master_field, foreign_db, foreign_table, foreign_field) VALUES ('phpOpenTracker', 'pot_accesslog', 'document_id', 'phpOpenTracker', 'pot_documents', 'data_id');
INSERT INTO PMA_relation (master_db, master_table, master_field, foreign_db, foreign_table, foreign_field) VALUES ('phpOpenTracker', 'pot_accesslog', 'exit_target_id', 'phpOpenTracker', 'pot_exit_targets', 'data_id');
INSERT INTO PMA_relation (master_db, master_table, master_field, foreign_db, foreign_table, foreign_field) VALUES ('phpOpenTracker', 'pot_visitors', 'operating_system_id', 'phpOpenTracker', 'pot_operating_systems', 'data_id');
INSERT INTO PMA_relation (master_db, master_table, master_field, foreign_db, foreign_table, foreign_field) VALUES ('phpOpenTracker', 'pot_visitors', 'user_agent_id', 'phpOpenTracker', 'pot_user_agents', 'data_id');
INSERT INTO PMA_relation (master_db, master_table, master_field, foreign_db, foreign_table, foreign_field) VALUES ('phpOpenTracker', 'pot_visitors', 'host_id', 'phpOpenTracker', 'pot_hostnames', 'data_id');
INSERT INTO PMA_relation (master_db, master_table, master_field, foreign_db, foreign_table, foreign_field) VALUES ('phpOpenTracker', 'pot_visitors', 'referer_id', 'phpOpenTracker', 'pot_referers', 'data_id');
