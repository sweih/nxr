<?php

$pagemenu = new cmsmenu();

$pagemenu->addMenu($lang->get("content", "Content"), "EXPLORE_SITE_M", false);
$pagemenu->addSubMenu($lang->get("m_www", "Website"), "modules/sitepages/sitepagebrowser.php", "EXPLORE_SITE_S", "editsite");
$pagemenu->addSubMenu($lang->get("m_lib", "Library"), "modules/content/objectbrowser.php?resetfilter=1", "OBJECT_BROWSER", "contenbase");
$pagemenu->addSubMenu($lang->get("m_articles", "Articles"), "modules/channels/overview.php", "CHANNEL_EDIT", "channel");
$pagemenu->addSubMenu($lang->get("m_clusters", "Clusters"), "modules/cluster/clusterbrowser.php?resetfilter=1", "CL_BROWSER", "clusters");
$pagemenu->addSubMenu($lang->get("m_rollout", "Rollout"), "modules/rollout/rollout.php", "ROLLOUT", "rollout");
$pagemenu->addSubMenu($lang->get("m_import_wz", "Import Wizard"), "modules/syndication/wz_import.php", "IMPORT", "import");
$pagemenu->addSubMenu($lang->get("m_export_wz", "Export Wizard"), "modules/syndication/wz_export.php", "EXPORT", "export");
  
$pagemenu->addMenu($lang->get("m_temp","Templates"), "TEMPLATES_M");
$pagemenu->addSubMenu($lang->get("m_ptemp", "Page Templates"), "modules/pagetemplate/sitepage_master.php", "SITEPAGE_MASTER", "template");
$pagemenu->addSubMenu($lang->get("m_clt", "Cluster Templates"), "modules/clustertemplate/clustertemplates.php", "CL_TEMP_BROWSER", "clustertemplates");
$pagemenu->addSubMenu($lang->get("m_meta"), "modules/meta/metascheme.php", "META_TEMP", "meta");
$pagemenu->addSubMenu($lang->get("cl_group","Cluster Group" ), "modules/compoundgroup/compound_groups.php", "COMPOUND_GROUPS", "clustergroup")	;
$pagemenu->addSubmenu($lang->get("designs", "Layout Designs"), "modules/designs/overview.php", 'ADMINISTRATOR');
		
$pagemenu->addMenu($lang->get("mt_title", "Maintenance"), "MAINTENANCE");
$pagemenu->addSubmenu($lang->get("m_purge"), "modules/purge/purge.php", "PURGE_DATABASE", "purge");
$pagemenu->addSubMenu($lang->get("mt_spider", "Run Spider"), "ext/phpdig/admin/nxindex.php", "SearchEngineAdm", "maintenance2");
$pagemenu->addSubMenu($lang->get("mt_generate_dta", "Generate DataTypes"), "modules/maintenance/datatypes.php", "ADMINISTRATOR", "maintenance");			
if ($c["renderstatichtml"])
	$pagemenu->addSubMenu($lang->get("rb_cache", "Rebuild Cache"), "modules/maintenance/rebuild_cache.php", "ADMINISTRATOR", "maintenance2");	
$pagemenu->addSubMenu($lang->get("clear_jpcache", "Clear Cache"), "modules/maintenance/clear_temp_cache.php", "ADMINISTRATOR", "purge");
	
$pagemenu->addSubMenu($lang->get("logs", "Logfile Analysis"), "modules/logs/logs.php", "LOGS", "logfile");
$pagemenu->addSubMenu($lang->get("sync_clusters", "Synchronize Clusters"), "modules/maintenance/sync_clusters.php", "SYNC_CLUSTERS", "maintenance2");			
$pagemenu->addSubMenu($lang->get("mt_lw_site", "Launch whole site"), "modules/maintenance/launch_site.php", "ADMINISTRATOR", "maintenance2");
$pagemenu->addSubMenu($lang->get("maint_mode", "Maintenance Mode"), "modules/maintenance_mode/switchmode.php", "ADMINISTRATOR", "maintenance2");
	
$pagemenu->addMenu($lang->get("m_installplugin", "Plugins"), "ANY");
$pagemenu->addSubmenu($lang->get("m_pgn"), "modules/plugins/install.php", "ADMINISTRATOR", "plugincontrol");

// retrieve list with all plugins
includePGNSources();
$plugins = createDBCArray("modules", "MODULE_ID", "1");

for ($i = 0; $i < count($plugins); $i++) {
	$ref = createPGNRef($plugins[$i], 0);

	if ($ref->globalConfigPage != "") {
		$ref->registration();

		$pagemenu->addSubMenu($ref->name, $ref->globalConfigPage, $ref->globalConfigRoles, "plugin", false);
	}
}
 
$pagemenu->addMenu($lang->get("m_admin", "Administration"), "ADMINISTRATION_M", false);
$pagemenu->addSubmenu($lang->get("m_access", "Access"), "modules/user/user_general.php", "USER_MANAGEMENT", "accesscontrol");
$pagemenu->addSubmenu($lang->get("m_var"), "modules/variations/variations.php", "VARIATIONS", "variations");
$pagemenu->addSubMenu($lang->get("m_backup", "Backup"), "modules/backup/backup.php", "BACKUP", "backup");
$pagemenu->addSubMenu($lang->get("m_translation", "Translation"), "modules/translation/translation.php", "TRANSLATION", "translation");
$pagemenu->addSubmenu($lang->get("m_myprofile"), "modules/user/myprofile.php", "ANY", "myprofile");

if ($c["pagetracking"])
	$pagemenu->addSubmenu($lang->get("m_report"), "modules/stats/report.php", "TRAFFIC", "stats");

$pagemenu->addMenu($lang->get("m_community", "Community"), "ESERVICES", false);
$pagemenu->addSubmenu($lang->get("m_cuser", "Users"), "modules/communitylogin/user_general.php", "CUSTOMERCAREADMIN", "accesscontrol");
$pagemenu->addSubMenu($lang->get("contacts", "Contacts"), "modules/address/overview.php", "ADDRESS");
$pagemenu->addSubMenu($lang->get("newsletter", "Newsletter"), "modules/newsletter/overview.php", "NEWSLETTER");
$pagemenu->addSubMenu($lang->get("mailings", "Mailings"), "modules/customercare/index.php", "CUSTOMERCARE|CUSTOMERCAREADMIN");



?>