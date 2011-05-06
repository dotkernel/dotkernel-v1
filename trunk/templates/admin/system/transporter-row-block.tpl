<!-- BEGIN list -->
<tr>
	<td style="text-align: center;">{ID}</td>
	<td><a href="{SITE_URL}/admin/system/transporter-update/id/{ID}">{USER}</a></td>
	<td>{SERVER}</td>
	<td>{PORT}</td>
	<td>{SSL}</td>
	<td>{DATE_CREATED}</td>
	<td style="text-align:right">{CAPACITY}</td>
	<td style="text-align:right">{COUNTER}</td>
	<td style="vertical-align:middle;">
		<a style="cursor:pointer" title="Activate / Deactivate" class="{ACTIVE_IMG}_state activeButton" data-id="{ID}" data-active="{ISACTIVE}">&nbsp;</a>
	</td>
	<td > 
		<table width="100%" class="action_table">
			<tr>
				<td width="25%"><a href="{SITE_URL}/admin/system/transporter-update/id/{ID}" title="Edit/Update" class="edit_state">&nbsp;</a></td>
				<td width="25%"><a href="{SITE_URL}/admin/system/transporter-delete/id/{ID}" title="Delete" class="delete_state">&nbsp;</a></td>
			</tr>
		</table>
	</td>
</tr>
<!-- END list -->