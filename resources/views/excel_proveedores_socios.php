<table>
	<thead>
		<tr>
			<th style="width: 120px">RUC</th>
			<th style="width: 350px">RAZON SOCIAL</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($proveedor as $item) {
			echo '<tr>';
			echo '<td style="text-align: left">' . $item->ruc . '</td>';
			echo '<td>' . str_replace('&', '&amp;', $item->razonSocial) . '</td>';
			echo '</tr>';
		}
		?>
	</tbody>
</table>

