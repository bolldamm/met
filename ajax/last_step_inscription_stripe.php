<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 18/11/2018
 * Time: 10:34
 */

//NB path is relative to stripe_button.html
require "includes/load_main_components.inc.php";

$idInscripcion = $_SESSION["idInscripcion"];
$numeroPedidoInscripcion = $_SESSION["numeroPedidoInscripcion"];

//Retrieve registration details from database and store in variables
$resultadoInscripcion=$db->callProcedure("CALL ed_sp_web_inscripcion_obtener_concreta(".$idInscripcion.")");
$datosInscripcion=$db->getData($resultadoInscripcion);
$importe=$datosInscripcion["importe"];
$idUsuarioWeb=$datosInscripcion["id_usuario_web"];
$idUsuarioWebCorreo=$idUsuarioWeb;
$emailUsuario=$datosInscripcion["correo_electronico"];
$esFactura=1;
$fechaInscripcion=$datosInscripcion["fecha_inscripcion"];
$fechaFinalizacion=$datosInscripcion["fecha_finalizacion"];

//Store billing information in variables
$nifFactura=generalUtils::escaparCadena($datosInscripcion["nif_cliente_factura"]);
$nombreClienteFactura=generalUtils::escaparCadena($datosInscripcion["nombre_cliente_factura"]);
$nombreEmpresaFactura=generalUtils::escaparCadena($datosInscripcion["nombre_empresa_factura"]);
$direccionFactura=generalUtils::escaparCadena($datosInscripcion["direccion_factura"]);
$codigoPostalFactura=generalUtils::escaparCadena($datosInscripcion["codigo_postal_factura"]);
$ciudadFactura=generalUtils::escaparCadena($datosInscripcion["ciudad_factura"]);
$provinciaFactura=generalUtils::escaparCadena($datosInscripcion["provincia_factura"]);
$paisFactura=generalUtils::escaparCadena($datosInscripcion["pais_factura"]);

//Store name in variables
$nombreUsuario=$datosInscripcion["nombre"];
$apellidosUsuario=$datosInscripcion["apellidos"];

//Assign appropriate subaccount (regular or student/over-65)
$idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP;
if($datosInscripcion["id_situacion_adicional"]==SITUACION_ADICIONAL_JUBILADO){
    $idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_RETIRED;
}else if($datosInscripcion["id_situacion_adicional"]==SITUACION_ADICIONAL_ESTUDIANTE){
    $idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_MEMBERSHIP_STUDENT;
}

//Open database connection
$db->startTransaction();

//Store Stripe transaction details in ed_tb_inscripcion_stripe
$resultado=$db->callProcedure("CALL ed_sp_web_inscripcion_stripe_guardar(".$idInscripcion.",'".$txnId."')");

//Update registration status from Pending to Confirmed
$idEstado=INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;
$resultado=$db->callProcedure("CALL ed_sp_web_inscripcion_estado_actualizar(".$idEstado.",'".$numeroPedidoInscripcion."')");

//Update registration status from Unpaid to Paid
$pagado=1;
$resultado=$db->callProcedure("CALL ed_sp_web_inscripcion_pagado_actualizar(".$pagado.",'".$numeroPedidoInscripcion."')");

//Update "email sent" status to Sent
$resultado=$db->callProcedure("CALL ed_sp_web_inscripcion_email_enviado_actualizar('".$numeroPedidoInscripcion."')");

//Run mail script (NB path is relative to last_step_inscription.inc.php)
require "includes/load_send_mail_inscription.inc.php";

//Close database connection
$db->endTransaction();


