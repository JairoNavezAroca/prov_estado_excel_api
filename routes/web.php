<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
	//return $router->app->version();
});


$router->post('/login', 'LoginController@login');

$router->post('/importar-excel', 'ProcesamientoController@importarExcel');
$router->delete('/importar-excel', 'ProcesamientoController@importarExcel');

$router->group(['middleware' => ['jwt.verify']], function () use ($router) {
	$router->patch('/cambiar-contrasena', 'LoginController@cambiarContrasena');

	$router->get('/obtener-cargado/{token}', 'ProcesamientoController@obtenerCargado');
	$router->post('/procesar-ruc', 'ProcesamientoController@procesarRuc');
});
$router->get('/exportar-excel/{token}', 'ProcesamientoController@exportarExcel');

/*$router->get('/key', function() {
    return \Illuminate\Support\Str::random(32);
});*/

// https://github.com/laracademy/generators
//php artisan generate:modelfromtable --overwrite=true --timestamps=false --table=cargado_proveedores,proveedor,proveedor_telefono,proveedor_email,proveedor_antecedente,proveedor_socio,proveedor_representante,proveedor_organo_administrativo,log_trazado_api_proveedores,log_proveedores,log_aplicacion

// Revisar generador de modelos
//https://github.com/reliese/laravel


/*

-- DROP DATABASE prov_estado_excel;
-- CREATE DATABASE prov_estado_excel;
USE prov_estado_excel;

CREATE TABLE cargado_proveedores(
	idCargadoProveedores INT PRIMARY KEY AUTO_INCREMENT,
	archivo VARCHAR(100),
	token VARCHAR(100),
	ip VARCHAR(100),
	fechaImportacion DATETIME,
	fechaExportacion DATETIME
);

CREATE TABLE proveedor(
	idProveedor INT PRIMARY KEY AUTO_INCREMENT,
	idCargadoProveedores INT NOT NULL,
	ruc VARCHAR(20) NOT NULL, -- SE REGISTRA AL INICIO DE LA TABLA
	token VARCHAR(100), -- es diferente al token de la tabla cargado_proveedores
	ip VARCHAR(100), -- cuando se registran todos los datos
	fechaRegistroRuc DATETIME,
	fechaRegistroDatos DATETIME, -- cuando se registran todos los datos
	estadoRegistroDatos VARCHAR(50), -- 'NO REGISTRADO', 'EN REGISTRO', 'REGISTRADO', 'FALLIDO'

	codigoRegistro VARCHAR(50), -- conformacion.proveedor.codigoRegistro
	
	respuesta INT,
	razon VARCHAR(200),
	tipoEmpresa VARCHAR(50),
	estado VARCHAR(40),
	condicion VARCHAR(20),
	departamento VARCHAR(100),
	provincia VARCHAR(100),
	distrito VARCHAR(100),
	personeria VARCHAR(10),
	process_ BOOLEAN,

	codProv VARCHAR(20),
	idOrigenProv INT,
	numRuc VARCHAR(20),
	nomRzsProv VARCHAR(200),
	esHabilitado BOOLEAN,
	lscIdTipReg VARCHAR(50),
	lscIdTipRegVig VARCHAR(50),
	esAptoContratar BOOLEAN,
	cmcTexto VARCHAR(200),
	
	numeroIntentos INT, 

	FOREIGN KEY (idCargadoProveedores) REFERENCES cargado_proveedores(idCargadoProveedores)
);

CREATE TABLE proveedor_telefono(
	idProveedorTelefono INT PRIMARY KEY AUTO_INCREMENT,
	idProveedor INT NOT NULL,
	telefono VARCHAR(200),
	FOREIGN KEY (idProveedor) REFERENCES proveedor(idProveedor)
);

CREATE TABLE proveedor_email(
	idProveedorEmail INT PRIMARY KEY AUTO_INCREMENT,
	idProveedor INT NOT NULL,
	email VARCHAR(200),
	FOREIGN KEY (idProveedor) REFERENCES proveedor(idProveedor)
);

CREATE TABLE proveedor_antecedente(
	idProveedorAntecedente INT PRIMARY KEY AUTO_INCREMENT,
	idProveedor INT NOT NULL,
	
	sanciones JSON,
	inhsJudicial JSON,
	inhsAdministrativa JSON,
	fechaConsultaSancTCE DATETIME,
	fechaConsultaInhabAD DATETIME,
	fechaConsultaInhabMJ DATETIME,
	process_ BOOLEAN,
	
	FOREIGN KEY (idProveedor) REFERENCES proveedor(idProveedor)
);

CREATE TABLE proveedor_socio(
	idProveedorSocio INT PRIMARY KEY AUTO_INCREMENT,
	idProveedor INT NOT NULL,
	
	idSocio INT,
	codigoRegistro VARCHAR(20),
	codigoDocIde VARCHAR(10),
	descDocIde VARCHAR(50),
	siglaDocIde VARCHAR(20),
	nroDocumento VARCHAR(20),
	numeroAcciones_ VARCHAR(20),
	numeroAcciones DECIMAL(11,1),
	porcentajeAcciones_ VARCHAR(20),
	porcentajeAcciones DECIMAL(12,2),
	razonSocial VARCHAR(200),
	numeroRuc VARCHAR(20),
	fechaIngreso DATE,
	
	FOREIGN KEY (idProveedor) REFERENCES proveedor(idProveedor)
);

CREATE TABLE proveedor_representante(
	idProveedorRepresentante INT PRIMARY KEY AUTO_INCREMENT,
	idProveedor INT NOT NULL,
	
	idRepresentante INT,
	codigoRegistro VARCHAR(20),
	codigoDocIde VARCHAR(10),
	descDocIde VARCHAR(50),
	siglaDocIde VARCHAR(20),
	nroDocumento VARCHAR(20),
	razonSocial VARCHAR(200),
	idCargo INT,
	descCargo VARCHAR(100),
	numeroRuc VARCHAR(200),
	fechaIngreso DATE,
	
	FOREIGN KEY (idProveedor) REFERENCES proveedor(idProveedor)
);

CREATE TABLE proveedor_organo_administrativo(
	idProveedorOrganoAdministrativo INT PRIMARY KEY AUTO_INCREMENT,
	idProveedor INT NOT NULL,
	
	idOrgano INT,
	codigoRegistro VARCHAR(20),
	codigoDocIde VARCHAR(10),
	descDocIde VARCHAR(50),
	siglaDocIde VARCHAR(20),
	nroDocumento VARCHAR(20),
	apellidosNomb VARCHAR(200),
	idTipoOrgano INT,
	descTipoOrgano VARCHAR(100),
	idCargo INT,
	descCargo VARCHAR(100),
	fechaIngreso DATE,	
	
	FOREIGN KEY (idProveedor) REFERENCES proveedor(idProveedor)
);

-- ------------------------
CREATE TABLE log_trazado_api_proveedores(
	idTrazadoApiProveedores INT PRIMARY KEY AUTO_INCREMENT,
	idProveedor INT,
	consulta_token_ruc DOUBLE,
	peticion_api_1 DOUBLE,
	peticion_api_2 DOUBLE,
	conversion DOUBLE,
	registro_bd DOUBLE,
	total DOUBLE
);

CREATE TABLE log_proveedores(
	idLogProveedores INT PRIMARY KEY AUTO_INCREMENT,
	idProveedor INT,
	ruc VARCHAR(20),
	fecha_registro DATETIME,
	api_1 JSON,
	api_2 JSON
);

CREATE TABLE log_aplicacion(
	idLogAplicacion INT PRIMARY KEY AUTO_INCREMENT,
	metodo VARCHAR(20),
	ruta VARCHAR(100),
	parametros_entrada VARCHAR(200),
	ip_usuario VARCHAR(100),
	comentario VARCHAR(200),
	tipo VARCHAR(50),
	detalle TEXT,
	fechahora DATETIME
);

-- CAMBIOS AGOSTO 2022
CREATE TABLE estado_usuario(
	idEstadoUsuario INT PRIMARY KEY,
	descripcion VARCHAR(100)
);

CREATE TABLE usuario(
	idUsuario INT PRIMARY KEY AUTO_INCREMENT,
	nombres VARCHAR(100),
	apellidoPaterno VARCHAR(100),
	apellidoMaterno VARCHAR(100),
	usuario VARCHAR(100),
	contrasena VARCHAR(500),
	contrasenaInicial VARCHAR(100),
	flagObligarCambiarContrasena BOOL,
	idEstadoUsuario INT NOT NULL,
	FOREIGN KEY (idEstadoUsuario) REFERENCES estado_usuario(idEstadoUsuario)
);

INSERT INTO estado_usuario VALUES (1, 'ACTIVADO');
INSERT INTO estado_usuario VALUES (2, 'DESACTIVADO');

INSERT INTO usuario(nombres, apellidoPaterno, apellidoMaterno, usuario, contrasena, contrasenaInicial, flagObligarCambiarContrasena, idEstadoUsuario)
	VALUES ('jairo', 'navez', 'aroca', 'jnavez', '', '123456', TRUE, 1);



-- ------------------------

-- SUPERCONSULTA DE TIEMPOS

SELECT 
--		l.idTrazadoApiProveedores AS id,
--		l.idProveedor,
---		FORMAT(l.consulta_token_ruc,	4) AS consulta_token_ruc,
---		FORMAT(l.peticion_api_1,		4) AS peticion_api_1,
---		FORMAT(l.peticion_api_2,		4) AS peticion_api_2,
---		FORMAT(l.conversion,				4) AS conversion,
---		FORMAT(l.registro_bd,			4) AS registro_bd,
---		FORMAT(l.total,					4) AS sumatoria,
---		FORMAT(l.consulta_token_ruc + l.peticion_api_1 + l.peticion_api_2 + l.conversion + l.registro_bd, 4) AS total
--		COUNT(*) AS registros,
--		AVG(consulta_token_ruc) AS consulta_token_ruc,
--		AVG(peticion_api_1) AS peticion_api_1,
--		AVG(peticion_api_2) AS peticion_api_2,
--		AVG(conversion) AS conversion,
--		AVG(registro_bd) AS registro_bd,
--		AVG(total) AS total
	FROM log_trazado_api_proveedores l ORDER BY 1 DESC;

	
DROP PROCEDURE if EXISTS RESETEAR_DATOS_PROVEEDOR;
DELIMITER //
CREATE PROCEDURE RESETEAR_DATOS_PROVEEDOR(
	_idProveedor INT
)
BEGIN
	DELETE FROM proveedor_email WHERE idProveedor = _idProveedor;
	DELETE FROM proveedor_telefono WHERE idProveedor = _idProveedor;
	DELETE FROM proveedor_antecedente WHERE idProveedor = _idProveedor;
	DELETE FROM proveedor_socio WHERE idProveedor = _idProveedor;
	DELETE FROM proveedor_representante WHERE idProveedor = _idProveedor;
	DELETE FROM proveedor_organo_administrativo WHERE idProveedor = _idProveedor;	
END
//
DELIMITER ;


*/
