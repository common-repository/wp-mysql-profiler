=== WP MySQL Profiler ===
Tags: profiler, performance, mysql, queries, speed, themes, plugins
Contributors: asadkn
Requires at least: 2.8
Tested up to: 2.9
Stable tag: 1.0

Used to profile MySQL queries for performance; with backtrace information for each query (caller functions, lines, file names etc.)

== Description ==

A simple plugin to assist both theme and plugin developers in MySQL Profiling. Once installed, MySQL statistics will be available at page footer. Visit http://wpsplash.com/mysql-profiler-plugin-for-wordpress/ for more information. 

== Installation ==

* Download WP MySQL Profiler and extract the archive.
* Rename your wp-db.php file in wp-includes directory to wp-db-backup.php.
* Upload wp-db.php included in the archive to your wp-includes directory.
* Upload wpSqlProfiler.php to wp-content/plugins/ directory.
* Edit your wp-config.php. Find define (‘WPLANG’, ”); and add below:
  define('SAVEQUERIES', true);
* Enable “WP MySQL Profiler” plugin from the admin CP.

Visit http://wpsplash.com/mysql-profiler-plugin-for-wordpress/ for more info. 

== Changelog ==


== Upgrade Notice ==

