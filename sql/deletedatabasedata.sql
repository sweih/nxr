# Deletes all contents from the database
# Please uninstall the plugins you do not need manually!

DELETE FROM acl_management WHERE GUID>1000;
DELETE FROM acl_relations WHERE GUID>1000;
DELETE FROM categories WHERE CATEGORY_ID > 1000;
TRUNCATE TABLE centerstage;
TRUNCATE TABLE channels;
TRUNCATE TABLE channel_articles;
TRUNCATE TABLE channel_categories;
TRUNCATE TABLE channel_cluster_templates;
TRUNCATE TABLE cluster_templates;
TRUNCATE TABLE cluster_template_items;
TRUNCATE TABLE cluster_node;
TRUNCATE TABLE cluster_content;
TRUNCATE TABLE cluster_variations;
TRUNCATE TABLE compound_group_members;
TRUNCATE TABLE compound_groups;
TRUNCATE TABLE content;
TRUNCATE TABLE content_variations;
TRUNCATE TABLE dig_engine;
TRUNCATE TABLE dig_excludes;
TRUNCATE TABLE dig_keywords;
TRUNCATE TABLE dig_logs;
TRUNCATE TABLE dig_sites;
TRUNCATE TABLE dig_spider;
TRUNCATE TABLE dig_tempspider;
DELETE FROM groups WHERE GROUP_ID > 999;
TRUNCATE TABLE log;
TRUNCATE TABLE meta;
DELETE FROM meta_templates WHERE MT_ID > 999;
DELETE FROM meta_template_items WHERE MT_ID > 999;

# clear statistics
TRUNCATE TABLE pot_accesslog;
TRUNCATE TABLE pot_add_data;
TRUNCATE TABLE pot_documents;
TRUNCATE TABLE pot_exit_targets;
TRUNCATE TABLE pot_hostnames;
TRUNCATE TABLE pot_nxlog;
TRUNCATE TABLE pot_operating_systems;
TRUNCATE TABLE pot_referers;
TRUNCATE TABLE pot_search_engines;
TRUNCATE TABLE pot_user_agents;
TRUNCATE TABLE pot_visitors;

DELETE FROM sequences WHERE 1;
TRUNCATE TABLE sessions;
TRUNCATE TABLE sitemap;
TRUNCATE TABLE sitepage;
TRUNCATE TABLE sitepage_master;
TRUNCATE TABLE sitepage_names;
TRUNCATE TABLE sitepage_owner;
TRUNCATE TABLE sitepage_variations;
TRUNCATE TABLE state_translation;

TRUNCATE TABLE temp_vars;
DELETE FROM user_permissions WHERE USER_ID > 1;
DELETE FROM user_session WHERE USER_ID > 1;
DELETE FROM users WHERE USER_ID > 1;
DELETE FROM variations WHERE VARIATION_ID > 1;

TRUNCATE TABLE pgn_text;
TRUNCATE TABLE pgn_image;
TRUNCATE TABLE pgn_label;
TRUNCATE TABLE pgn_link;







