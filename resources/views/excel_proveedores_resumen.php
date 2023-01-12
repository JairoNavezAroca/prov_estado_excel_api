<table>
	<thead>
		<tr>
			<th style="width: 120px">RUC</th>
			<th style="width: 450px">RAZON SOCIAL</th>
			<th style="width: 200px">DEPARTAMENTO</th>
			<th style="width: 300px">EMAILS</th>
			<th style="width: 250px">TELEFONOS</th>
			<th style="width: 350px">REPRESENTANTE</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($proveedor as $item) {
			echo '<tr>';
			echo '<td style="text-align: left">' . str_replace('&', '&amp;', $item->ruc) . '</td>';
			echo '<td>' . str_replace('&', '&amp;', $item->razon) . '</td>';
			echo '<td>' . str_replace('&', '&amp;', $item->departamento) . '</td>';
			echo '<td>' . str_replace('&', '&amp;', $item->emails) . '</td>';
			echo '<td style="text-align: left">' . str_replace('&', '&amp;', $item->telefonos) . '</td>';
            echo '<td>' . str_replace('&', '&amp;', $item->representante) . '</td>';
			echo '</tr>';
		}
		?>
	</tbody>
</table>

