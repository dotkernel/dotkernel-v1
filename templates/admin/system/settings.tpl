<form method="post" name="configs" action="{SITE_URL}/admin/system/settings-update">
	<input type="hidden" name="userToken" value="{USERTOKEN}">
	<div class="rouded-corners box-shadow">
		<table class="big_table" frame="box" rules="all">
			<thead>
				<tr>
					<th width='33%'>Name</th>
					<th width='34%'>Value</th>
					<th width='33%'>Default</th>
				</tr>
			</thead>
			<tbody>
			<!-- BEGIN textarea -->
				<tr>
					<td valign="top" ><b>{NAME}</b><br />{EXPLANATION}</td>
					<td><textarea name="{VARIABLE}" rows="{NR_ROWS}" cols="50">{CURRENT_VALUE}</textarea></td>
					<td valign="top">{DEFAULT}</td>
				</tr>
				<!-- END textarea -->
				<!-- BEGIN option -->
				<tr>
					<td valign="top"><b>{NAME}</b><br />{EXPLANATION}</td>
					<td>
						<select name="{VARIABLE}" style='min-width: 280px;'>
							<!-- BEGIN options -->
							<option value="{LIST_OPTION}" {SELECTED_OPTION}>{LIST_OPTION}</option>
							<!-- END options -->
						</select>
					</td>
					<td valign="top">{DEFAULT}</td>
				</tr>
				<!-- END option -->
				<!-- BEGIN radio -->
				<tr>
					<td><b>{NAME}</b><br />{EXPLANATION}</td>
					<td valign="middle">
						<!-- BEGIN radios -->
						<span>{POSIBLE_VALUE_TXT}</span><input type="radio" id="{VARIABLE}_{POSIBLE_VALUE}" name="{VARIABLE}" value="{POSIBLE_VALUE}" {CHECKED_OPTION}/>&nbsp;
						<!-- END radios -->
					</td>
					<td valign="top">{DEFAULT}</td>
				</tr>
			<!-- END radio -->
			</tbody>
		</table>
	</div>
	<br/>
	<input type="submit" value="Update" class="button">
</form>

