<?php
	/**
	 * Indicamos todos modulos utilizados en el gestor y las funcionalidades asociadas.
	 * @author eData
	 * @version 4.0
	 * 
	 */

	/**
	 * 
	 * Definimos la ruta donde se situan las secciones del gestor
	 * @var string
	 */
	define("SECTION_PATH","sections/");

	/**
	 * 
	 * Inclusion del fichero que almacena las definiciones de las secciones de la aplicacion
	 * 
	 */
	require "sections.php";
	/**
	 * 
	 * Inclusion del fichero que almacena las definiciones de acciones de la aplicacion
	 * 
	 */
	require "actions.php";
	
	/**
	 * 
	 * Guardaremos para cada seccion-accion la ruta al script asociado para su funcionamiento.
	 * @var array
	 * 
	 */
	$controller=Array();
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion menu
	 * 
	 */
	$controller[SECTION_MENU][ACTION_VIEW]=SECTION_PATH.SECTION_MENU_PATH."view_menu.php";
	$controller[SECTION_MENU][ACTION_CREATE]=SECTION_PATH.SECTION_MENU_PATH."create_menu.php";
	$controller[SECTION_MENU][ACTION_EDIT]=SECTION_PATH.SECTION_MENU_PATH."edit_menu.php";
	$controller[SECTION_MENU]['ID_SECTION']=-1;
	
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion noticias
	 * 
	 */
	$controller[SECTION_NEW][ACTION_VIEW]=SECTION_PATH.SECTION_NEW_PATH."view_new.php";
	$controller[SECTION_NEW][ACTION_CREATE]=SECTION_PATH.SECTION_NEW_PATH."create_new.php";
	$controller[SECTION_NEW][ACTION_EDIT]=SECTION_PATH.SECTION_NEW_PATH."edit_new.php";
	$controller[SECTION_NEW]['ID_SECTION']=-1;
	
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion conference
	 * 
	 */
	$controller[SECTION_CONFERENCE][ACTION_VIEW]=SECTION_PATH.SECTION_CONFERENCE_PATH."view_conference.php";
	$controller[SECTION_CONFERENCE][ACTION_CREATE]=SECTION_PATH.SECTION_CONFERENCE_PATH."create_conference.php";
	$controller[SECTION_CONFERENCE][ACTION_EDIT]=SECTION_PATH.SECTION_CONFERENCE_PATH."edit_conference.php";
	$controller[SECTION_CONFERENCE][ACTION_GENERATE]=SECTION_PATH.SECTION_CONFERENCE_PATH."generate_certificates.php";
	$controller[SECTION_CONFERENCE][ACTION_SEND]=SECTION_PATH.SECTION_CONFERENCE_PATH."send_certificates.php";
    $controller[SECTION_CONFERENCE][ACTION_BADGES]=SECTION_PATH.SECTION_CONFERENCE_PATH."generate_badges.php";
    $controller[SECTION_CONFERENCE][ACTION_DINNER]=SECTION_PATH.SECTION_CONFERENCE_PATH."generate_dinner_inserts.php";
	$controller[SECTION_CONFERENCE]['ID_SECTION']=-1;
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion conference registered
	 * 
	 */
	$controller[SECTION_CONFERENCE_REGISTERED][ACTION_VIEW]=SECTION_PATH.SECTION_CONFERENCE_REGISTERED_PATH."view_conference_registered.php";
	$controller[SECTION_CONFERENCE_REGISTERED][ACTION_EDIT]=SECTION_PATH.SECTION_CONFERENCE_REGISTERED_PATH."edit_conference_registered.php";
	$controller[SECTION_CONFERENCE_REGISTERED]['ID_SECTION']=-1;
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion workshop
	 * 
	 */
	$controller[SECTION_WORKSHOP][ACTION_VIEW]=SECTION_PATH.SECTION_WORKSHOP_PATH."view_workshop.php";
	$controller[SECTION_WORKSHOP][ACTION_CREATE]=SECTION_PATH.SECTION_WORKSHOP_PATH."create_workshop.php";
	$controller[SECTION_WORKSHOP][ACTION_EDIT]=SECTION_PATH.SECTION_WORKSHOP_PATH."edit_workshop.php";
	$controller[SECTION_WORKSHOP][ACTION_GENERATE]=SECTION_PATH.SECTION_WORKSHOP_PATH."generate_certificates.php";
	$controller[SECTION_WORKSHOP][ACTION_SEND]=SECTION_PATH.SECTION_WORKSHOP_PATH."send_certificates.php";
	$controller[SECTION_WORKSHOP][ACTION_BADGES]=SECTION_PATH.SECTION_WORKSHOP_PATH."generate_ws_badges.php";
	$controller[SECTION_WORKSHOP]['ID_SECTION']=-1;
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion workshop date
	 * 
	 */
	$controller[SECTION_WORKSHOP_DATE][ACTION_VIEW]=SECTION_PATH.SECTION_WORKSHOP_DATE_PATH."view_workshop_date.php";
	$controller[SECTION_WORKSHOP_DATE][ACTION_EDIT]=SECTION_PATH.SECTION_WORKSHOP_DATE_PATH."edit_workshop_date.php";
	$controller[SECTION_WORKSHOP_DATE]['ID_SECTION']=-1;
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion workshop email
	 * 
	 */
	$controller[SECTION_WORKSHOP_EMAIL][ACTION_VIEW]=SECTION_PATH.SECTION_WORKSHOP_EMAIL_PATH."email_settings.php";
	$controller[SECTION_WORKSHOP_DATE]['ID_SECTION']=-1;

	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion tematica
	 * 
	 */
	$controller[SECTION_THEMATIC][ACTION_VIEW]=SECTION_PATH.SECTION_THEMATIC_PATH."view_thematic.php";
	$controller[SECTION_THEMATIC][ACTION_CREATE]=SECTION_PATH.SECTION_THEMATIC_PATH."create_thematic.php";
	$controller[SECTION_THEMATIC][ACTION_EDIT]=SECTION_PATH.SECTION_THEMATIC_PATH."edit_thematic.php";
	$controller[SECTION_THEMATIC]['ID_SECTION']=3;
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion jobs
	 * 
	 */
	$controller[SECTION_JOB][ACTION_VIEW]=SECTION_PATH.SECTION_JOB_PATH."view_job.php";
	$controller[SECTION_JOB][ACTION_CREATE]=SECTION_PATH.SECTION_JOB_PATH."create_job.php";
	$controller[SECTION_JOB][ACTION_EDIT]=SECTION_PATH.SECTION_JOB_PATH."edit_job.php";
	$controller[SECTION_JOB]['ID_SECTION']=-1;
	
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion movimientos
	 * 
	 */
	$controller[SECTION_MOVEMENT][ACTION_VIEW]=SECTION_PATH.SECTION_MOVEMENT_PATH."view_movement.php";
	$controller[SECTION_MOVEMENT][ACTION_CREATE]=SECTION_PATH.SECTION_MOVEMENT_PATH."create_movement.php";
	$controller[SECTION_MOVEMENT][ACTION_EDIT]=SECTION_PATH.SECTION_MOVEMENT_PATH."edit_movement.php";
	$controller[SECTION_MOVEMENT]['ID_SECTION']=-1;
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion concepto
	 * 
	 */
	$controller[SECTION_CONCEPT][ACTION_VIEW]=SECTION_PATH.SECTION_CONCEPT_PATH."view_concept.php";
	$controller[SECTION_CONCEPT][ACTION_CREATE]=SECTION_PATH.SECTION_CONCEPT_PATH."create_concept.php";
	$controller[SECTION_CONCEPT][ACTION_EDIT]=SECTION_PATH.SECTION_CONCEPT_PATH."edit_concept.php";
	$controller[SECTION_CONCEPT]['ID_SECTION']=-1;
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion tipo pago
	 * 
	 */
	$controller[SECTION_PAYMENT_TYPE][ACTION_VIEW]=SECTION_PATH.SECTION_PAYMENT_TYPE_PATH."view_payment_type.php";
	$controller[SECTION_PAYMENT_TYPE][ACTION_CREATE]=SECTION_PATH.SECTION_PAYMENT_TYPE_PATH."create_payment_type.php";
	$controller[SECTION_PAYMENT_TYPE][ACTION_EDIT]=SECTION_PATH.SECTION_PAYMENT_TYPE_PATH."edit_payment_type.php";
	$controller[SECTION_PAYMENT_TYPE]['ID_SECTION']=-1;

	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion buttons
	 * 
	 */
	$controller[SECTION_BUTTONS][ACTION_VIEW]=SECTION_PATH.SECTION_BUTTONS_PATH."view_buttons.php";
	$controller[SECTION_BUTTONS]['ID_SECTION']=-1;

	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion workshop buttons
	 * 
	 */
	$controller[SECTION_WS_BUTTONS][ACTION_VIEW]=SECTION_PATH.SECTION_WS_BUTTONS_PATH."view_buttons.php";
	$controller[SECTION_WS_BUTTONS]['ID_SECTION']=-1;
	
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion factura
	 * 
	 */
	$controller[SECTION_INVOICE][ACTION_VIEW]=SECTION_PATH.SECTION_INVOICE_PATH."view_invoice.php";
	$controller[SECTION_INVOICE][ACTION_CREATE]=SECTION_PATH.SECTION_INVOICE_PATH."create_invoice.php";
	$controller[SECTION_INVOICE][ACTION_EDIT]=SECTION_PATH.SECTION_INVOICE_PATH."edit_invoice.php";
	$controller[SECTION_INVOICE][ACTION_GENERATE]=SECTION_PATH.SECTION_INVOICE_PATH."generate_invoice.php";
	$controller[SECTION_INVOICE][ACTION_GENERATE_BATCH]=SECTION_PATH.SECTION_INVOICE_PATH."generate_batch_invoice.php";
	$controller[SECTION_INVOICE][ACTION_DOWNLOAD]=SECTION_PATH.SECTION_INVOICE_PATH."download_invoice.php";
	$controller[SECTION_INVOICE][ACTION_SEND]=SECTION_PATH.SECTION_INVOICE_PATH."send_invoice.php";
	$controller[SECTION_INVOICE][ACTION_SEND_ALL]=SECTION_PATH.SECTION_INVOICE_PATH."send_all_invoice.php";
	$controller[SECTION_INVOICE][ACTION_DELETE_PDF]=SECTION_PATH.SECTION_INVOICE_PATH."delete_pdfs.php";
	$controller[SECTION_INVOICE][ACTION_VERIFACTI_LOG]=SECTION_PATH.SECTION_INVOICE_PATH."view_verifacti_log.php";
	$controller[SECTION_INVOICE]['ID_SECTION']=-1;

	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion menu seo
	 * 
	 */
	$controller[SECTION_MENU_SEO][ACTION_CREATE]=SECTION_PATH.SECTION_MENU_SEO_PATH."create_menu_seo.php";
	$controller[SECTION_MENU_SEO]['ID_SECTION']=-1;
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion usuarios
	 * 
	 */
	$controller[SECTION_USER][ACTION_VIEW]=SECTION_PATH.SECTION_USER_PATH."view_user.php";
	$controller[SECTION_USER][ACTION_CREATE]=SECTION_PATH.SECTION_USER_PATH."create_user.php";
	$controller[SECTION_USER][ACTION_EDIT]=SECTION_PATH.SECTION_USER_PATH."edit_user.php";
	$controller[SECTION_USER][ACTION_PROFILE]=SECTION_PATH.SECTION_USER_PATH."profile_user.php";
	$controller[SECTION_USER]['ID_SECTION']=-1;
	
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion miembros
	 * 
	 */
	$controller[SECTION_MEMBER][ACTION_VIEW]=SECTION_PATH.SECTION_MEMBER_PATH."view_member.php";
	$controller[SECTION_MEMBER][ACTION_CREATE]=SECTION_PATH.SECTION_MEMBER_PATH."create_member.php";
	$controller[SECTION_MEMBER][ACTION_EDIT]=SECTION_PATH.SECTION_MEMBER_PATH."edit_member.php";
	$controller[SECTION_MEMBER]['ID_SECTION']=-1;
	
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con los miembros institucionales
	 * 
	 */
	$controller[SECTION_SUCCESS][ACTION_VIEW]=SECTION_PATH.SECTION_SUCCESS_PATH."view_success.php";
	$controller[SECTION_SUCCESS][ACTION_CREATE]=SECTION_PATH.SECTION_SUCCESS_PATH."create_success.php";
	$controller[SECTION_SUCCESS][ACTION_EDIT]=SECTION_PATH.SECTION_SUCCESS_PATH."edit_success.php";
	$controller[SECTION_SUCCESS]['ID_SECTION']=-1;
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion boton de acceso
	 * 
	 */
	$controller[SECTION_ACCESS_BUTTON][ACTION_VIEW]=SECTION_PATH.SECTION_ACCESS_BUTTON_PATH."view_access_button.php";
	$controller[SECTION_ACCESS_BUTTON][ACTION_CREATE]=SECTION_PATH.SECTION_ACCESS_BUTTON_PATH."create_access_button.php";
	$controller[SECTION_ACCESS_BUTTON][ACTION_EDIT]=SECTION_PATH.SECTION_ACCESS_BUTTON_PATH."edit_access_button.php";
	$controller[SECTION_ACCESS_BUTTON]['ID_SECTION']=-1;
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion agenda
	 * 
	 */
	$controller[SECTION_DIARY][ACTION_VIEW]=SECTION_PATH.SECTION_DIARY_PATH."view_diary.php";
	$controller[SECTION_DIARY][ACTION_CREATE]=SECTION_PATH.SECTION_DIARY_PATH."create_diary.php";
	$controller[SECTION_DIARY][ACTION_EDIT]=SECTION_PATH.SECTION_DIARY_PATH."edit_diary.php";
	$controller[SECTION_DIARY]['ID_SECTION']=-1;
	
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion eletter
	 * 
	 */
	$controller[SECTION_ELETTER][ACTION_VIEW]=SECTION_PATH.SECTION_ELETTER_PATH."view_eletter.php";
	$controller[SECTION_ELETTER][ACTION_CREATE]=SECTION_PATH.SECTION_ELETTER_PATH."create_eletter.php";
	$controller[SECTION_ELETTER][ACTION_EDIT]=SECTION_PATH.SECTION_ELETTER_PATH."edit_eletter.php";
	$controller[SECTION_ELETTER][ACTION_SEND]=SECTION_PATH.SECTION_ELETTER_PATH."send_eletter.php";
    $controller[SECTION_ELETTER]['ID_SECTION']=-1;
	
	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion history_email
	 * 
	 */
	$controller[SECTION_HISTORY_EMAIL][ACTION_VIEW]=SECTION_PATH.SECTION_HISTORY_EMAIL_PATH."view_history_email.php";
	$controller[SECTION_HISTORY_EMAIL][ACTION_EDIT]=SECTION_PATH.SECTION_HISTORY_EMAIL_PATH."edit_history_email.php";
    $controller[SECTION_HISTORY_EMAIL]['ID_SECTION']=-1;

	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion history eletter
	 * 
	 */
	$controller[SECTION_HISTORY_ELETTER][ACTION_VIEW]=SECTION_PATH.SECTION_HISTORY_ELETTER_PATH."view_history_eletter.php";
	$controller[SECTION_HISTORY_ELETTER][ACTION_EDIT]=SECTION_PATH.SECTION_HISTORY_ELETTER_PATH."edit_history_eletter.php";
    $controller[SECTION_HISTORY_ELETTER]['ID_SECTION']=-1;

	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion settings
	 * 
	 */
	$controller[SECTION_SETTINGS][ACTION_VIEW]=SECTION_PATH.SECTION_SETTINGS_PATH."global_settings.php";
    $controller[SECTION_SETTINGS]['ID_SECTION']=-1;

	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion settings
	 * 
	 */
	$controller[SECTION_GDPR][ACTION_VIEW]=SECTION_PATH.SECTION_GDPR_PATH."view_gdpr.php";
    $controller[SECTION_GDPR]['ID_SECTION']=-1;

	/**
	 * 
	 * Almacenamos las acciones asociadas con la seccion programme
	 * 
	 */
	// $controller[SECTION_PROGRAMME][ACTION_VIEW]=SECTION_PATH.SECTION_PROGRAMME_PATH."edit_programme.php";
    // $controller[SECTION_PROGRAMME]['ID_SECTION']=-1;
?>