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
# $Id: 1_1_X_to_1_2_0.sql,v 1.1 2003/09/01 17:19:33 sven_weih Exp $
#

ALTER TABLE pot_accesslog DROP client_id;
ALTER TABLE pot_accesslog CHANGE entry_document    entry_document    TINYINT(3) UNSIGNED NOT NULL;
ALTER TABLE pot_visitors  CHANGE returning_visitor returning_visitor TINYINT(3) UNSIGNED NOT NULL;

UPDATE pot_accesslog SET entry_document    = entry_document    - 1;
UPDATE pot_visitors  SET returning_visitor = returning_visitor - 1;
