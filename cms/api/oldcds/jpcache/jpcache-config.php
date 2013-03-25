<?php

	// jpcache configuration file
	//
	// Some settings are specific for the type of cache you are running, like
	// file- or database-based.
	// 

	/**
	 * Define which jpcache-type you want to use (storage and/or system).
	 *
	 * This allows for system-specific patches & handling, as sometimes
	 * 'platform independent' is behaving quite differently.
	 */
	$JPCACHE_TYPE = "file";

	/**
	 * General configuration options. 
	 */
	$JPCACHE_DEBUG = 0;         // Turn debugging on/off
	$JPCACHE_IGNORE_DOMAIN = 0; // Ignore domain name in request(single site)
	$JPCACHE_POST = 0;          // Should POST's be cached
	$JPCACHE_GC = 1;            // Probability % of garbage collection
	$JPCACHE_GZIP_LEVEL = 9;    // GZIPcompressionlevel to use (1=low,9=high)

	/**
	 * File based caching setting.
	 */
	$JPCACHE_DIR = $c["dyncachepath"]; // Directory where jpcache must store
	// generated files. Please use a dedicated
	// directory, and make it writable
	$JPCACHE_FILEPREFIX = "jpc-"; // Prefix used in the filename. This enables
// us to (more accuratly) recognize jpcache-
// files.
?>