<script language="JavaScript" type="text/javascript">
<!--
	function UpdateConfig()
	{
		if (confirm('Are you sure ?\nALL changes are applied directy in site !!!') )
		{
			document.configs.submit();
		}
	}
//-->
</script>
<fieldset>
<legend>List Settings Variables</legend>
<form method="post" name="configs" action="{SITE_URL}/admin/system/settings-update">
<input type="hidden" name='send' value='on'>
<table cellspacing='0' cellpadding="4" width="100%" class='grey'>
	<tr>
		<td class='table_subhead' width='25%'><span class="table_subheading">Name</span></td>
		<td class='table_subhead' width='40%'><span class="table_subheading">Value</span></td>
		<td class='table_subhead' width='35%'><span class="table_subheading">Default</span></td>
	</tr>
	<!-- BEGIN textarea -->
	<tr>
		<td class='row2' valign="top"><b>{NAME}</b></td>
		<td class='row2'><textarea name="{VARIABLE}" rows="{NR_ROWS}" cols="50">{CURRENT_VALUE}</textarea></td>
		<td class='row2' valign="top"><b>{DEFAULT}</b></td>
	</tr>
	<tr>
		<td colspan="3" class="{B2}"><i>{EXPLANATION}</i></td>
	</tr>
	<!-- END textarea -->
	<!-- BEGIN option -->
	<tr>
		<td class='row2' valign="top"><b>{NAME}</b></td>
		<td class='row2'>
			<select name="{VARIABLE}" style='min-width: 280px;'>
				<!-- BEGIN options -->
				<option value="{LIST_OPTION}" {SELECTED_OPTION}>{LIST_OPTION}</option>
				<!-- END options -->
			</select>
		</td>
		<td class='row2' valign="top"><b>{DEFAULT}</b></td>
	</tr>
	<tr>
		<td colspan="3" class="{B2}"><i>{EXPLANATION}</i></td>
	</tr>
	<!-- END option -->
	<!-- BEGIN radio -->
	<tr>
		<td class='row2'><b>{NAME}</b></td>
		<td class='row2' valign="middle">
			<!-- BEGIN radios -->
			<input type="radio" id="{VARIABLE}_{POSIBLE_VALUE}" name="{VARIABLE}" value="{POSIBLE_VALUE}" {CHECKED_OPTION}/><label for="{VARIABLE}_{POSIBLE_VALUE}">{POSIBLE_VALUE_TXT}</label>&nbsp;
			<!-- END radios -->
		</td>
		<td class='row2' valign="top"><b>{DEFAULT}</b></td>
	</tr>
	<tr>
		<td colspan="3" class="{B2}"><i>{EXPLANATION}</i></td>
	</tr>
	<!-- END radio -->
</table>
<br>
<center><input type="button" value="Update " class="small_btn" onClick='UpdateConfig()'></center>
</form>
</fieldset>
{NOTICE}