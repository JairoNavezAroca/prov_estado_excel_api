<table>
	<thead>
		<tr>
			<th style="width: 120px">RUC</th>
			<th style="width: 450px">RAZON SOCIAL</th>
			<th style="width: 250px">TIPO DE EMPRESA</th>
			<th style="width: 200px">DEPARTAMENTO</th>
			<th style="width: 200px">PROVINCIA</th>
			<th style="width: 250px">DISTRITO</th>
			<th style="width: 100px">HABILITADO</th>
			<th style="width: 300px">EMAILS</th>
			<th style="width: 250px">TELEFONOS</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($proveedor as $item) {
			$item->esHabilitado = ($item->esHabilitado == '1') ? 'SI' : 'NO';
			echo '<tr>';
			echo '<td style="text-align: left">' . str_replace('&', '&amp;', $item->ruc) . '</td>';
			echo '<td>' . str_replace('&', '&amp;', $item->razon) . '</td>';
			echo '<td>' . str_replace('&', '&amp;', $item->tipoEmpresa) . '</td>';
			echo '<td>' . str_replace('&', '&amp;', $item->departamento) . '</td>';
			echo '<td>' . str_replace('&', '&amp;', $item->provincia) . '</td>';
			echo '<td>' . str_replace('&', '&amp;', $item->distrito) . '</td>';
			echo '<td>' . str_replace('&', '&amp;', $item->esHabilitado) . '</td>';
			echo '<td>' . str_replace('&', '&amp;', $item->emails) . '</td>';
			echo '<td style="text-align: left">' . str_replace('&', '&amp;', $item->telefonos) . '</td>';
			echo '</tr>';
		}
		?>
	</tbody>
</table>

