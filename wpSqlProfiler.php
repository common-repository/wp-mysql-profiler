<?php

/*
Plugin Name: WP MySQL Profiler
Plugin URI: http://wpsplash.com/mysql-profiler-plugin-for-wordpress/
Description: Profile your MySQL queries.
Author: Asad Khan
Version: 1.0
Author URI: http://twitter.com/asadkn
*/

/**
Dual GPL / MIT license. 

Copyright (c) 2010 Asad Khan

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

class wpSqlProfiler
{
	var $wpdb;

	function wpSqlProfiler(&$wpdb) {
		$this->wpdb = &$wpdb;
	}

	function showJScriptCss()
	{

$javascript = <<<EOF
	<script type="text/javascript">
	function showFullTrace(id) {
		var full = document.getElementById('fullTrace-' + id);
		full.style.display = '';

		var simple = document.getElementById('simpleTrace-' + id);
		simple.style.display = 'none';

		return false;
	}		
	</script>

	<style type="text/css">
	table.profiler td { padding: 10px; }
	table.profiler { border: 1px solid #000; }
	table.profiler td { border: 1px solid #ececec; }
	table.profiler th { padding: 10px; background-color: #efefef; }

	table.profiler .firstRow {
		background-color: #f2f2fa;
	}

	table.profiler .altRow { background-color: #fff; }
	</style>
EOF;
		echo $javascript;
	}

	function showQueries()
	{

		// is not an admin?
		if (!current_user_can('manage_options')) {
			return false;
		}

		if (!$this->wpdb) {
			global $wpdb;
			$this->wpdb = $wpdb;
		}

		$this->showJScriptCss();

		$queries = array();
		$totalQueries = $totalTime = 0;
		foreach (array_reverse($this->wpdb->queries) as $query)
		{
			$count++;

			$backtrace = (array) $query[2];
			$simpleTrace = $fullTrace = array();
			foreach ($backtrace as $key => $trace) 
			{
				// a static or a dynamic object call?
				if ($trace['type'] == '->' OR $trace['type'] == '::') {
					$trace['function'] = $trace['class'] . $trace['type'] . $trace['function'];
				}

				$theTrace = "<div style='border-bottom: 1px dotted #000;'>{$trace['file']} (line {$trace['line']}): {$trace['function']}()</div>";
				$fullTrace[] = $theTrace;

				if (!stristr($trace['file'], '/wp-content/') OR !$trace['function']) {
					continue;
				}

				$simpleTrace[] = $theTrace;
			}

			$simpleTrace = implode("<br />", $simpleTrace);
			$fullTrace   = implode("<br />", $fullTrace);

			$totalTime += $query[1];
			$totalQueries++;

			$queries[] = "
			<tr  class='". (($count % 2) == 0 ? 'firstRow' : 'altRow')."'>
				<td>{$query[0]}</td>
				<td>". number_format($query[1], 5) . "</td>
				<td>
					<div id='simpleTrace-{$count}'>
						{$simpleTrace}
						<div><a href='#' onclick='return showFullTrace({$count});'>Show Full Trace</a></div>
					</div>
					<div  id='fullTrace-{$count}' style='display: none;'>{$fullTrace}</div>
				</td>
			</tr>
			";
		}

		echo "<div style='background-color: #fff; margin: 10px; clear: both;'>
			<h2>WP MySQL Profiler</h2>
			<p><strong>Total Queries:</strong> {$totalQueries} - <strong>Total Time Of MySQL Queries:</strong> ". number_format($totalTime, 5) . "</p>
			<table class='profiler' cellspacing='0'>
				<tr><th>Query</th><th>Time</th><th>Backtrace</th></tr>
			" . implode("\r\n", $queries) . "</table>
		</div>";
	}
}

$profiler = new wpSqlProfiler($wpdb);

add_action('wp_footer', array($profiler, 'showQueries'), 100);
add_action('admin_footer', array($profiler, 'showQueries'), 100);

if (!defined('SAVEQUERIES')) {
	define('SAVEQUERIES', true);
}


