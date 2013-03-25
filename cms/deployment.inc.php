<?PHP	

	/**
	 Deployment lets you copy the files you are launching to several servers. So you can create
	 backups of your homepage, you can use loadbalancers or you can also publish a static html
	 homepage to a server which does not support php.
	 Deployment was not used very much yet, so make yourself sure, that you may run into bugs, we
	 would need to fix. 
	 
	 If you are using deployment for backup sites and load-balancers, you may want to setup a database
	 replication. Read details in the mySQL-handbook. The slave system has the usual n/x-installation. You 
	 should however delete cms/modules and cms/index.php.
	 
	 For configuration-questions of deployment read the installation guide.
	 */

	// Deployment
	// Your server deploys all files locally first. Then you can define additional deploy-List here.
	/**
	$deploy[0]["mode"] = "ftp";  					// file or ftp
	$deploy[0]["ftpserver"] = "localhost";			// if ftp, server-address
	$deploy[0]["ftpport"] = "21";					// if ftp, server-port
	$deploy[0]["ftpuser"] = "test";					// if ftp, server-user
	$deploy[0]["ftppasswd"] = "test";				// if ftp, user-password
	$deploy[0]["ftprootdir"] = "/";					// if ftp, ftp-root-dir
	$deploy[0]["livepath"] = "www/";                // if ftp, ftp-live-files-path (pages)
	$deploy[0]["livefilespath"] =  "www/images/";   // if ftp, ftp-live-data-files (images) -path
	$deploy[0]["cachepath"] = "www/pages/";		// path, where cache files go to.
	
	$deploy[1]["mode"] = "file";  					// file or ftp
	$deploy[1]["ftpserver"] = "localhost";			// if ftp, server-address
	$deploy[1]["ftpport"] = "21";					// if ftp, server-port
	$deploy[1]["ftpuser"] = "test";					// if ftp, server-user
	$deploy[1]["ftppasswd"] = "test";				// if ftp, user-password
	$deploy[1]["ftprootdir"] = "/";					// if ftp, ftp-root-dir
	$deploy[1]["livepath"] = "c:/test/";                // live files-path (pages)
	$deploy[1]["livefilespath"] =  "c:/test/images/";   // live-data-files (images) -path
	$deploy[1]["cachepath"] = "c:/test/pages/";		// path, where cache files go to. 
	*/ 
	
?>