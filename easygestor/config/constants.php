<?php
	// CURRENT_DOMAIN is now defined in includes/load_environment.inc.php
	// This ensures it's available everywhere, including index.php and authentication.php


	//Tipos usuario
    define("TIPO_USUARIO_SOCIO",1);
    define("TIPO_USUARIO_EDITOR",2);
    define("TIPO_USUARIO_CONSEJO",3);
    define("TIPO_USUARIO_ADMIN",4);
    define("TIPO_USUARIO_INVITADO",5);
	
	//Modalidad usuario
	define("MODALIDAD_USUARIO_INDIVIDUAL",1);
	define("MODALIDAD_USUARIO_INSTITUTIONAL",2);
	
	//Situacion adicional
	define("SITUACION_ADICIONAL_JUBILADO",1);
	define("SITUACION_ADICIONAL_ESTUDIANTE",2);
	
	//Precio modalidad usuario
	//define("PRECIO_MODALIDAD_USUARIO_INDIVIDUAL",38);
	//define("PRECIO_MODALIDAD_USUARIO_INDIVIDUAL_DESCUENTO",19);
	//define("PRECIO_MODALIDAD_USUARIO_INSTITUTIONAL",100);
	
	//Estado de la inscripcion
	define("INSCRIPCION_ESTADO_INSCRIPCION_PENDIENTE",1);
	define("INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA",2);
	define("INSCRIPCION_ESTADO_INSCRIPCION_DESCARTADA",3);
	
	//Tipos de pago
	define("INSCRIPCION_TIPO_PAGO_TRANSFERENCIA",1);
	define("INSCRIPCION_TIPO_PAGO_PAYPAL",2);
	define("INSCRIPCION_TIPO_PAGO_OTROS",3);
	define("INSCRIPCION_TIPO_PAGO_DEBIT",4);
	define("INSCRIPCION_TIPO_PAGO_TRANSFERENCIA_DESCRIPTION","Bank transfer");
	define("INSCRIPCION_TIPO_PAGO_PAYPAL_DESCRIPTION","Debit/credit card");
	define("INSCRIPCION_TIPO_PAGO_OTROS_DESCRIPTION","Other");
	define("INSCRIPCION_TIPO_PAGO_DEBIT_DESCRIPTION","Direct debit");
	define("INSCRIPCION_TIPO_PAGO_TRANSFERENCIA_DESCRIPTIO","Transfer");
	define("INSCRIPCION_TIPO_PAGO_PAYPAL_DESCRIPTIO","PayPal");
	define("INSCRIPCION_TIPO_PAGO_OTROS_DESCRIPTIO","Other");
	define("INSCRIPCION_TIPO_PAGO_DEBIT_DESCRIPTIO","Debit");

	//Tipo correo electronico
	define("EMAIL_TYPE_CONTACT_FORM",1);
	define("EMAIL_TYPE_REMEMBER_PASSWORD_FORM",2);
	define("EMAIL_TYPE_EXPENSE_FORM",3);
	define("EMAIL_TYPE_EVENT_FORM",4);
	define("EMAIL_TYPE_JOB_FORM",5);
	define("EMAIL_TYPE_NEW_FORM",6);
	define("EMAIL_TYPE_INSCRIPTION_FORM",7);
	define("EMAIL_TYPE_JOB_FORM_REQUEST",8);
	define("EMAIL_TYPE_INSCRIPTION_RENEW_FORM",9);
	define("EMAIL_TYPE_INSCRIPTION_ACTIVATED",10);
	define("EMAIL_TYPE_NOMINEE_ACTIVATION",11);
	define("EMAIL_TYPE_FORM_METM_TRANSPORT",12);
	define("EMAIL_TYPE_ACCOMMODATION_FORM_USER",13);
	define("EMAIL_TYPE_ACCOMMODATION_FORM",14);
	define("EMAIL_TYPE_WORKSHOP_FORM_TO_MET",15);
	define("EMAIL_TYPE_WORKSHOP_FORM_TO_USER",16);
	define("EMAIL_TYPE_WORKSHOP_FORM_TO_USER_ACTIVATION",17);
	define("EMAIL_TYPE_INVOICE_SENT",18);
	define("EMAIL_TYPE_CONFERENCE_FORM_TO_MET",19);
	define("EMAIL_TYPE_CONFERENCE_FORM_TO_USER",20);
	define("EMAIL_TYPE_CONFERENCE_FORM_TO_USER_ACTIVATION",21);
	
	/******** INICIO: BREADCUMB ********/
		/******** INICIO: MENU BREADCUMB ********/
		define("STATIC_BREADCUMB_INICIO_LINK","main_app.php?section=menu&action=view");
		define("STATIC_BREADCUMB_MENU_CREATE_MENU_LINK","main_app.php?section=menu&action=create");
		define("STATIC_BREADCUMB_MENU_EDIT_MENU_LINK","main_app.php?section=menu&action=edit");
		/******** FINAL: MENU BREADCUMB ********/
		/******** INICIO: MENU SEO BREADCUMB ********/
		define("STATIC_BREADCUMB_MENU_SEO_CREATE_MENU_SEO_LINK","main_app.php?section=menu_seo&action=create");
		/******** FINAL: MENU SEO BREADCUMB ********/		
		/******** INICIO: USER_PROFILE BREADCUMB ********/
		define("STATIC_BREADCUMB_USER_PROFILE_LINK","main_app.php?section=user&action=profile");
		/******** FINAL: USER_PROFILE BREADCUMB ********/
		/******** INICIO: NEW BREADCUMB ********/
		define("STATIC_BREADCUMB_NEW_VIEW_NEW_LINK","main_app.php?section=new&action=view");
		define("STATIC_BREADCUMB_NEW_CREATE_NEW_LINK","main_app.php?section=new&action=create");
		define("STATIC_BREADCUMB_NEW_EDIT_NEW_LINK","main_app.php?section=new&action=edit");
		/******** FINAL: NEW BREADCUMB ********/
		/******** INICIO: CONFERENCE BREADCUMB ********/
		define("STATIC_BREADCUMB_CONFERENCE_VIEW_CONFERENCE_LINK","main_app.php?section=conference&action=view");
		define("STATIC_BREADCUMB_CONFERENCE_CREATE_CONFERENCE_LINK","main_app.php?section=conference&action=create");
		define("STATIC_BREADCUMB_CONFERENCE_EDIT_CONFERENCE_LINK","main_app.php?section=conference&action=edit");
		/******** FINAL: CONFERENCE BREADCUMB ********/
		
		/******** INICIO: CONFERENCE_REGISTERED BREADCUMB ********/
		define("STATIC_BREADCUMB_CONFERENCE_REGISTERED_VIEW_CONFERENCE_REGISTERED_LINK","main_app.php?section=conference_registered&action=view");
		define("STATIC_BREADCUMB_CONFERENCE_REGISTERED_CREATE_CONFERENCE_REGISTERED_LINK","main_app.php?section=conference_registered&action=create");
		define("STATIC_BREADCUMB_CONFERENCE_REGISTERED_EDIT_CONFERENCE_REGISTERED_LINK","main_app.php?section=conference_registered&action=edit");
		/******** FINAL: CONFERENCE_REGISTERED BREADCUMB ********/
		
		/******** INICIO: WORKSHOP BREADCUMB ********/
		define("STATIC_BREADCUMB_WORKSHOP_VIEW_WORKSHOP_LINK","main_app.php?section=workshop&action=view");
		define("STATIC_BREADCUMB_WORKSHOP_CREATE_WORKSHOP_LINK","main_app.php?section=workshop&action=create");
		define("STATIC_BREADCUMB_WORKSHOP_EDIT_WORKSHOP_LINK","main_app.php?section=workshop&action=edit");
		/******** FINAL: WORKSHOP BREADCUMB ********/
		/******** INICIO: WORKSHOP_DATE BREADCUMB ********/
		define("STATIC_BREADCUMB_WORKSHOP_DATE_VIEW_WORKSHOP_DATE_LINK","main_app.php?section=workshop_date&action=view");
		define("STATIC_BREADCUMB_WORKSHOP_DATE_CREATE_WORKSHOP_DATE_LINK","main_app.php?section=workshop_date&action=create");
		define("STATIC_BREADCUMB_WORKSHOP_DATE_EDIT_WORKSHOP_DATE_LINK","main_app.php?section=workshop_date&action=edit");
		/******** FINAL: WORKSHOP_DATE BREADCUMB ********/
		/******** INICIO: THEMATIC BREADCUMB ********/
		define("STATIC_BREADCUMB_THEMATIC_VIEW_THEMATIC_LINK","main_app.php?section=thematic&action=view");
		define("STATIC_BREADCUMB_THEMATIC_CREATE_THEMATIC_LINK","main_app.php?section=thematic&action=create");
		define("STATIC_BREADCUMB_THEMATIC_EDIT_THEMATIC_LINK","main_app.php?section=thematic&action=edit");
		/******** FINAL: THEMATIC BREADCUMB ********/
		/******** INICIO: JOB BREADCUMB ********/
		define("STATIC_BREADCUMB_JOB_VIEW_JOB_LINK","main_app.php?section=job&action=view");
		define("STATIC_BREADCUMB_JOB_CREATE_JOB_LINK","main_app.php?section=job&action=create");
		define("STATIC_BREADCUMB_JOB_EDIT_JOB_LINK","main_app.php?section=job&action=edit");
		/******** FINAL: JOB BREADCUMB ********/
		/******** INICIO: MOVEMENT BREADCUMB ********/
		define("STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_LINK","main_app.php?section=movement&action=view");
		define("STATIC_BREADCUMB_MOVEMENT_CREATE_MOVEMENT_LINK","main_app.php?section=movement&action=create");
		define("STATIC_BREADCUMB_MOVEMENT_EDIT_MOVEMENT_LINK","main_app.php?section=movement&action=edit");
		/******** FINAL: MOVEMENT BREADCUMB ********/
		/******** INICIO: INVOICE BREADCUMB ********/
		define("STATIC_BREADCUMB_INVOICE_VIEW_INVOICE_LINK","main_app.php?section=invoice&action=view");
		define("STATIC_BREADCUMB_INVOICE_CREATE_INVOICE_LINK","main_app.php?section=invoice&action=create");
		define("STATIC_BREADCUMB_INVOICE_EDIT_INVOICE_LINK","main_app.php?section=invoice&action=edit");
		define("STATIC_BREADCUMB_INVOICE_SEND_INVOICE_LINK","main_app.php?section=invoice&action=send");
		/******** FINAL: INVOICE BREADCUMB ********/
		/******** INICIO: CONCEPT BREADCUMB ********/
		define("STATIC_BREADCUMB_CONCEPT_VIEW_CONCEPT_LINK","main_app.php?section=concept&action=view");
		define("STATIC_BREADCUMB_CONCEPT_CREATE_CONCEPT_LINK","main_app.php?section=concept&action=create");
		define("STATIC_BREADCUMB_CONCEPT_EDIT_CONCEPT_LINK","main_app.php?section=concept&action=edit");
		/******** FINAL: CONCEPT BREADCUMB ********/
		/******** INICIO: PAYMENT_TYPE BREADCUMB ********/
		define("STATIC_BREADCUMB_PAYMENT_TYPE_VIEW_PAYMENT_TYPE_LINK","main_app.php?section=payment_type&action=view");
		define("STATIC_BREADCUMB_PAYMENT_TYPE_CREATE_PAYMENT_TYPE_LINK","main_app.php?section=payment_type&action=create");
		define("STATIC_BREADCUMB_PAYMENT_TYPE_EDIT_PAYMENT_TYPE_LINK","main_app.php?section=payment_type&action=edit");
		/******** FINAL: PAYMENT_TYPE BREADCUMB ********/
		/******** INICIO: NEW SUCCESS ********/
		define("STATIC_BREADCUMB_SUCCESS_VIEW_SUCCESS_LINK","main_app.php?section=success&action=view");
		define("STATIC_BREADCUMB_SUCCESS_CREATE_SUCCESS_LINK","main_app.php?section=success&action=create");
		define("STATIC_BREADCUMB_SUCCESS_EDIT_SUCCESS_LINK","main_app.php?section=success&action=edit");
		/******** FINAL: NEW SUCCESS ********/
		/******** INICIO: ACCESS_BUTTON BREADCUMB ********/
		define("STATIC_BREADCUMB_ACCESS_BUTTON_VIEW_ACCESS_BUTTON_LINK","main_app.php?section=access_button&action=view");
		define("STATIC_BREADCUMB_ACCESS_BUTTON_CREATE_ACCESS_BUTTON_LINK","main_app.php?section=access_button&action=create");
		define("STATIC_BREADCUMB_ACCESS_BUTTON_EDIT_ACCESS_BUTTON_LINK","main_app.php?section=access_button&action=edit");
		/******** FINAL: ACCESS_BUTTON BREADCUMB ********/
		/******** INICIO: DIARY BREADCUMB ********/
		define("STATIC_BREADCUMB_DIARY_VIEW_DIARY_LINK","main_app.php?section=diary&action=view");
		define("STATIC_BREADCUMB_DIARY_CREATE_DIARY_LINK","main_app.php?section=diary&action=create");
		define("STATIC_BREADCUMB_DIARY_EDIT_DIARY_LINK","main_app.php?section=diary&action=edit");
		/******** FINAL: DIARY BREADCUMB ********/
		/******** INICIO: MEMBER BREADCUMB ********/
		define("STATIC_BREADCUMB_MEMBER_VIEW_MEMBER_LINK","main_app.php?section=member&action=view");
		define("STATIC_BREADCUMB_MEMBER_CREATE_MEMBER_LINK","main_app.php?section=member&action=create");
		define("STATIC_BREADCUMB_MEMBER_EDIT_MEMBER_LINK","main_app.php?section=member&action=edit");
		/******** FINAL: MEMBER BREADCUMB ********/
		/******** INICIO: ELETTER BREADCUMB ********/
		define("STATIC_BREADCUMB_ELETTER_VIEW_ELETTER_LINK","main_app.php?section=eletter&action=view");
		define("STATIC_BREADCUMB_ELETTER_CREATE_ELETTER_LINK","main_app.php?section=eletter&action=create");
		define("STATIC_BREADCUMB_ELETTER_EDIT_ELETTER_LINK","main_app.php?section=eletter&action=edit");
		define("STATIC_BREADCUMB_ELETTER_SEND_ELETTER_LINK","main_app.php?section=eletter&action=send");
		/******** FINAL: ELETTER BREADCUMB ********/
		/******** INICIO: HISTORY_EMAIL BREADCUMB ********/
		define("STATIC_BREADCUMB_HISTORY_EMAIL_VIEW_HISTORY_EMAIL_LINK","main_app.php?section=history_email&action=view");
		define("STATIC_BREADCUMB_HISTORY_EMAIL_EDIT_HISTORY_EMAIL_LINK","main_app.php?section=history_email&action=edit");
		/******** FINAL: HISTORY_EMAIL BREADCUMB ********
		/******** INICIO: HISTORY ELETTER BREADCUMB ********/
		define("STATIC_BREADCUMB_HISTORY_ELETTER_VIEW_HISTORY_ELETTER_LINK","main_app.php?section=history_eletter&action=view");
		define("STATIC_BREADCUMB_HISTORY_ELETTER_EDIT_HISTORY_ELETTER_LINK","main_app.php?section=history_eletter&action=edit");
		/******** FINAL: HISTORY ELETTER BREADCUMB ********
	/******** FINAL: BREADCUMB ********/
	
	define("DEFAULT_FILTER_NUMBER",20);
	define("MAX_FILTER_NUMBER",50);
	
	/******** INICIO: IMAGENES CABECERA ********/
	define("MAX_IMG_MENU_HEADER",4);
	define("WIDTH_SIZE_MENU_HEADER",870);
	define("HEIGHT_SIZE_MENU_HEADER",169);
	/******** FINAL: IMAGENES CABECERA ********/
	
	/******** INICIO: IMAGENES BACKGROUND ********/
	define("MAX_IMG_MENU_BACKGROUND",4);
	define("WIDTH_SIZE_MENU_BACKGROUND",1920);
	define("HEIGHT_SIZE_MENU_BACKGROUND",455);
	/******** FINAL: IMAGENES BACKGROUND ********/
	
	/******** INICIO: IMAGENES NEW ********/
	define("WIDTH_SIZE_NEW",237);
	define("HEIGHT_SIZE_NEW",96);
	/******** FINAL: IMAGENES NEW ********/	
	
	/******** INICIO: IMAGENES SUCCESS ********/
	define("WIDTH_SIZE_SUCCESS",237);
	define("HEIGHT_SIZE_SUCCESS",96);
	/******** FINAL: IMAGENES SUCCESS ********/	
	
	/******** INICIO: IMAGENES CUSTOMER ********/
	define("WIDTH_SIZE_CUSTOMER",237);
	define("HEIGHT_SIZE_CUSTOMER",96);
	/******** FINAL: IMAGENES CUSTOMER ********/	
	
	/******** INICIO: IMAGENES PARTNER ********/
	define("WIDTH_SIZE_PARTNER",237);
	define("HEIGHT_SIZE_PARTNER",96);
	/******** FINAL: IMAGENES PARTNER ********/	
	
	
	/******** INICIO: IMAGENES BOTON ACCESO ********/
	define("WIDTH_SIZE_ACCESS_BUTTON",254);
	define("HEIGHT_SIZE_ACCESS_BUTTON",254);
	/******** FINAL: IMAGENES BOTON ACCESO ********/
	
	
	/******** INICIO: IMAGENES MEMBER ********/
	define("WIDTH_SIZE_MEMBER_INDIVIDUAL_THUMB", 83);
	define("HEIGHT_SIZE_MEMBER_INDIVIDUAL_THUMB", 83);
	
	define("WIDTH_SIZE_MEMBER_INDIVIDUAL", 200);
	define("HEIGHT_SIZE_MEMBER_INDIVIDUAL", 200);
	
	define("WIDTH_SIZE_MEMBER_INSTITUTIONAL_THUMB", 250);
	define("HEIGHT_SIZE_MEMBER_INSTITUTIONAL_THUMB", 143);
	
	define("WIDTH_SIZE_MEMBER_INSTITUTIONAL", 250);
	define("HEIGHT_SIZE_MEMBER_INSTITUTIONAL", 143);
	
	/******** INICIO: IMAGENES MEMBER ********/
	
	/******** INICIO: IMAGENES PROFILE ********/
	define("WIDTH_SIZE_PROFILE","170");
	define("HEIGHT_SIZE_PROFILE","70");
	/******** FINAL: IMAGENES PROFILE ********/
?>