<form id="label_delete_form" method="post" action="/admin/index.php">
	<input type="hidden" name="label_delete_action" id="label_delete_action" value="" />

	{if (count($all_labels) > 0)}
		<table class="listing" cellpadding="5" cellspacing="0" border="0">
			<colgroup width="300px"></colgroup>
			<colgroup width="75px"></colgroup>
			<thead>
				<tr class="header">
					<th>Naam</th>
					<th class="delete_column">Verwijder</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$all_labels item=label}
					<tr>
						<td><a href="/admin/index.php?label={$label.id}" title="{$label.name}">{$label.name}</a></td>
						<td class="delete_column">
							{$label.delete_checkbox}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		{$no_labels_message}
	{/if}
</form>
