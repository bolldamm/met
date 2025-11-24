<?php
	/**
	 * 
	 * Presentamos por pantalla este formulario
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */
header("Location: http://www.metmeetings.org/en/registration-closed-:919");
	die();
	if(isset($_SESSION["met_user"])){
		
		header("Location: http://www.metmeetings.org/en/registration-info:914");
die();
		}
		else{
			header("Location: http://www.metmeetings.org/en/are-you-a-member:916");
die();
			
			}
			
			
?>
