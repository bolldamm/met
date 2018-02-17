<?php
/* Deletes the personal information of members who have been lapsed since specified date*/

	require "../classes/databaseConnection.php";
	require "../database/connection.php";

$date='2014-12-31';

$result1 = mysql_query('
DELETE FROM ed_tb_usuario_web_actividad_profesional
WHERE id_usuario_web IN(
    SELECT DISTINCT u.id_usuario_web
    FROM ed_tb_usuario_web u
    LEFT JOIN ed_tb_inscripcion i ON u.id_usuario_web=i.id_usuario_web
    WHERE i.pagado=1 
    AND 
        (
            SELECT MAX(iAux.fecha_finalizacion)
            FROM ed_tb_usuario_web uAux
            INNER JOIN ed_tb_inscripcion iAux ON uAux.id_usuario_web=iAux.id_usuario_web
            WHERE uAux.id_usuario_web=u.id_usuario_web      
        )< $date
)
');
if (!$result1) {
    die('Result-1 not valid: ' . mysql_error());
}
mysql_free_result($result1);

$result2 = mysql_query('
DELETE FROM ed_tb_usuario_web_situacion_laboral
WHERE id_usuario_web IN(
    SELECT DISTINCT u.id_usuario_web
    FROM ed_tb_usuario_web u
    LEFT JOIN ed_tb_inscripcion i ON u.id_usuario_web=i.id_usuario_web
    WHERE i.pagado=1 
    AND 
        (
            SELECT MAX(iAux.fecha_finalizacion)
            FROM ed_tb_usuario_web uAux
            INNER JOIN ed_tb_inscripcion iAux ON uAux.id_usuario_web=iAux.id_usuario_web
            WHERE uAux.id_usuario_web=u.id_usuario_web      
        )< $date
)
');
if (!$result2) {
    die('Result-2 not valid: ' . mysql_error());
}
mysql_free_result($result2);

$result3 = mysql_query('
DELETE FROM ed_tb_usuario_web_areas_of_expertise
WHERE id_usuario_web IN(
    SELECT DISTINCT u.id_usuario_web
    FROM ed_tb_usuario_web u
    LEFT JOIN ed_tb_inscripcion i ON u.id_usuario_web=i.id_usuario_web
    WHERE i.pagado=1 
    AND 
        (
            SELECT MAX(iAux.fecha_finalizacion)
            FROM ed_tb_usuario_web uAux
            INNER JOIN ed_tb_inscripcion iAux ON uAux.id_usuario_web=iAux.id_usuario_web
            WHERE uAux.id_usuario_web=u.id_usuario_web      
        )< $date
)
');
if (!$result3) {
    die('Result-3 not valid: ' . mysql_error());
}
mysql_free_result($result3);

$result4 = mysql_query('
DELETE FROM ed_tb_usuario_web_working_languages
WHERE id_usuario_web IN(
    SELECT DISTINCT u.id_usuario_web
    FROM ed_tb_usuario_web u
    LEFT JOIN ed_tb_inscripcion i ON u.id_usuario_web=i.id_usuario_web
    WHERE i.pagado=1 
    AND 
        (
            SELECT MAX(iAux.fecha_finalizacion)
            FROM ed_tb_usuario_web uAux
            INNER JOIN ed_tb_inscripcion iAux ON uAux.id_usuario_web=iAux.id_usuario_web
            WHERE uAux.id_usuario_web=u.id_usuario_web      
        )< $date
)
');
if (!$result4) {
    die('Result-4 not valid: ' . mysql_error());
}
mysql_free_result($result4);

$result5 = mysql_query('
DELETE FROM ed_tb_usuario_web_individual
WHERE id_usuario_web IN(
    SELECT DISTINCT u.id_usuario_web
    FROM ed_tb_usuario_web u
    LEFT JOIN ed_tb_inscripcion i ON u.id_usuario_web=i.id_usuario_web
    WHERE i.pagado=1 
    AND 
        (
            SELECT MAX(iAux.fecha_finalizacion)
            FROM ed_tb_usuario_web uAux
            INNER JOIN ed_tb_inscripcion iAux ON uAux.id_usuario_web=iAux.id_usuario_web
            WHERE uAux.id_usuario_web=u.id_usuario_web      
        )< $date
)
');
if (!$result5) {
    die('Result-5 not valid: ' . mysql_error());
}
mysql_free_result($result5);

$result6 = mysql_query('
DELETE FROM ed_tb_usuario_web_aux
WHERE id_usuario_web IN(
    SELECT DISTINCT u.id_usuario_web
    FROM ed_tb_usuario_web u
    LEFT JOIN ed_tb_inscripcion i ON u.id_usuario_web=i.id_usuario_web
    WHERE i.pagado=1 
    AND 
        (
            SELECT MAX(iAux.fecha_finalizacion)
            FROM ed_tb_usuario_web uAux
            INNER JOIN ed_tb_inscripcion iAux ON uAux.id_usuario_web=iAux.id_usuario_web
            WHERE uAux.id_usuario_web=u.id_usuario_web      
        )< $date
)
');
if (!$result6) {
    die('Result-6 not valid: ' . mysql_error());
}
mysql_free_result($result6);

$result7 = mysql_query('
UPDATE ed_tb_usuario_web 
SET correo_electronico = "", otros = "", descripcion = "", publicaciones = "", web = "", imagen = "", nif_cliente_factura = "", nombre_cliente_factura = "", nombre_empresa_factura = "", direccion_factura = "", codigo_postal_factura = "", ciudad_factura = "", provincia_factura = "", pais_factura = "", borrado = 1
WHERE id_usuario_web IN(
    SELECT DISTINCT u.id_usuario_web
    FROM ed_tb_usuario_web u
    LEFT JOIN ed_tb_inscripcion i ON u.id_usuario_web=i.id_usuario_web
    WHERE i.pagado=1 
    AND 
        (
            SELECT MAX(iAux.fecha_finalizacion)
            FROM ed_tb_usuario_web uAux
            INNER JOIN ed_tb_inscripcion iAux ON uAux.id_usuario_web=iAux.id_usuario_web
            WHERE uAux.id_usuario_web=u.id_usuario_web      
        )< $date
)
');
if (!$result7) {
    die('Result-7 not valid: ' . mysql_error());
}else{
    echo "All done!";
}
mysql_free_result($result7);

?>