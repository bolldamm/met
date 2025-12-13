<?php
require "includes/load_main_components.inc.php";

if($_SESSION["met_user"]["tipoUsuario"] < TIPO_USUARIO_EDITOR)
{
	if(!isset($_SESSION["registration_desk"]))
		{
			generalUtils::redirigir(CURRENT_DOMAIN);
		}
}

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/includes/insert_generator.php';
?>