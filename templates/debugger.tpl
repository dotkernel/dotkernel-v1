<link rel="stylesheet" href="{SITE_URL}/templates/css/debugger.css" type="text/css" >
<div class="index_debugger clearfix">
	<!-- BEGIN php_version -->
	<div class="version_php"><img src="{SITE_URL}/images/debugbar/php.png" class="debugger_images" alt="{PHP_VERSION}">{PHP_VERSION}</div>
	<!-- END php_version -->
	<!-- BEGIN zf_version -->
	<div class="version_zf"><img src="{SITE_URL}/images/debugbar/copyright.gif" class="debugger_images" alt="{ZF_VERSION}">{ZF_VERSION}</div>
	<!-- END zf_version -->
	<!-- BEGIN dot_version -->
	<div class="version_zf"><img src="{SITE_URL}/images/debugbar/dotkernel.png" class="debugger_images" alt="{DOT_VERSION}">{DOT_VERSION}</div>
	<!-- END dot_version -->
	<!-- BEGIN total_time -->
	<div class="total_time"><img src="{SITE_URL}/images/debugbar/time.png" class="debugger_images" alt="Page generated"><span>Page generated in:</span> {TOTAL_GENERAL_TIME} ms  </div>
	<!-- END total_time -->
	<!-- BEGIN memory_usage -->
	<div class="memory_usage"><img src="{SITE_URL}/images/debugbar/memory.png" class="debugger_images" alt="Memory usage"><span>Memory usage:</span> {MEMORY_USAGE} </div>
	<!-- END memory_usage -->
	<!-- BEGIN details_db_debug -->
	<div class="details_debugger_db" id="dbclick" onclick="ShowHideDiv('db', 'showhidediv')"><img src="{SITE_URL}/images/debugbar/database.png" class="debugger_images" alt="Executed queries"><span>Executed queries:</span> {TOTAL_QUERIES}, in {TOTAL_TIME} ms. </div>
	<!-- END details_db_debug -->
	<!-- BEGIN db_debug -->
	<div class="debugger_db"><img src="{SITE_URL}/images/debugbar/database.png" class="debugger_images" alt="Executed queries"><span>Executed queries :</span> {TOTAL_QUERIES}, in {TOTAL_TIME} ms.</div>
	<!-- END db_debug -->
	<!-- BEGIN details_opcache_memory -->
	<div class="details_debugger_opcache" id="opcacheclick" onclick="ShowHideDiv('opcache', 'showhidediv')"><img src="{SITE_URL}/images/debugbar/cache.png" class="debugger_images" alt="OpCache usage"><span>OpCache usage:</span> {OPCACHE_MEMORY}</div>
	<!-- END details_opcache_memory -->
	<!-- BEGIN opcache_memory -->
	<div class="debugger_opcache"><img src="{SITE_URL}/images/debugbar/cache.png" class="debugger_images" alt="OpCache usage"><span>OpCache usage:</span> {OPCACHE_MEMORY}</div>
	<!-- END opcache_memory -->

	<!-- BEGIN if_show_debug -->
	<div id="db" style="display: {INITIAL_DISPLAY};" class="db_debug_rows showhidediv" >
		<table class="debuggertable">
			<tr>
				<th width="70">Query #</th>
				<th>Time</th>
				<th>Query</th>
				<th>Params</th>
			</tr>
			<!-- BEGIN queries -->
			<tr class="{DEBUG_CLASS}">
				<td>{QUERY_COUNT}</td>
				<td>{QUERY_TIME} ms</td>
				<td>{QUERY_TEXT}</td>
				<td align="center">
					<!-- BEGIN if_params --><a onclick="ShowHideDiv('params_{QUERY_COUNT}');">details</a><!-- END if_params -->
					<!-- BEGIN no_params -->No params<!-- END no_params -->
				</td>
			</tr>
			<tr class="{DEBUG_CLASS}_params" id="params_{QUERY_COUNT}" style="display: none;">
				<td valign="top"><b>Params :</b></td>
				<td colspan="3">
					<!-- BEGIN params --><blockquote>{QUERY_PARAMS}</blockquote><!-- END params -->
				</td>
			</tr>
			<!-- END queries -->
		</table><br>
		<table  class="debuggertable">
			<tr>
				<td class="debugger_1"><b>Total queries executed</b> : </td>
				<td class="debugger_2">{TOTAL_QUERIES}</td>
			</tr>
			<tr>
				<td class="debugger_1"><b>Total time elapsed</b> : </td>
				<td class="debugger_2">{TOTAL_TIME} ms</td>
			</tr>
			<tr>
				<td class="debugger_1"><b>Average query lenght</b> : </td>
				<td class="debugger_2">{AVERAGE_QUERY_TIME} ms</td>
			</tr>
			<tr>
				<td class="debugger_1"><b>Queries per second</b> : </td>
				<td class="debugger_2">{QUERIES_PER_SECOND} queries</td>
			</tr>
			<tr>
				<td class="debugger_1"><b>Longest query</b> : </td>
				<td class="debugger_2">{LONGEST_QUERY}, in {LONGEST_QUERY_TIME} ms</td>
			</tr>
		</table>
	</div>
	<!-- END if_show_debug -->
	
	<!-- BEGIN if_show_opcache -->
	<div id="opcache" style="display: {OPCACHE_INITIAL_DISPLAY};" class="opcache_debug_rows showhidediv" >
		<table class="debuggertable">
			<tr>
				<th colspan="8">OpCache Statistics</th>
			</tr>
			<tr>
				<td class="debugger_1"><b>Used memory</b> : </td>
				<td class="debugger_2">{USED_MEMORY}</td>
				<td class="debugger_1"><b>Cached keys</b> : </td>
				<td class="debugger_2">{CACHED_KEYS}</td>
				<td class="debugger_1"><b>Blacklist misses</b> : </td>
				<td class="debugger_2">{BLACKLIST_MISSES}</td>
				<td class="debugger_1"><b>Last restart</b> : </td>
				<td class="debugger_2">{LAST_RESTART}</td>
			</tr>
			<tr>
				<td class="debugger_1"><b>Wasted memory</b> : </td>
				<td class="debugger_2">{WASTED_MEMORY}</td>
				<td class="debugger_1"><b>Max cached keys</b> : </td>
				<td class="debugger_2">{MAX_CACHED_KEYS}</td>
				<td class="debugger_1"><b>Miss ratio</b> : </td>
				<td class="debugger_2">{MISS_RATIO}</td>
				<td class="debugger_1"><b>Oom restart</b> : </td>
				<td class="debugger_2">{OOM_RESTART}</td>	
			</tr>
			<tr>
				<td class="debugger_1"><b>Currently wasted</b> : </td>
				<td class="debugger_2">{CURRENTLY_WASTED}</td>
				<td class="debugger_1"><b>Hits</b> : </td>
				<td class="debugger_2">{HITS}</td>
				<td class="debugger_1"><b>Opcache hit rate</b> : </td>
				<td class="debugger_2">{OPCACHE_HIT_RATE}</td>
				<td class="debugger_1"><b>Hash restarts</b> : </td>
				<td class="debugger_2">{HASH_RESTARTS}</td>
			</tr>
			<tr>
				<td class="debugger_1"><b>Cached scripts</b> : </td>
				<td class="debugger_2">{CACHED_SCRIPTS}</td>
				<td class="debugger_1"><b>Misses</b> : </td>
				<td class="debugger_2">{MISSES}</td>
				<td class="debugger_1"><b>Start time</b> : </td>
				<td class="debugger_2">{START_TIME}</td>
				<td class="debugger_1"><b>Manual restarts</b> : </td>
				<td class="debugger_2">{MANUAL_RESTARTS}</td>
			</tr>
		</table>
	</div>
	<!-- END if_show_opcache -->
	
</div>
