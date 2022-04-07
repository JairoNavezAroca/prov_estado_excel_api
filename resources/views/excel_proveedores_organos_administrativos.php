<table>
	<thead>
		<tr>
			<th style="width: 120px">RUC</th>
			<th style="width: 250px">APELLIDOS Y NOMBRES</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($proveedor as $item) {
			echo '<tr>';
			echo '<td style="text-align: left">' . $item->ruc . '</td>';
			echo '<td>' . str_replace('&', '&amp;', $item->apellidosNomb) . '</td>';
			echo '</tr>';
		}
		?>
	</tbody>
</table>

