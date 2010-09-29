<fieldset style="width: 100%">
<legend>List Settings Variables</legend>
<form method="post" name="configs" action="{SITE_URL}/admin/system/settings-update">
<input type="hidden" name='send' value='on'>
<table cellpadding="0" cellspacing="0" class="list_table" width="100%">
	<tr>
		<td class='table_subhead' width='33%'><span>Name</span></td>
		<td class='table_subhead' width='34%'><span>Value</span></td>
		<td class='table_subhead' width='33%'><span>Default</span></td>
	</tr>
	<!-- BEGIN textarea -->
	<tr>
		<td class='row1' valign="top" ><b>{NAME}</b><br />{EXPLANATION}</td>
		<td class='row1'><textarea name="{VARIABLE}" rows="{NR_ROWS}" cols="50">{CURRENT_VALUE}</textarea></td>
		<td class='row1' valign="top">{DEFAULT}</td>
	</tr>

	<!-- END textarea -->
	<!-- BEGIN option -->
	<tr>
		<td class='row1' valign="top"><b>{NAME}</b><br />{EXPLANATION}</td>
		<td class='row1'>
			<select name="{VARIABLE}" style='min-width: 280px;'>
				<!-- BEGIN options -->
				<option value="{LIST_OPTION}" {SELECTED_OPTION}>{LIST_OPTION}</option>
				<!-- END options -->
			</select>
		</td>
		<td class='row1' valign="top">{DEFAULT}</td>
	</tr>
	<!-- END option -->
	<!-- BEGIN radio -->
	<tr>
		<td class='row1'><b>{NAME}</b><br />{EXPLANATION}</td>
		<td class='row1' valign="middle">
			<!-- BEGIN radios -->
			<span>{POSIBLE_VALUE_TXT}</span><input type="radio" id="{VARIABLE}_{POSIBLE_VALUE}" name="{VARIABLE}" value="{POSIBLE_VALUE}" {CHECKED_OPTION}/>&nbsp;
			<!-- END radios -->
		</td>
		<td class='row1' valign="top">{DEFAULT}</td>
	</tr>

	<!-- END radio -->
</table><br />
<center><input type="submit" value="Update " class="small_btn"></center>
</form>
</fieldset>