<?php
/****************** INICIO: COUNCIL ******************/
// These are now defined in settings.php (ie. ed_tb_settings)
$absolutePath=dirname(__FILE__);
require $absolutePath."/../../includes/settings.php";
/****************** FIN: COUNCIL ******************/
/****************** INICIO: 404 page ******************/
define("STATIC_404_MESSAGE", "Sorry, the page that you are trying to access does not exist or is not available at this moment. You can go back to the <a href='javascript:history.back()'>previous page</a> or return to the <a href='/'>home page</a>.");
define("STATIC_404_TITLE", "Page not found");
/****************** FIN: 404 page ******************/
/****************** INICIO: METM links ******************/
define("STATIC_OFF_METMS", "https://www.metmeetings.org/en/off-metm-activities:1714");
define("STATIC_METM_PROGRAMME", "https://www.metmeetings.org/en/programme:1703");
define("STATIC_METM_DINNER_MENU", "https://www.metmeetings.org/documentacion/files/metm_files/metm25/METM25_Closing_dinner_menu.pdf");
define("STATIC_METM_FEES", "https://www.metmeetings.org/en/fees:1697");
define("STATIC_METM_WS_WAITING_LIST", "https://forms.gle/4HbWS8WpBVunfB2k9");
// Replace all instances of double quotes with single quotes in STATIC_METM_LATEST
define("STATIC_METM_LATEST", "<script language='javascript' src='//metmeetings.us15.list-manage.com/generate-js/?u=ce57b490ae2eef0ea986a4006&show=10&fid=17502' type='text/javascript'></script>");
/****************** FIN: METM links ******************/
/****************** INICIO: GLOBAL ******************/
define("STATIC_GLOBAL_COMBO_DEFAULT", "Please select...");
define("STATIC_GLOBAL_SENDING", "Sending");
define("STATIC_GLOBAL_SUBMIT", "Submit");
define("STATIC_GLOBAL_EXPORT_EXCEL", "Export to excel");
define("STATIC_GLOBAL_REGISTER", "Register and pay");
define("STATIC_GLOBAL_REGISTER_CHECK_CODE", "Check");
define("STATIC_GLOBAL_BUTTON_YES", "Yes");
define("STATIC_GLOBAL_BUTTON_NO", "No");
define("STATIC_GLOBAL_ALL_FIELDS_REQUIRED", "All fields are required");
define("STATIC_GLOBAL_IMAGE_ALLOW_EXTENSION", "Permitted image formats: .jpg, .gif, .png");
define("STATIC_GLOBAL_IMAGE_ALLOW_SIZE", "Image size can not exceed 500 kb");
define("STATIC_GLOBAL_IMAGE_UPLOAD", "The image was uploaded successfully");
define("STATIC_GLOBAL_PAYPAL_FEE", "Processing fee");
/****************** FIN: GLOBAL ******************/

define("STATIC_TITLE_WEB_HOME", "Mediterranean Editors & Translators");
define("STATIC_ADVANCED_SEARCH", "search");
define("STATIC_LEGAL_TEXT_FOOTER", "Mediterranean Editors and Translators (MET) is a forum for translators and editors who work mainly into or with English. Through MET we exchange views and experiences on promising practices and keep up with relevant research. Read our mission statement. MET is a member of the Spanish Network of the Anna Lindh Euro-Mediterranean Foundation for Dialogue Between Cultures.");
define("STATIC_DISCLAIMER", "Disclaimer");
define("STATIC_MEMBERS", "Members and METM attendees");
define("STATIC_DISCONNECT", "Sign out");
define("STATIC_EXPIRED_ACCOUNT", "<br>Membership expired");
define("STATIC_EDIT_PROFILE", "Edit profile");
define("STATIC_PROFILE_RENEW_MEMBERSHIP", "Renew membership");
define("STATIC_PRIVACY_POLICY", "privacy policy");
define("STATIC_MEDITERRANEAN_EDITORS_AND_TRANSLATORS", "Mediterranean Editors and Translators");
define("STATIC_GUEST", "guest");
define("STATIC_GUESTS", "guests");
define("STATIC_NO_GUESTS", "No guests");
define("STATIC_SEARCH_WIDGET", "Search website");

/* ## HOME ## */
define("STATIC_ANNUAL_CONFERENCE", "Annual conference");
define("STATIC_WORKSHOP", "Workshop");
define("STATIC_ABOUT_US", "About us");
define("STATIC_NEWS", "News");
define("STATIC_NEWS_EVENTS", "News & Events");
define("STATIC_SEE_ALL", "See all news");
define("STATIC_USERNAME", "username");
define("STATIC_MAIL", "email");
define("STATIC_PASSWORD", "password");
define("STATIC_RE_PASSWORD", "Confirm new password");
define("STATIC_MEMBER_UNTIL", "Member until");
define("STATIC_PAYMENT_PENDING", "Payment pending");
define("STATIC_SIGN_IN", "Sign in");
define("STATIC_I_FORGOT_MY_USER", "I forgot my");
define("STATIC_OR", "or");
define("STATIC_FOLLOW_US", "Follow us");
DEFINE("STATIC_HIVE_HOME_PAGE_TEXT", "<a href='https://www.metmeetings.org/en/the-hive:1026' target='_blank'>The Hive</a> is MET’s hub for useful tools and resources, where members can find everything from writing tips to tool recommendations, all in one easy-to-access location.");

/* ## HIVE LOGIN ## */
define("STATIC_HIVE_LOGIN_LINK_TEXT", "Take me to the Hive!");

/* ## NEWS ## */
define("STATIC_NO_NEWS", "No news found");
define("STATIC_ALL_TOPICS", "ALL CATEGORIES");
define("STATIC_TOPIC_NEWS", "News category");
define("STATIC_LIST_NEWS", "News archive");
define("STATIC_NEWS_COMBO_TOPIC_TITLE", "Select category");

/* ## EVENTS ## */
define("STATIC_NO_EVENTS", "No events found");
define("STATIC_LIST_EVENTS", "Events archive");

/* ## CONTACT ## */
define("STATIC_CONTACT_WITH", "Contact");
define("STATIC_CONTACT_COUNCIL_MEMBER", "Contact a MET Council member");
define("STATIC_CONTACT_FORM", "Contact form");
define("STATIC_CONTACT_FIRST_NAME", "First name");
define("STATIC_CONTACT_LAST_NAME", "Last name");
define("STATIC_CONTACT_EMAIL", "Email");
define("STATIC_CONTACT_PHONE", "Phone");
define("STATIC_CONTACT_COMMENTS", "Comments");
define("STATIC_CONTACT_ERROR_FIRST_NAME", "Please enter your first name");
define("STATIC_CONTACT_ERROR_EMAIL", "Please enter a valid email address");
define("STATIC_CONTACT_SENDED", "Email sent successfully.");
define("STATIC_CONTACT_NO_SENDED", "Sorry, due to technical problems the email could not be sent. Please try again later.");
define("STATIC_CONTACT_MAIL_SUBJECT", "MET - contact form submitted");
define("STATIC_CONTACT_MET_ADDRESS", "Mediterranean Editors and Translators<br>Carrer Major 17<br>43422 Barberà de la Conca<br>Tarragona<br>Spain<br><a href='mailto:contact@metmeetings.org'>contact@metmeetings.org</a>");
define("STATIC_CONTACT_PRIVACY", "MET processes your data in accordance with the GDPR. Please see our <a href='https://www.metmeetings.org/en/met-privacy-policy:30' target='_blank'>Privacy notice</a> for more details.");


define("STATIC_SEND", "Send");

/* ## INSTITUTIONAL MEMBERS ## */
define("STATIC_FORM_INSTITUTIONAL_MEMBER_SEARCH_TITLE_CONTENT", "Institutional member title or content");
define("STATIC_SEARCH_INTITUTIONAL_MEMBER", "Search for an institutional member");
define("STATIC_LIST_INTITUTIONAL_MEMBERS", "Institutional members");
define("STATIC_NO_INSTITUTIONAL_MEMBERS", "No institutional member found");
define("STATIC_MEMBER_CONTACT_DETAILS", "Contact details");
define("STATIC_MEMBER_PREFESSIONAL_DETAIL", "Professional profile");
define("STATIC_MEMBER_ACTIVITIES", "Activities");
define("STATIC_MEMBER_LANGUAGE_PAIRS", "Language pairs");
define("STATIC_MEMBER_AREAS_OF_EXPERTISE", "Areas of expertise");
define("STATIC_MEMBER_DESCRIPTION", "Qualifications and experience");
define("STATIC_MEMBER_PUBLICATIONS", "Publications");
define("STATIC_MEMBER_CONTINUING_PROFESSIONAL_DEVELOPMENT", "Continuing professional development");
define("STATIC_MEMBER_WORKSHOPS", "Workshops");

/* ## JOB OPPORTUNITIES ## */
define("STATIC_JOBS_LIST_OPPORTUNITIES", "Job opportunities");
define("STATIC_JOBS_TEXT1", "In this space, MET announces in-house or stable freelance offers that are posted through the <a href='https://www.metmeetings.org/en/submit-a-job:962'>Submit-a-Job form</a>. Job offers announced here have been briefly reviewed by the membership chair but not been formally vetted by MET.");
define("STATIC_JOBS_TEXT2", "To approach MET members about specific ad hoc jobs, when forming a temporary team for example, we suggest using the database of members to find candidates. We also refer individual members to the institutional members page, where some of <a href='https://www.metmeetings.org/en/institutional-members:18'>MET’s institutional members</a> have stated their interest in receiving CVs.");

/* ## FORMS ## */
define("STATIC_FORM_LOGIN_ERROR_USERNAME", "Please enter your username");
define("STATIC_FORM_LOGIN_ERROR_PASSWORD", "Please enter your password");
define("STATIC_FORM_LOGIN_ERROR_MESSAGE", "Invalid username or password");
define("STATIC_FORM_LOGIN_ERROR_USERNAME_AND_PASSWORD_INCORRECT", "Invalid username or password");


define("STATIC_FORM_OFFER_I_INTERESED", "I’m interested");
define("STATIC_FORM_OFFER_YOUR_CV", "Your CV");
define("STATIC_FORM_OFFER_SEND_YOUR_CV", "Send your CV");

/* ## DIRECTORY ## */
define("STATIC_DIRECTORY_MEMBER_DATABASE", "Search the MET member database");
define("STATIC_DIRECTORY_MEMBER_DATABASE_TEXT1", "Use the search form below to find a MET member who offers the English-language communication services you need.");
define("STATIC_DIRECTORY_MEMBER_DATABASE_TEXT2", "Please note that only members who have chosen to make their profile public are listed here. To access the full members’ directory you must be a member and signed in.");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_LAST_NAME_CONTAINS", "Last name contains");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_TEXT_SEARCH", "Free text search");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_LAST_NAME_SEARCH", "Last name contains");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_PROFESSIONAL_ACTIVITY", "Any professional activity");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_STATUS_PAYMENT", "Payment status");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_PAYED", "Paid");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_PENDING_PAYMENT", "Pending");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_NATIONALITY", "Nationality");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_AGE", "Age");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_SEX", "Sex");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_COUNTRY", "Any country");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_CITY", "Any city");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_NO_PAYED", "Not paid");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_PREFERENCE", "View preference");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_PREFERENCE_PUBLIC", "Public");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_PREFERENCE_MEMBERS_ONLY", "Members only");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_WORK_SITUATION", "Work situation");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_TOTAL_MEMBERS_FOUND", "Total members found");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_SOURCE_LANGUAGE", "Any source language");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_TARGET_LANGUAGE", "Any target language");
define("STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_AREAS_OF_EXPERTISE", "Any subject area");


/**** EXCEL ****/
define("STATIC_DIRECTORY_MEMBER_EXCEL_NAME", "Name");
define("STATIC_DIRECTORY_MEMBER_EXCEL_EMAIL", "Email");
define("STATIC_DIRECTORY_MEMBER_EXCEL_COUNTRY", "Country");
define("STATIC_DIRECTORY_MEMBER_EXCEL_PROFESSION", "Profession");
define("STATIC_DIRECTORY_MEMBER_EXCEL_WORK_SITUATION", "Work situation");
define("STATIC_DIRECTORY_MEMBER_EXCEL_AGE", "Age");
define("STATIC_DIRECTORY_MEMBER_EXCEL_SEX", "Sex");
/**** EXCEL ****/


define("STATIC_NO_MEMBERS_DIRECTORY", "No members found");
define("STATIC_DIRECTORY_MEMBER_IMAGE", "Image");
define("STATIC_DIRECTORY_MEMBER_WHOLE_WORD", "Whole word only");
define("STATIC_DIRECTORY_DETAIL_METMS", "MET Meetings");
define("STATIC_DIRECTORY_DETAIL_WORKSHOPS", "MET workshops");
define("STATIC_DIRECTORY_DETAIL_OTHER_CPD", "Other CPD");


/**** STRIPE ****/
define("STATIC_FORM_STRIPE_BUTTON_INTRO_TEXT", "<h3>Pay by debit or credit card</h3>

        <p>Click on the button below to pay by credit or debit card. MET does not record or store your card details, which are sent directly from your device to the Stripe system.</p>

        <p>Lorem fistrum sed commodo reprehenderit. Laboris aute laboris dolore aliqua. Tempor de la pradera voluptate nisi amatomaa amatomaa te va a hasé pupitaa. Te va a hasé pupitaa va usté muy cargadoo nisi está la cosa muy malar et ese pedazo de quietooor aute.</p>");
define("STATIC_FORM_STRIPE_ITEM_MEMBERSHIP", "MET membership");
define("STATIC_FORM_STRIPE_ITEM_MEMBERSHIP_RENEWAL", "MET membership renewal");
define("STATIC_FORM_STRIPE_ITEM_CONFERENCE_REGISTRATION", "METM registration");
define("STATIC_FORM_STRIPE_ITEM_WORKSHOP_REGISTRATION", "Workshop registration");

/**** MEMBERSHIP FORM ****/
define("STATIC_FORM_MEMBERSHIP_LEGEND_ACCOUNT_DETAILS", "Choose your sign-in details");
define("STATIC_FORM_MEMBERSHIP_LEGEND_PERSONAL_DETAILS", "Personal details");
define("STATIC_FORM_MEMBERSHIP_LEGEND_ADDRESS", "Address");
define("STATIC_FORM_MEMBERSHIP_LEGEND_CONTACT_WARNING", "Fields here are added to your MET directory entry, which is visible to members only by default. You can make your profile visible to non-members and search engines after you have registered.");
define("STATIC_FORM_MEMBERSHIP_LEGEND_CONTACT_WARNING_2", "Fields here are shown in your MET directory entry, which is visible to members-only by default and non-members too if you select the box above.");
define("STATIC_FORM_MEMBERSHIP_LEGEND_CONTACT_DETAILS", "Contact details visible on your profile");
define("STATIC_FORM_PICTURE", "Attendee list");
define("STATIC_FORM_MEMBERSHIP_LEGEND_BILLING_INFORMATION", "Billing information");
define("STATIC_FORM_MEMBERSHIP_LEGEND_PROFESSIONAL_INFORMATION", "Professional information");
define("STATIC_FORM_MEMBERSHIP_LEGEND_ADDITIONAL_INFORMATION", "Other personal information (optional)");
define("STATIC_FORM_MEMBERSHIP_LEGEND_OTHER_CPD", "Other CPD");
define("STATIC_FORM_MEMBERSHIP_LEGEND_PREFERRED_METHOD_OF_PAYMENT", "Annual fee and payment method");
define("STATIC_FORM_MEMBERSHIP_LEGEND_PRIVACY", "Privacy");
define("STATIC_FORM_MEMBERSHIP_LEGEND_PHOTO", "Photo");
define("STATIC_FORM_MEMBERSHIP_EMAIL_USER", "Email");
define("STATIC_FORM_MEMBERSHIP_PASSWORD_USER", "Password");
define("STATIC_FORM_MEMBERSHIP_CONFIRM_PASSWORD_USER", "Confirm password");
define("STATIC_FORM_MEMBERSHIP_TITLE", "Title");
define("STATIC_FORM_MEMBERSHIP_FIRST_NAME", "First name(s)");
define("STATIC_FORM_MEMBERSHIP_LAST_NAMES", "Last name(s)");
define("STATIC_FORM_MEMBERSHIP_NATIONALITY", "Nationality(ies)");
define("STATIC_FORM_MEMBERSHIP_COUNTRY_OF_RESIDENCE", "Country of residence");
define("STATIC_FORM_MEMBERSHIP_STREET_1", "Street-1");
define("STATIC_FORM_MEMBERSHIP_STREET_2", "Street-2");
define("STATIC_FORM_MEMBERSHIP_TOWN_CITY", "Town/City");
define("STATIC_FORM_MEMBERSHIP_PROVINCE", "Province");
define("STATIC_FORM_MEMBERSHIP_POSTCODE", "Postcode");
define("STATIC_FORM_MEMBERSHIP_EMAIL", "Email");
define("STATIC_FORM_MEMBERSHIP_HOME_PHONE", "Home phone");
define("STATIC_FORM_MEMBERSHIP_ALTERNATIVE_EMAIL", "Alternative email");
define("STATIC_FORM_MEMBERSHIP_WORK_PHONE", "Work phone");
define("STATIC_FORM_MEMBERSHIP_FAX", "Fax");
define("STATIC_FORM_MEMBERSHIP_MOBILE_PHONE", "Mobile phone");
define("STATIC_FORM_MEMBERSHIP_YOUR_PROFESSION", "Your activities");
define("STATIC_FORM_MEMBERSHIP_EDITOR", "Editor");
define("STATIC_FORM_MEMBERSHIP_TRANSLATOR", "Translator");
define("STATIC_FORM_MEMBERSHIP_EDUCATOR", "Educator");
define("STATIC_FORM_MEMBERSHIP_INTERPRETER", "Interpreter");
define("STATIC_FORM_MEMBERSHIP_RESEARCHER", "Researcher");
define("STATIC_FORM_MEMBERSHIP_WRITER", "Writer");
define("STATIC_FORM_MEMBERSHIP_STUDENT", "Student or 65+");
define("STATIC_FORM_MEMBERSHIP_OTHER", "Other");
define("STATIC_FORM_MEMBERSHIP_PLEASE_CHECK_LEAST_ONE", "Please check at least one and as many as appropriate.");
define("STATIC_FORM_MEMBERSHIP_IF_OTHER_SPECIFY", "If &quot;Other&quot;, please specify");
define("STATIC_FORM_MEMBERSHIP_IF_STUDENT_SUBJECT", "If a student, your subject");
define("STATIC_FORM_MEMBERSHIP_DEGREES_QUALIFICATIONS", "Degrees/qualifications");
define("STATIC_FORM_MEMBERSHIP_WORK_SITUATION", "Your status");
define("STATIC_FORM_MEMBERSHIP_FREELANCE", "Freelance");
define("STATIC_FORM_MEMBERSHIP_INHOUSE", "In-house");
define("STATIC_FORM_MEMBERSHIP_AGE", "Age");
define("STATIC_FORM_MEMBERSHIP_SEX", "Sex");
define("STATIC_FORM_MEMBERSHIP_MALE", "Male");
define("STATIC_FORM_MEMBERSHIP_FEMALE", "Female");
define("STATIC_FORM_MEMBERSHIP_HOW_DID_YOU_HEAR_ABOUT_MET", "How did you hear about MET?");
define("STATIC_FORM_MEMBERSHIP_MEMBERSHIP_COSTS_30", "");
define("STATIC_FORM_MEMBERSHIP_MEMBERSHIP_COSTS_30_RENEW", "Membership costs €38.");
define("STATIC_FORM_MEMBERSHIP_MEMBERSHIP_COSTS_15_RENEW", "Membership costs €19.");
define("STATIC_FORM_MEMBERSHIP_NEED_INVOICE", "I need an invoice");
define("STATIC_FORM_MEMBERSHIP_PAYMENT_METHOD", "<span style='color:maroon;font-weight: bold;'>*</span> <b>Select your payment method</b>");
define("STATIC_FORM_MEMBERSHIP_BANK_TRANSFER", "Bank transfer");
define("STATIC_FORM_MEMBERSHIP_PAYPAL", "Credit/debit card");
define("STATIC_FORM_MEMBERSHIP_DIRECT_DEBIT_SPAIN", "Direct debit (Spain)");
// define("STATIC_FORM_MEMBERSHIP_PAYMENT_METHOD_TXT_1", "To pay by direct debit (Spain only), please print out and sign this <a href='documentacion/files/MET_direct_debit_form.pdf' target='_blank'>direct debit form</a> and return it to <a href='mailto:direct_debit@metmeetings.org'>MET</a> by email (scanned) or by regular mail (see form for postal address).");
define("STATIC_FORM_MEMBERSHIP_PAYMENT_METHOD_TXT_1", "Payment is by credit or debit card through Stripe's secure payment platform.");
define("STATIC_FORM_MEMBERSHIP_PAYMENT_METHOD_TXT_2", "If you pay by bank transfer, membership will become effective once payment has been received. Click here for MET’s <a href='https://www.metmeetings.org/en/met-bank-details:498' target='_blank'>account details</a>.");
define("STATIC_FORM_MEMBERSHIP_PRIVACY_NOTE", "<span style='color:maroon;font-weight: bold;'>*</span> I consent to the processing of my data under the terms described in <a href='https:/www.metmeetings.org/en/met-privacy-policy:30' target='_blank'>MET’s privacy notice</a>, which I have read and understood.");
define("STATIC_FORM_MEMBERSHIP_PRIVACY_NOTE_1", "<span style='color:maroon;font-weight: bold;'>*</span> I consent to the processing of my data under the terms described in <a href='https:/www.metmeetings.org/en/met-privacy-policy:30' target='_blank'>MET’s privacy notice</a>, which I have read and understood.");
define("STATIC_FORM_MEMBERSHIP_NEWSLETTER_PERMISSION", "I agree for my name and country of residence to be listed in the welcome section of an upcoming member newsletter.");
define("STATIC_FORM_MEMBERSHIP_REGISTER", "Register and pay");
define("STATIC_FORM_MEMBERSHIP_REGISTER_2", "Renew and pay");
define("STATIC_FORM_NEW_SEARCH_LEGEND_SERCH_NEWS", "Search for a news item");
define("STATIC_FORM_NEW_SEARCH_TITLE_CONTENT_NEWS", "News item title or content");
define("STATIC_FORM_NEW_SEARCH_DATE", "Date");
define("STATIC_FORM_NEW_SEARCH", "Search");
define("STATIC_FORM_MEMBERSHIP_ERROR_EMAIL_USER", "Please enter your email");
define("STATIC_FORM_MEMBERSHIP_ERROR_PASSWORD", "Please enter your password");
define("STATIC_FORM_MEMBERSHIP_ERROR_CONFIRM_PASSWORD", "The passwords do not match");
define("STATIC_FORM_MEMBERSHIP_ERROR_FIRST_NAME", "Please enter your first name");
define("STATIC_FORM_MEMBERSHIP_ERROR_LAST_NAME", "Please enter your last name(s)");
define("STATIC_FORM_MEMBERSHIP_ERROR_COUNTRY_RESIDENCE", "Please enter your country of residence");
define("STATIC_FORM_MEMBERSHIP_ERROR_ADDRESS", "Please enter your street address");
define("STATIC_FORM_MEMBERSHIP_ERROR_TOWN_CITY", "Please enter your town/city");
define("STATIC_FORM_MEMBERSHIP_ERROR_EMAIL", "Please enter a valid email address");
define("STATIC_FORM_MEMBERSHIP_ERROR_PAYMENT_METHOD", "Please select method of payment");
define("STATIC_FORM_MEMBERSHIP_EMAIL_REPEAT", "A member already exists with this email address");
define("STATIC_FORM_MEMBERSHIP_ERROR_PROFESSION", "Please select a profession");
//Errors billing information
define("STATIC_FORM_MEMBERSHIP_BILLING_ERROR_NIF", "Please enter your VAT or tax ID number");
define("STATIC_FORM_MEMBERSHIP_BILLING_ERROR_NAME", "Please fill out your name");
define("STATIC_FORM_MEMBERSHIP_BILLING_ERROR_ADDRESS", "Please fill out your Address");
define("STATIC_FORM_MEMBERSHIP_BILLING_ERROR_ZIPCODE", "Please fill out your postal code");
define("STATIC_FORM_MEMBERSHIP_BILLING_ERROR_CITY", "Please fill out your City");
define("STATIC_FORM_MEMBERSHIP_BILLING_ERROR_COUNTRY", "Please fill out your Country");
define("STATIC_FORM_MEMBERSHIP_BILLING_EXPLANATORY_TEXT", "If you don’t have a VAT or tax ID number, write N/A.");
define("STATIC_FORM_MEMBERSHIP_ERROR_PRIVACY", "You must accept MET’s privacy notice");

define("STATIC_FORM_MEMBERSHIP_SPECIAL_FEE", "Select your membership category.");


define("STATIC_FORM_RENEW_MEMBERSHIP_ERROR_PROFESSION", "Please select one or more professions");
define("STATIC_FORM_RENEW_MEMBERSHIP_ERROR_SITUATION_WORK", "Please select a work situation");
define("STATIC_FORM_RENEW_MEMBERSHIP_ERROR_CAPTCHA", "Please enter the characters from the image (NB case-insensitive). If you can’t read the image, click the circular arrows until you get one you can.");


define("STATIC_FORM_EVENT_SEARCH_LEGEND_SERCH_EVENTS", "Search for an event");
define("STATIC_FORM_EVENT_SEARCH_TITLE_CONTENT_EVENTS", "Event title or content");

define("STATIC_FORM_MEMBERSHIP_INSTIT_LEGEND_DETAILS_INSTITUTION", "Details of the institution");
define("STATIC_FORM_MEMBERSHIP_INSTIT_LEGEND_INSTITUTIONAL_REPRESENTATIVE", "Institutional representative");
define("STATIC_FORM_MEMBERSHIP_INSTIT_LEGEND_PREF_METHOD_PAYMENT", "Preferred method of payment");
define("STATIC_FORM_MEMBERSHIP_INSTIT_NAME_INSTITUTION", "Name of institution");
define("STATIC_FORM_MEMBERSHIP_INSTIT_DEPARTMENT_IF_APPLICABLE", "Department (if applicable)");
define("STATIC_FORM_MEMBERSHIP_INSTIT_COUNTRY", "Country (optional)");
define("STATIC_FORM_MEMBERSHIP_INSTIT_PHONE_NO", "Phone no.");
define("STATIC_FORM_MEMBERSHIP_INSTIT_FAX_NO", "Fax no.");
define("STATIC_FORM_MEMBERSHIP_INSTIT_EMAIL_ADDRESS", "Email address");
define("STATIC_FORM_MEMBERSHIP_INSTIT_INSTITUTIONAL_REPRESENTATIVE_TEXT", "Each institution is asked to designate a representative to serve as the principal channel of communication between the institution and MET for the year.");
define("STATIC_FORM_MEMBERSHIP_INSTIT_IF_OTHER_PLEASE_STATE", "If other, please state");
define("STATIC_FORM_MEMBERSHIP_INSTIT_EMAIL_TO_USER", "Email");
define("STATIC_FORM_MEMBERSHIP_INSTIT_ALTERNATIVE_EMAIL", "Alternative email");
define("STATIC_FORM_MEMBERSHIP_INSTIT_COST_100", "Institutional membership costs €100 per year.");
define("STATIC_FORM_MEMBERSHIP_INSTIT_PREFERRED_PAYMENT_METHOD", "Preferred method of payment");
define("STATIC_FORM_MEMBERSHIP_INSTIT_DIRECT_DEBIT", "Direct debit");
define("STATIC_FORM_MEMBERSHIP_INSTIT_DATE_OF_SPAIN", "Date of payment");
define("STATIC_FORM_MEMBERSHIP_INSTIT_ERROR_NAME_INSTITUTION", "Please enter the name of the institution");
define("STATIC_FORM_MEMBERSHIP_INSTIT_ERROR_DATE_PAYMENT", "Please enter a valid date of payment");

define("STATIC_FORM_RENEW_MEMBERSHIP_LEGEND_RENEW_MEMBERSHIP", "Renewal fee and payment method");
define("STATIC_FORM_RENEW_MEMBERSHIP_LEGEND_PROFESSIONAL_INFORMATION", "Professional information");
define("STATIC_FORM_RENEW_MEMBERSHIP_RENEW_MEMBERSHIP_TEXT", "I wish to renew my MET membership.");
define("STATIC_FORM_RENEW_MEMBERSHIP_AMOUNT_PAID", "Amount paid");
define("STATIC_FORM_RENEW_MEMBERSHIP_PLEASE_WRITE_ANY_COMMENTS_HERE", "Please write any comments here.");
define("STATIC_FORM_RENEW_MEMBERSHIP_RENEW", "Renew");
define("STATIC_FORM_RENEW_MEMBERSHIP_ERROR_AMOUNT_PAID", "Incorrect payment amount");

define("STATIC_FORM_WORKSHOP_REGISTER_LEGEND_SISTER_ASSOCIATION_MEMBERSHIP", "Sister or supporting association/institute");
define("STATIC_FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS", "Workshop/Event");
define("STATIC_FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_NAME", "Title");
define("STATIC_FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_MET_MEMBER_REQUIRED", "Members only");
define("STATIC_FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE_SHORT", "Member");
define("STATIC_FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE_SISTER_SHORT", "Sister assoc.");
define("STATIC_FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE_NON_MEMBER_SHORT", "Non-member");
define("STATIC_FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE", "Member price");
define("STATIC_FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE_SISTER", "Sister assoc. price");
define("STATIC_FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_PRICE_NON_MEMBER", "Non-member price");
define("STATIC_FORM_WORKSHOP_REGISTER_LEGEND_WORKSHOPS_DATE", "Date");
define("STATIC_FORM_WORKSHOP_REGISTER_LEGEND_METHOD_OF_PAYMENT", "Method of payment");
define("STATIC_FORM_WORKSHOP_REGISTER_TOTAL_AMOUNT", "Total amount payable");
define("STATIC_FORM_WORKSHOP_REGISTER_NOT_MEMBER_1", "To attend");
define("STATIC_FORM_WORKSHOP_REGISTER_NOT_MEMBER_2", "you must be a member of MET or of one of MET’s sister associations");
define("STATIC_FORM_WORKSHOP_REGISTER_FULL_MEMBER", "is full. To have your name put on a waiting list, please complete <a href='https://docs.google.com/forms/d/e/1FAIpQLSf2muK1n3u-kKCT5AYlI74L48DEpw8kK2CH7rTS03LoJISo4w/viewform' target='_blank'>this form</a>");
define("STATIC_FORM_WORKSHOP_REGISTER_NO_SELECTED", "Please select at least one workshop");
define("STATIC_FORM_WORKSHOP_REGISTER_LEGEND_CONTACT_DETAILS", "Contact details");

define("STATIC_FORM_WORKSHOP_REGISTER_LEGEND_COMMENTS", "Comments");
define("STATIC_FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO", "Mobile phone no.");
define("STATIC_FORM_WORKSHOP_REGISTER_CODE_INPUT", "**********");
define("STATIC_FORM_WORKSHOP_REGISTER_SISTER_ASSOCIATION_MEMBERSHIP_TEXT_1", "If you're a member of one of MET's sister associations, select your association from the dropdown list below to receive the discount. Please note: We may ask for proof of membership.");
define("STATIC_FORM_WORKSHOP_REGISTER_SISTER_ASSOCIATION_MEMBERSHIP_TEXT_2", "If you're a member of one of MET's sister associations, select your association from the dropdown list below to receive the discount. Please note: We may ask for proof of membership.");
define("STATIC_FORM_WORKSHOP_REGISTER_SELECT_ASSOCIATION_WHICH_MEMBER", "Please select the association of which you are a member");
define("STATIC_FORM_WORKSHOP_REGISTER_CHECK_WORKSHOP_OPTION_1", "MET medley: talks, tasks and open discussions (open to all, €0)");
define("STATIC_FORM_WORKSHOP_REGISTER_CHECK_WORKSHOP_OPTION_2", "Sea of Words project, working groups (1) (MET members only, €0)");
define("STATIC_FORM_WORKSHOP_REGISTER_CHECK_WORKSHOP_OPTION_3", "An introduction to editing non-native English for application to different types of text (€30)");
define("STATIC_FORM_WORKSHOP_REGISTER_CHECK_WORKSHOP_OPTION_4", "Sea of Words project, theory and practice (2) (MET members only, €0)");
define("STATIC_FORM_WORKSHOP_REGISTER_CHECK_WORKSHOP_OPTION_5", "Grammar pathway: sessions 2 and 3 (€24)");
define("STATIC_FORM_WORKSHOP_REGISTER_CHECK_WORKSHOP_OPTION_6", "Style Matters (MET members, €135/€150; VÉRTICE members, €145/€160; see");
define("STATIC_FORM_WORKSHOP_REGISTER_CHECK_WORKSHOP_OPTION_7", "Editing literary translations: a hands-on experience inspired by MET’s Sea of Words project (€15)");
define("STATIC_FORM_WORKSHOP_REGISTER_FREE_SCHUDLE", "fee schedule");
define("STATIC_FORM_WORKSHOP_REGISTER_FULL", "FULL!");
define("STATIC_FORM_WORKSHOP_REGISTER_METHOD_OF_PAYMENT_TEXT_2", "To pay by bank transfer, see <a href='https:/www.metmeetings.org/en/met-bank-details:498' target='_blank'>MET’s bank details</a>.<br>As the reason for payment, enter your surname and 'MET CPD'");
define("STATIC_FORM_WORKSHOP_REGISTER_AMOUN_PAID_EVENT_0", "Amount paid");
define("STATIC_FORM_WORKSHOP_REGISTER_AMOUN_PAID_EVENT_0_TEXT", "EMAME, APTIC, plus others in the Spanish language professionals’ network, and AITI.");
define("STATIC_FORM_WORKSHOP_REGISTER_DATE_PAID", "Date paid");

define("STATIC_FORM_MEMT_REGISTER_MEMBER_LEGEND_MEMBERSHIP_RENEWAL_GUEST_MEMBERS", "Membership renewal/Guest members");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_LEGEND_CONFERENCE_REGISTRATION", "Conference registration");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_LEGEND_ROOM_RESERVATION", "Room reservation");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_LEGEND_FEES", "Fees");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_LEGEND_METHOD_OF_PAYMENT", "Method of payment");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_RENEW_MEMBERSHIP_FOR", "Please renew my membership for");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_MEMBERSHIP_30", "membership is €30");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_CHECK_MEMBERS_OTHER_ASSOCIATIONS", "Members of MET’s sister associations may attend MET events as guest members. If you are a member of one of these associations, please tick the appropriate box below.");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_IF_YOU_ARE_MEMBER_VERTICE", "If you are a member of a VÉRTICE association, please specify which one");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_SPEAKER_PRESENTER_HELPER_CONFERENCE", "I am a METM25 speaker, a MET helper or a student MET member");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_CONFERENCE_REGISTRATION_TEXT_1", "<strong>Workshops:</strong> One workshop (on Thursday afternoon or Friday morning) is included in the conference fee. Please choose from the workshops listed below. The workshops on each day run concurrently, so if you wish to attend a second workshop, choose one from the other day. The fee for a second workshop is €30 (see");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_TABLE_OF_FEES", "table of fees");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_CONFERENCE_REGISTRATION_TEXT_2", "Please <strong>do not check more than one box</strong> on the same day.");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_THUSDAY_AFTERNOON_WORKSHOPS", "Thursday afternoon workshops");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_PRACTICAL_STATS_1", "Practical stats I");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_ANATOMY_PART_3", "Anatomy part 3: nervous system");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_APPROACHES_EFFECTIVE_PARAGRAPHING", "Approaches to effective paragraphing");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_READY_STEADY_EDIT", "Ready, steady, edit");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_FRIFAY_AFTERNOON_WORKSHOPS", "Friday morning workshops");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_PRACTICAL_STATS_2", "Practical stats II");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_GETTING_STARTED_FINANCIAL_TRANSLATION", "Getting started in financial translation");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_TRANSLATION_REVISION", "Translation revision");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_INTRODUCTION_EDITING_NON_NATIVE_ENGLISH", "Introduction to editing non-native English");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_EDITING_LITERARY_TRANSLATIONS", "Editing literary translations");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_CONFERENCE_BADGE", "<strong>Conference badge:</strong> Please write here the information you would like to have displayed on your conference badge. Please write your name, profession ajnd/or affiliation, and city/country on <strong>three</strong> lines, for example:");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_SLASH_INDICATE_LINE_BREAK", "Please use a slash to indicate a line break (e.g. “Nicola Martin / Centro Linguistico / Università  di Venezia / Venezia, Italy”). Please do not exceed 3 short lines of text.");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_CLOSING_DINNER", "Closing dinner");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_CLOSING_DINNER_TEXT_1", "The conference fee includes the closing dinner on Saturday evening for all conference participants. If you will be bringing one or more guests (€50 per guest, see");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_CLOSING_DINNER_TEXT_2", "), please select the number of persons:");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_CHECK_CLOSING_DINNER", "I will NOT be attending the dinner on Saturday evening (save €40 on the conference fee, see");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_EMAIL_PERMISSION", "Email permission");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_CHECK_EMAIL_PERMISSION", "Check this box if you do NOT want your email address to be included in the list of contact details to be distributed to conference participants. Otherwise we assume you grant permission.");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_MEMBERSHIP", "Membership");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_CONFERENCE", "Conference");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_EXTRA_WORKSHOP", "Extra workshop");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_DINNER_OPT_OUT", "Dinner opt-out");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_DINNER_GUEST", "Dinner guest(s)");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_TOTAL_IN_EUROS", "Total (in euros)");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_PLEASE_SELECT_PAYMENT_METHOD", "Please select a payment method");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_METHOD_PAYMENT_TEXT_1", "To pay by bank transfer you will need MET’s account details.");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_METHOD_PAYMENT_TEXT_2", "To pay by PayPal, sign in to your PayPal account, select the option ‘Send Money’ and give the recipient’s email address as <a href='mailto:metmember@gmail.com'>metmember@gmail.com</a>. In the Message to the recipient section please specify 'METM");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_METHOD_PAYMENT_TEXT_3", "registration – [Your Name]'. N.B. If not selected automatically, remember to select EUR as the payment currency.");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_METHOD_PAYMENT_TEXT_4", "If you do not have a PayPal account, you can still pay by PayPal as a PayPal guest via this page. In the payment form on the left, put 'METM");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_METHOD_PAYMENT_TEXT_5", "registration – [Your Name]' in the Description field and fill in the amount, then click on 'Update' (or 'Actualizar'). Next, on the righthand side, below the PayPal sign-in form, click on the link that says 'Don't have a PayPal account?' (or '¿No dispone de una cuenta PayPal?', etc.). You will then see a form in which you can fill out your card details to pay as a guest. Please use the same email address and message to the recipient as above.");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_METHOD_PAYMENT_TEXT_6", "Once you are sure that all the details on this form are correct, click on ‘Submit form’. Registration will become effective once we have received payment.");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_INVOICE", "Invoice");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_INVOICE_TEXT", "If you would like an invoice, please write your registered address and tax number (or fiscal code) in the box below. N.B. Please use a slash to indicate a line break.");
define("STATIC_FORM_MEMT_REGISTER_MEMBER_COMMENTS_TEXT", "If you have any comments or special needs (e.g. regarding travel visas), please write them in the box below.");

define("STATIC_FORM_PROFILE_VIEW_PROFILE", "View profile");
define("STATIC_FORM_PROFILE_PERSONAL_DETAILS", "Personal details");
define("STATIC_FORM_PROFILE_CONTACT_DETAILS", "Contact details visible on your profile");
define("STATIC_FORM_PROFILE_CONTINUAING_PROFESSIONAL_DEVELOPMENT", "Continuing professional development");
define("STATIC_FORM_PROFILE_CHANGE_PASSWORD", "Change password");
define("STATIC_FORM_PROFILE_CHANGE_NEW_PASSWORD", "New password");
define("STATIC_FORM_PROFILE_CHANGE_OLD_PASSWORD", "Current password");
define("STATIC_FORM_PROFILE_CHANGE_REPEAT_PASSWORD", "Repeat new password");
define("STATIC_FORM_PROFILE_CHANGE_APPLY_CHANGES", "Apply changes");
define("STATIC_FORM_PROFILE_CHANGE_IMAGE_PROFILE", "Profile image");
define("STATIC_FORM_PROFILE_CHANGE_STREET_ADDRESS", "Street address");
define("STATIC_FORM_PROFILE_CHANGE_CITY", "City");
define("STATIC_FORM_PROFILE_CHANGE_STATE_PROVINCE", "State or province");
define("STATIC_FORM_PROFILE_CHANGE_POSTAL_CODE", "Postal code");
define("STATIC_FORM_PROFILE_CHANGE_TELEPHONE_LANDLINE", "Telephone (landline)");
define("STATIC_FORM_PROFILE_CHANGE_TELEPHONE_MOBILE", "Telephone (mobile)");
define("STATIC_FORM_PROFILE_CHANGE_EMAIL_ADDRESS_1", "Email address 1");
define("STATIC_FORM_PROFILE_CHANGE_EMAIL_ADDRESS_2", "Email address 2");
define("STATIC_FORM_PROFILE_CHANGE_WEB", "Website");
define("STATIC_FORM_PROFILE_CHANGE_PASS_NO_EQUAL", "Passwords do not match");
define("STATIC_FORM_PROFILE_MESSAGE_UPDATES_OK", "The changes have been saved successfully");
define("STATIC_FORM_PROFILE_MESSAGE_ERROR_CHANGE_PASS", "Please enter your current password correctly");
define("STATIC_FORM_PROFILE_MESSAGE_CHANGE_PASS", "The password has been modified");
define("STATIC_FORM_PROFILE_VIEW_IMAGE", "To view the image click here");
define("STATIC_FORM_PROFILE_DELETE_IMAGE", "To delete the image click here");
define("STATIC_FORM_PROFILE_RECYCLE_IMAGE", "To restore the image click here");
define("STATIC_FORM_PROFILE_PUBLIC_PROFILE", "Check this box to make your profile visible to non-members (and to search engines).");
define("STATIC_FORM_PROFILE_RECOMENDED_SIZE", "Recommended size");
define("STATIC_FORM_PROFILE_METMS_CONFERENCES", "METMs and conferences");
define("STATIC_FORM_PROFILE_INDIVIDUAL_MEMBER_RECOMMENDED_SIZE", "Recommended size: 200 x 200 pixels");
define("STATIC_FORM_PROFILE_INSTITUTIONAL_MEMBER_RECOMMENDED_SIZE", "Recommended size: 250 x 143 pixels");
define("STATIC_FORM_PROFILE_SOURCE_LANGUAGES_HEADING", "Source (click in box to select)");
define("STATIC_FORM_PROFILE_TARGET_LANGUAGES_HEADING", "Target (click in box to select)");
define("STATIC_FORM_PROFILE_LEGEND_LANGUAGE_PAIRS", "Language combinations");
define("STATIC_FORM_PROFILE_AREAS_OF_EXPERTISE_HEADING", "Click to select");
define("STATIC_FORM_PROFILE_LEGEND_AREAS_OF_EXPERTISE", "Areas of expertise");
define("STATIC_FORM_PROFILE_INSCRIPTIONS_HISTORY_TITLE", "Your membership history");
define("STATIC_FORM_PROFILE_INSCRIPTIONS_HISTORY_NUMBER", "Reg ID");
define("STATIC_FORM_PROFILE_INSCRIPTIONS_HISTORY_AMOUNT", "Amount");
define("STATIC_FORM_PROFILE_INSCRIPTIONS_HISTORY_START_DATE", "Registration date");
define("STATIC_FORM_PROFILE_INSCRIPTIONS_HISTORY_END_DATE", "End date");
define("STATIC_FORM_PROFILE_INSCRIPTIONS_HISTORY_PAYMENT_TYPE", "Payment type");
define("STATIC_FORM_PROFILE_SOURCE_LANGS", "Select your source languages");
define("STATIC_FORM_PROFILE_TARGET_LANGS", "Select your target languages");
define("STATIC_FORM_PROFILE_WORKING_LANGUAGES_TEXT", "Note that if you work exclusively in English, you can choose English as both source and target language");
define("STATIC_FORM_PROFILE_AREAS_OF_EXPERTISE", "Select your areas of expertise");
define("STATIC_FORM_PROFILE_CPD", "Continuing professional development");


/*************** INICIO: PROFILE_BILLING_INFORMATION  ***************/
define("STATIC_FORM_PROFILE_BILLING_CUSTOMER_NIF", "Tax ID (or N/A)");
define("STATIC_FORM_PROFILE_BILLING_NAME_CUSTOMER", "Name");
define("STATIC_FORM_PROFILE_BILLING_NAME_COMPANY", "Company name");
define("STATIC_FORM_PROFILE_BILLING_ADDRESS", "Address");
define("STATIC_FORM_PROFILE_BILLING_ZIPCODE", "Postal code");
define("STATIC_FORM_PROFILE_BILLING_CITY", "City");
define("STATIC_FORM_PROFILE_BILLING_PROVINCE", "Province");
define("STATIC_FORM_PROFILE_BILLING_COUNTRY", "Billing country");
define("STATIC_FORM_PROFILE_BILLING_TAX_ID_TYPE", "Tax ID type");
define("STATIC_FORM_PROFILE_BILLING_TAX_ID_COUNTRY", "Country of residence");
define("STATIC_FORM_PROFILE_BILLING_TAX_ID_NUMBER", "Tax ID number");
/*************** FIN: PROFILE_BILLING_INFORMATION  ***************/


define("STATIC_FORM_NEW_SUBMIT_TITLE", "News item");
define("STATIC_FORM_NEW_SUBMIT_TITLE_BUTTON", "Submit news item");
define("STATIC_FORM_NEW_SUBMIT_OK", "Your news item has been submitted and will be reviewed for publication");
define("STATIC_FORM_NEW_SUBMIT_KO", "Error in submitting news item");

define("STATIC_FORM_EVENT_SUBMIT_TITLE", "Event details");
define("STATIC_FORM_EVENT_SUBMIT_TITLE_BUTTON", "Submit event");
define("STATIC_FORM_EVENT_SUBMIT_THEME", "Select a category");
define("STATIC_FORM_EVENT_SUBMIT_SUMMARY", "Summary (max. 230 characters)");
define("STATIC_FORM_EVENT_SUBMIT_CONTENT", "Content");
define("STATIC_FORM_EVENT_SUBMIT_TITLE_EMPTY", "Please enter a title");
define("STATIC_FORM_EVENT_SUBMIT_SUMMARY_ERROR", "Please enter a summary");
define("STATIC_FORM_EVENT_SUBMIT_DATE_ERROR", "Please enter a date");
define("STATIC_FORM_EVENT_SUBMIT_CONTENT_ERROR", "Please enter the content");
define("STATIC_FORM_EVENT_SUBMIT_OK", "Your event announcement has been submitted and will be reviewed for publication");
define("STATIC_FORM_EVENT_SUBMIT_KO", "Error in submitting event");

define("STATIC_FORM_JOB_SUBMIT_TITLE", "Job details");
define("STATIC_FORM_JOB_SUBMIT_TITLE_BUTTON", "Submit job offer");
define("STATIC_FORM_JOB_SUBMIT_MAIL_ERROR", "Please enter a valid email address");
define("STATIC_FORM_JOB_SUBMIT_OK", "Your job offer has been submitted and will be reviewed for publication");
define("STATIC_FORM_JOB_SUBMIT_KO", "Error in submitting job offer");

/****************** INICIO: REMEMBER_PASSWORD ******************/
define("STATIC_REMEMBER_PASSWORD_EMAIL", "E-mail");
define("STATIC_REMEMBER_PASSWORD_SEND_FORM", "Send");
define("STATIC_REMEMBER_PASSWORD_TITLE", "Forgot your password?");
define("STATIC_REMEMBER_PASSWORD_ERROR_EMAIL", "Invalid email address");
define("STATIC_REMEMBER_PASSWORD_TEXT", "If you are a MET member, reset your password below.<br />If you are not a MET member, find your password in the conference registration confirmation email.");
define("STATIC_REMEMBER_PASSWORD_HELP", "If you need help, contact the <a href='mailto:webmaster@metmeetings.org'>webmaster</a>.");
define("STATIC_REMEMBER_PASSWORD_MAIL_SUBJECT", "How to reset your password");
define("STATIC_REMEMBER_PASSWORD_MAIL_NO_EXISTS", "There is no member with that email address. Non-member conference attendees should write to the <a href='mailto:webmaster@metmeetings.org'>webmaster.");
define("STATIC_REMEMBER_PASSWORD_OK", "Shortly you will receive an email with instructions on how to reset your password");
define("STATIC_REMEMBER_PASSWORD_CAPTCHA_KO","Captcha incorrect. Please try again.");
define("STATIC_REMEMBER_PASSWORD_NO_SENDED", "Sorry, due to technical problems the email was not sent. Please try again later.");


/****************** INICIO: MAIL_REMEMBER_PASSWORD ******************/
define("STATIC_MAIL_REMEMBER_PASSWORD_TEXT_1", "How to reset your password");
define("STATIC_MAIL_REMEMBER_PASSWORD_TEXT_2", "First click on the following link, which will open a new browser window.");
define("STATIC_MAIL_REMEMBER_PASSWORD_TEXT_3", "Then follow the on-screen instructions.");
define("STATIC_MAIL_REMEMBER_PASSWORD_TEXT_4", "reset your password");
/****************** FIN: MAIL_REMEMBER_PASSWORD ******************/


/****************** FIN: REMEMBER_PASSWORD ******************/

/****************** INICIO: RESET_PASSWORD ******************/
define("STATIC_RESET_PASSWORD_TITLE", "Reset password");
define("STATIC_RESET_PASSWORD_ERROR_PASS_EMPTY", "Please enter your password");
define("STATIC_RESET_PASSWORD_ERROR_PASS_EQUAL", "The passwords do not match");

define("STATIC_RESET_PASSWORD_EMPTY", "You have successfully changed your password.");
define("STATIC_RESET_PASSWORD_OK", "Your password has been reset.");
define("STATIC_RESET_PASSWORD_ERROR", "Sorry, due to technical problems your password has not been reset. Please try again later.");

/****************** FIN: RESET_PASSWORD ******************/


/****************** INICIO: PAGINATOR ******************/
define("STATIC_PAGINATOR_PREV", "previous");
define("STATIC_PAGINATOR_NEXT", "next");
/****************** FIN: PAGINATOR ******************/


/****************** INICIO: MAIL_MEMBER ******************/

define("STATIC_MAIL_MEMBER_BODY_POSTER_DETAIL", "Posted by:");
define("STATIC_MAIL_MEMBER_BODY_MEMBER_DETAIL_MAIL_ADDRESS", "E-mail");
define("STATIC_MAIL_MEMBER_BODY_MEMBER_DETAIL_NAME", "Name");
define("STATIC_MAIL_MEMBER_BODY_MEMBER_DETAIL_MORE_1", "See ");
define("STATIC_MAIL_MEMBER_BODY_MEMBER_DETAIL_MORE_2", "’s profile");

/****************** FIN: MAIL_MEMBER ******************/
/****************** INICIO: SUBMIT_NEW ******************/
define("STATIC_SUBMIT_NEW_MAIL_SUBJECT", "A member has posted a news item for review.");
define("STATIC_SUBMIT_NEW_MAIL_BODY_NEW_MORE_1", "To review the news item, click ");
define("STATIC_SUBMIT_NEW_MAIL_BODY_NEW_MORE_2", "here");
/****************** FIN: SUBMIT_NEW ******************/


/****************** INICIO: SUBMIT_EVENT ******************/
define("STATIC_SUBMIT_EVENT_MAIL_SUBJECT", "A website user has posted an event for review.");
define("STATIC_SUBMIT_EVENT_MAIL_BODY_EVENT_MORE_1", "To review the event posting, click ");
define("STATIC_SUBMIT_EVENT_MAIL_BODY_EVENT_MORE_2", "here");
/****************** FIN: SUBMIT_EVENT ******************/

/****************** INICIO: SUBMIT_JOB_INTEREST ******************/
define("STATIC_SUBMIT_JOB_INTEREST_MAIL_SUBJECT", "A person is interested in the job offer");
define("STATIC_SUBMIT_JOB_INTEREST_MAIL_BODY_DETAIL_PERSON", "Personal details");
define("STATIC_SUBMIT_JOB_INTEREST_MAIL_BODY_DETAIL_PERSON_FIRST_NAME", "First name");
define("STATIC_SUBMIT_JOB_INTEREST_MAIL_BODY_DETAIL_PERSON_LAST_NAME", "Last name(s)");
define("STATIC_SUBMIT_JOB_INTEREST_MAIL_BODY_DETAIL_PERSON_EMAIL", "Email");
define("STATIC_SUBMIT_JOB_INTEREST_SEND_OK", "Request submitted");
define("STATIC_SUBMIT_JOB_INTEREST_MAIL_DETAIL_MORE_1", "To see the job opportunity, click ");
define("STATIC_SUBMIT_JOB_INTEREST_MAIL_DETAIL_MORE_2", "here");
/****************** FIN: SUBMIT_JOB_INTEREST ******************/


/****************** INICIO: SUBMIT_JOB ******************/
define("STATIC_SUBMIT_JOB_MAIL_SUBJECT", "A website user has posted a job offer for review.");
define("STATIC_SUBMIT_JOB_MAIL_BODY_JOB_MORE_1", "To review the job offer, click ");
define("STATIC_SUBMIT_JOB_MAIL_BODY_JOB_MORE_2", "here");
/****************** FIN: SUBMIT_EVENT ******************/

/****************** INICIO: SUBMIT_EXPENSE ******************/
define("STATIC_SUBMIT_EXPENSE_MAIL_SUBJECT", "MET expense form");
define("STATIC_SUBMIT_EXPENSE_MAIL_BODY_EXPENSE_MORE_1", "To view the expense form, click ");
define("STATIC_SUBMIT_EXPENSE_MAIL_BODY_EXPENSE_MORE_2", "here");
/****************** FIN: SUBMIT_EXPENSE ******************/


define("STATIC_INSCRIPTION_LAST_STEP_TITLE", "Thank you");

define("STATIC_INSCRIPTION_LAST_STEP_SUCCESS", "
Please make your payment as agreed.
<br><br>
  ");

define("STATIC_INSCRIPTION_LAST_STEP_DEBIT", "
  Your registration was successful. You can now sign in to our website. If you have not already done so, please remember to fill out and return the signed <a href='documentacion/files/MET_direct_debit_form.pdf'>direct debit authorization</a>. 
  ");


define("STATIC_INSCRIPTION_LAST_STEP_PAYPAL", "
  Your payment was successful. You can now sign in to our website. You will receive an email shortly with further information.
   ");

define("STATIC_INSCRIPTION_LAST_STEP_ERROR", "
  Due to an error with Paypal, your payment was not completed. Please try again.
  ");


define("STATIC_INSCRIPTION_WORKSHOP_LAST_STEP_SUCCESS", "
    Your registration was successful. You will receive an email shortly with further information.
    <br><br>
  ");

define("STATIC_INSCRIPTION_WORKSHOP_LAST_STEP_PAYPAL", "
  Your registration was successful. You will receive an email shortly with further information.
               <br><br>
  ");

define("STATIC_INSCRIPTION_CONFERENCE_LAST_STEP_SUCCESS", "Your registration was successful. You will receive an email shortly with further information.<br><br>Curious to know who else will be attending METM25? Click <a href='https://www.metmeetings.org/en/attendees:1204'>here</a> to find out.<br><br><br>");

define("STATIC_INSCRIPTION_CONFERENCE_LAST_STEP_PAYPAL", "Your registration was successful. You will receive an email shortly with further information.<br><br>Curious to know who else will be attending METM25? Click <a href='https://www.metmeetings.org/en/attendees:1204'>here</a> to find out.<br><br><br>");

/****************** INICIO: MAIL_INSCRIPCION_TRANSFERENCIA ******************/
define("STATIC_MAIL_INSCRIPCION_SUBJECT", "MET membership");
define("STATIC_MAIL_INSCRIPCION_DEAR", "Dear");
define("STATIC_MAIL_INSCRIPTION_TRANSFER_PAYMENT_FIRST_STEP", "
  Thank you for registering with Mediterranean Editors and Translators. Please make your payment as agreed.
<br>
You can now sign in to the website but you will not be able to access full membership benefits until we receive your payment. We will notify you when your membership becomes active.
<br><br> 
  Best regards,<br>
" . STATIC_MEMBERSHIP . "<br><a href=' mailto:membership@metmeetings.org'>Membership Chair</a>
<br><br>NB. This is an automatically generated message. Please do not reply to this message.
  ");

define("STATIC_MAIL_INSCRIPTION_DEBIT_PAYMENT_FIRST_STEP", "
    Thank you for registering at the Mediterranean Editors and Translators website. You can now sign in to our website and submit any registration form that requires sign-in, but members’ area access will be limited and your membership status will remain “pending” until payment has been received. You will be notified by email when your membership has been activated.
<br>
    Best regards,<br>
" . STATIC_MEMBERSHIP . "<br><a href=' mailto:membership@metmeetings.org'>Membership Chair</a>
<br><br>NB. This is an automatically generated message from a notification-only address. Please do not reply to this message.
  ");
define("STATIC_MAIL_PRIVACY_NOTICE", "To learn how MET processes your data, please read our <a href='https://www.metmeetings.org//en/privacy-notice:30'>privacy notice</a>.");

define("STATIC_MAIL_INSCRIPTION_PAYPAL_PAYMENT_FIRST_STEP", "
  Welcome to Mediterranean Editors and Translators. To sign in to the <a href='https://www.metmeetings.org'>MET website</a>, use the email address and password you specified when you registered. Once you have signed in, you will see your membership expiry date displayed in the sign-in area.<br><br>
To find out more about MET and get the most out of your membership, please sign in and visit our <a href='https://www.metmeetings.org/en/welcome:1168'>welcome page</a> for new members, where you can catch up with the latest newsletter and explore our website.
<br><br>
Best regards,<br>
" . STATIC_MEMBERSHIP . "<br><a href=' mailto:membership@metmeetings.org'>Membership Chair</a>
<br><br>NB. This is an automatically generated message from a notification-only address. Please do not reply to this message.
  ");


define("STATIC_MAIL_RENEW_INSCRIPTION_TRANSFER_PAYMENT_FIRST_STEP", "
  Thank you for renewing your membership of Mediterranean Editors and Translators. Please make your payment as agreed.<br>
 Your renewal will become effective when we receive your payment.
  <br><br>
  Best regards,<br>
" . STATIC_MEMBERSHIP . "<br><a href=' mailto:membership@metmeetings.org'>Membership Chair</a>
<br><br>NB. This is an automatically generated message. Please do not reply to this message.
  ");


define("STATIC_MAIL_RENEW_INSCRIPTION_DEBIT_PAYMENT_FIRST_STEP", "
  Thank you for renewing your membership of Mediterranean Editors & Translators. Renewal will be effective once payment has been received. You will be notified by email when your membership has been renewed.
<br>
  Best regards,<br>
" . STATIC_MEMBERSHIP . "<br><a href=' mailto:membership@metmeetings.org'>Membership Chair</a>
<br><br>NB. This is an automatically generated message from a notification-only address. Please do not reply to this message.
  ");

define("STATIC_MAIL_RENEW_INSCRIPTION_PAYPAL_PAYMENT_FIRST_STEP", "
  Thank you for renewing your membership of Mediterranean Editors & Translators. <br><br>
  To get the most out of your membership, please <a href='https://www.metmeetings.org'>sign in</a> and visit our <a href='https://www.metmeetings.org/en/welcome:1168'>welcome page</a>, where you can catch up with the latest newsletter, browse our website content and find out about everything we do.
<br><br>

  Best regards,<br>
" . STATIC_MEMBERSHIP . "<br><a href=' mailto:membership@metmeetings.org'>Membership Chair</a>
<br><br>NB. This is an automatically generated message from a notification-only address. Please do not reply to this message.
  ");

/****************** FIN: MAIL_INSCRIPCION_TRANSFERENCIA ******************/


/****************** INICIO: MAIL_INSCRIPCION_WORKSHOP ******************/
define("STATIC_MAIL_INSCRIPTION_WORKSHOP_SUBJECT", "MET event registration");
define("STATIC_MAIL_INSCRIPTION_WORKSHOP_SUBJECT_MEMBER_TYPE", "Member");
define("STATIC_MAIL_INSCRIPTION_WORKSHOP_SUBJECT_SISTER_ASSOCIATION_TYPE", "Sister association");
define("STATIC_MAIL_INSCRIPTION_WORKSHOP_SUBJECT_NON_MEMBER_TYPE", "Non-member");
/****************** FIN: MAIL_INSCRIPCION_WORKSHOP ******************/


/****************** INICIO: MAIL_INSCRIPCION_WORKSHOP_TO_MET ******************/
define("STATIC_MAIL_INSCRIPTION_TO_MET_BODY_1", "The following workshop registration has been submitted:");
define("STATIC_MAIL_INSCRIPTION_TO_MET_NAME", "Name");
define("STATIC_MAIL_INSCRIPTION_TO_MET_EMAIL", "Email");
define("STATIC_MAIL_INSCRIPTION_TO_MET_PHONE", "Contact phone");
define("STATIC_MAIL_INSCRIPTION_NON_MEMBER_PASSWORD", "Conference password");
define("STATIC_MAIL_INSCRIPTION_TO_MET_SISTER_ASSOCIATION", "Sister association");
define("STATIC_MAIL_INSCRIPTION_TO_MET_WORKSHOPS", "Workshops");
define("STATIC_MAIL_INSCRIPTION_TO_MET_COMMENTS", "Comments");
/****************** FIN: MAIL_INSCRIPCION_WORKSHOP_TO_MET ******************/


/****************** INICIO: MAIL_INSCRIPCION_WORKSHOP_TO_USER ******************/
// define("STATIC_MAIL_INSCRIPTION_TO_USER_BODY_1", "Thank you for registering. 
//                <br><br>You have signed up for the following online workshop:");
// define("STATIC_MAIL_INSCRIPTION_TO_USER_BODY_2", "
//    We're hosting the event on Zoom and will send you the link to your workshop a few days beforehand. In the meantime, if you haven't used Zoom on your current computer, please try out this <a href='https://zoom.us/test'>test meeting</a> where you can download the Zoom app and test your audio and video.
//    <br><br>
//    Best regards,<br>
//    " . STATIC_CPD . "<br><a href='mailto:development@metmeetings.org'>CPD Chair</a>
//  ");
define("STATIC_MAIL_INSCRIPTION_SIG", "
    <br><br>
    Best regards,<br>
    " . STATIC_CPD . "<br><a href='mailto:development@metmeetings.org'>CPD Chair</a>
  ");

define("STATIC_MAIL_INSCRIPTION_TO_USER_DEAR", "Dear");
define("STATIC_MAIL_INSCRIPTION_TO_USER_NAME", "Name");
define("STATIC_MAIL_INSCRIPTION_TO_USER_EMAIL", "Email");
define("STATIC_MAIL_INSCRIPTION_TO_USER_PHONE", "Contact phone");
define("STATIC_MAIL_INSCRIPTION_TO_USER_SISTER_ASSOCIATION", "Sister association");
define("STATIC_MAIL_INSCRIPTION_TO_USER_WORKSHOPS", "Workshops");
define("STATIC_MAIL_INSCRIPTION_TO_USER_COMMENTS", "Comments");
/****************** FIN: MAIL_INSCRIPCION_WORKSHOP_TO_USER ******************/

/* captcha */
define("STATIC_CAPTCHA_RELOAD", "Refresh");
define("STATIC_CAPTCHA_ENTER_LETTERS_NUMBERS_IMAGE", "Please enter the characters from the image. If you can’t read the image, click the circular arrows until you get one you can.");


/****************** INICIO: EXPENSE_FORM ******************/
define("STATIC_EXPENSE_FORM_NAME", "Name");
define("STATIC_EXPENSE_FORM_EMAIL", "Email");
define("STATIC_EXPENSE_FORM_TYPE_EXPENSE", "Account");
define("STATIC_EXPENSE_FORM_SUBTYPE_EXPENSE", "Subaccount");
define("STATIC_EXPENSE_FORM_DATE_INCURRED", "Date incurred");
define("STATIC_EXPENSE_FORM_DESCRIPTION", "Description of expense");
define("STATIC_EXPENSE_FORM_AMOUNT", "Amount in euros");
define("STATIC_EXPENSE_FORM_RECEIPT", "Type of receipt to be presented");
define("STATIC_EXPENSE_FORM_DESIRED_PAYMENT", "Desired form of payment");
define("STATIC_EXPENSE_FORM_DETAILS_PAYMENT", "Details for payment (bank details, PayPal ID, address for sending cheque)");
define("STATIC_EXPENSE_FORM_SUBMIT", "Submit form");
define("STATIC_EXPENSE_FORM_FOOTER", "
  Please submit this form <strong>before December 31</strong> of the year in which the expense was incurred.
  <br><br>
  <strong>N.B.</strong> Original receipts must be delivered in person or by post to:<br>
  Helen Casas<br>
  Apdo. de Correos 23<br>
  08197 Valldoreix (Barcelona)<br>
  Spain
  ");
define("STATIC_EXPENSE_FORM_AMOUNT_NUMERIC", "The amount must be numeric");
define("STATIC_EXPENSE_FORM_INTEREST_SEND_OK", "Expense form submitted successfully");
define("STATIC_EXPENSE_FORM_INTEREST_SEND_KO", "Sorry, due to technical problems the form was not submitted. Please try again later.");
/****************** FIN: EXPENSE_FORM ******************/

/****************** INICIO: RENEW_MEMBERSHIP_FORM ******************/
define("STATIC_RENEW_MEMBERSHIP_FORM_CADUCITY_1", "Your membership renewal application submitted on");
define("STATIC_RENEW_MEMBERSHIP_FORM_CADUCITY_2", "is awaiting confirmation. If you have any query, please <a href='mailto:membership@metmeetings.org'>contact the membership secretary.</a>");
/****************** FIN: RENEW_MEMBERSHIP_FORM ******************/


/****************** INICIO: SUMMARY ******************/
define("STATIC_SUMMARY_SECTION_TITLE", "Summary");
define("STATIC_SUMMARY_BLOCK_COUNTRY", "Countries");
define("STATIC_SUMMARY_BLOCK_COUNTRY_NAME", "Country");
define("STATIC_SUMMARY_BLOCK_COUNTRY_MEMBERS", "Members, n (%)");
define("STATIC_SUMMARY_BLOCK_PREFERENCES", "Status and view preference");
define("STATIC_SUMMARY_BLOCK_PREFERENCES_TOTAL_MEMBERS", "Total no. of members");
define("STATIC_SUMMARY_BLOCK_PREFERENCES_PAID_STATUS", "Paid status");
define("STATIC_SUMMARY_BLOCK_PREFERENCES_VIEW", "View preference");
define("STATIC_SUMMARY_BLOCK_PREFERENCES_MEMBER", "Members, n");
define("STATIC_SUMMARY_BLOCK_PREFERENCES_PAID_STATUS_YES", "Yes");
define("STATIC_SUMMARY_BLOCK_PREFERENCES_PAID_STATUS_NO", "No");
define("STATIC_SUMMARY_BLOCK_PREFERENCES_VIEW_PUBLIC", "Public");
define("STATIC_SUMMARY_BLOCK_PREFERENCES_VIEW_MEMBERS_ONLY", "Members only");
define("STATIC_SUMMARY_BLOCK_PROFESSION", "Professions");
define("STATIC_SUMMARY_BLOCK_PROFESSION_NAME", "Profession");
define("STATIC_SUMMARY_BLOCK_PROFESSION_MEMBERS", "Members, n (%)");
define("STATIC_SUMMARY_BLOCK_AGE", "Ages");
define("STATIC_SUMMARY_BLOCK_AGE_NAME", "Age");
define("STATIC_SUMMARY_BLOCK_AGE_MEMBERS", "Members, n (%)");
define("STATIC_SUMMARY_BLOCK_SEX", "Sexes");
define("STATIC_SUMMARY_BLOCK_SEX_NAME", "Sex");
define("STATIC_SUMMARY_BLOCK_SEX_MEMBERS", "Members, n (%)");
define("STATIC_SUMMARY_BLOCK_WORK_SITUATION", "Work situations");
define("STATIC_SUMMARY_BLOCK_WORK_SITUATION_NAME", "Work situation");
define("STATIC_SUMMARY_BLOCK_WORK_SITUATION_MEMBERS", "Members, n (%)");
/****************** FIN: SUMMARY ******************/

/****************** INICIO: MOVEMENT ******************/
define("STATIC_MOVEMENT_NEW_MEMBERSHIP_DESCRIPTION", "MET membership");
define("STATIC_MOVEMENT_NEW_WORKSHOP_DESCRIPTION", "MET workshop registration");
define("STATIC_MOVEMENT_NEW_CONFERENCE_DESCRIPTION", "METM registration");
/****************** FIN: MOVEMENT ******************/

/****************** INICIO: EXCEL_HEARD_ABOUT_MET ******************/
define("STATIC_EXCEL_HEARD_ABOUT_MET_FIRST_NAME", "First name");
define("STATIC_EXCEL_HEARD_ABOUT_MET_LAST_NAME", "Last name");
define("STATIC_EXCEL_HEARD_ABOUT_MET_EMAIL", "Email");
define("STATIC_EXCEL_HEARD_ABOUT_MET_DESCRIPTION", "Description");
define("STATIC_HEARD_ABOUT_MET_TOOLTIP", "Click here to see the responses to How did you hear about MET");
/****************** FIN: EXCEL_HEARD_ABOUT_MET ******************/


/****************** INICIO: DATE FORMAT ******************/
define("STATIC_WEEK_DAY_MONDAY", "Monday");
define("STATIC_WEEK_DAY_TUESDAY", "Tuesday");
define("STATIC_WEEK_DAY_WEDNESDAY", "Wednesday");
define("STATIC_WEEK_DAY_THURSDAY", "Thursday");
define("STATIC_WEEK_DAY_FRIDAY", "Friday");
define("STATIC_WEEK_DAY_SATURDAY", "Saturday");
define("STATIC_WEEK_DAY_SUNDAY", "Sunday");

define("STATIC_MONTH_JANUARY", "January");
define("STATIC_MONTH_FEBRUARY", "February");
define("STATIC_MONTH_MARCH", "March");
define("STATIC_MONTH_APRIL", "April");
define("STATIC_MONTH_MAY", "May");
define("STATIC_MONTH_JUNE", "June");
define("STATIC_MONTH_JULY", "July");
define("STATIC_MONTH_AUGUST", "August");
define("STATIC_MONTH_SEPTEMBER", "September");
define("STATIC_MONTH_OCTOBER", "October");
define("STATIC_MONTH_NOVEMBER", "November");
define("STATIC_MONTH_DECEMBER", "December");

/****************** FIN: DATE FORMAT ******************/

/****************** INICIO: FORM CONFERENCE ******************/
define("STATIC_FORM_CONFERENCE_REGISTER_CONTACT_DETAILS_TEXT", "<p>We will use your mobile number only in exceptional circumstances. Format: +34123456789</p>");
define("STATIC_FORM_CONFERENCE_REGISTER_LEGEND_SISTER_ASSOCIATION_MEMBERSHIP", "Sister association members");
define("STATIC_FORM_CONFERENCE_REGISTER_SISTER_ASSOCIATION_MEMBERSHIP_TEXT_1", "
  If you are a member of one of MET’s <a href='https://www.metmeetings.org/en/sister-associations:940' target='_blank'>sister associations</a>, select your association from the dropdown list. We may ask for proof of membership.
  ");
define("STATIC_FORM_CONFERENCE_REGISTER_LEGEND_SPEAKER_HELPER", "Special attendee category");
define("STATIC_FORM_CONFERENCE_REGISTER_LEGEND_SPEAKER_HELPER_FEES", "All key helpers who qualify for the reduced fee have been informed. See <a href='" . STATIC_METM_FEES . "' target='_blank'>Fees</a>.");
define("STATIC_FORM_CONFERENCE_REGISTER_PROMPT_SPEAKER_HELPER", "Select if applicable...");
define("STATIC_FORM_CONFERENCE_REGISTER_ATTENDEE_TEXT1", "A list of attendees, with a photo and the badge text you choose above, will be displayed on the website from now until the end of the year.");
define("STATIC_FORM_CONFERENCE_REGISTER_ATTENDEE_TEXT2", "The image shown here will be used.<br>
To upload a different photo, first choose the image file, <b>then click on the <i>Upload</i> button</b> when it appears. Images must be no bigger than 500 KB and in .jpg, .png or .gif format.");
define("STATIC_FORM_CONFERENCE_REGISTER_ATTENDEE_TEXT3", "The image shown here will be used.<br>
Images must be no bigger than 500 KB and in .jpg, .png or .gif format.");
define("STATIC_FORM_CONFERENCE_REGISTER_LEGEND_CONFERENCE_REGISTRATION", "Conference registration");
define("STATIC_FORM_CONFERENCE_REGISTER_LEGEND_WORKSHOPS_DATE", "Date");
define("STATIC_FORM_CONFERENCE_REGISTER_LEGEND_METHOD_OF_PAYMENT", "Method of payment");
define("STATIC_FORM_CONFERENCE_REGISTER_TOTAL_AMOUNT", "Total amount payable");
define("STATIC_FORM_CONFERENCE_REGISTER_NOT_MEMBER_1", "To attend");
define("STATIC_FORM_CONFERENCE_REGISTER_NOT_MEMBER_2", "you must be a member of MET or of one of METs sister associations");
define("STATIC_FORM_CONFERENCE_REGISTER_FULL_MEMBER", "is full. To have you name put on a waiting list, please complete <a href='https://docs.google.com/forms/d/e/1FAIpQLSf2muK1n3u-kKCT5AYlI74L48DEpw8kK2CH7rTS03LoJISo4w/viewform' target='_blank'>this form</a>");
// define("STATIC_FORM_CONFERENCE_REGISTER_LEGEND_COMMENTS", "Dietary, accessibility and travel requirements");
define("STATIC_FORM_CONFERENCE_REGISTER_LEGEND_COMMENTS", "Accessibility and travel requirements");
// define("STATIC_FORM_CONFERENCE_REGISTER_COMMENTS_EXPLAIN", "Specify your dietary requirements and enter any accessibility or travel needs (e.g. stair-free access, visas).<br /><strong>Do not include allergies and intolerances – we will ask for these later.</strong>");
define("STATIC_FORM_CONFERENCE_REGISTER_COMMENTS_EXPLAIN", "Specify any accessibility or travel needs (e.g. stair-free access, visas).<br /><strong>Do not include dietary requirements, allergies and intolerances – we will ask for these later.</strong>");
define("STATIC_FORM_CONFERENCE_REGISTER_METHOD_OF_PAYMENT_TEXT", "&nbsp;");
define("STATIC_FORM_CONFERENCE_REGISTER_SPEAKER_HELPER", "I am a METM25 speaker, a MET helper or a student MET member");
define("STATIC_FORM_CONFERENCE_REGISTER_DIETARY_PREFERENCES", "Choose dietary requirements for catering, excluding the closing dinner");
define("STATIC_FORM_CONFERENCE_REGISTER_LEGEND_CODE", "PRISEAL");
define("STATIC_FORM_CONFERENCE_REGISTER_CODE", "If you have received a registration code from PRISEAL (see <a href='https:/www.metmeetings.org/en/how-to-register:769'>How to register</a> for details), enter it here (NB the code is case-sensitive).");
define("STATIC_FORM_CONFERENCE_REGISTER_BADGE_PROMPT", "Write your three-line caption here, starting with your name on the first line");
// define("STATIC_FORM_CONFERENCE_REGISTER_WORKSHOP_PRICE", "Workshop and minisession fees are €");
define("STATIC_FORM_CONFERENCE_REGISTER_WORKSHOP_PRICE", "Workshop fees are €");
define("STATIC_FORM_CONFERENCE_REGISTER_WORKSHOP_PRICE_2", "and €");
// define("STATIC_FORM_CONFERENCE_REGISTER_WORKSHOP_EXPLAIN", ", respectively. To remove a choice, click the button again.<br>
//  If your first choice is full, <a href='" . STATIC_METM_WS_WAITING_LIST . "' target='_blank'>join the waiting list</a>. You may switch from another workshop if a vacancy arises.
//   ");
define("STATIC_FORM_CONFERENCE_REGISTER_WORKSHOP_EXPLAIN", ". To remove a choice, click the button again.<br>
  If your first choice is full, <a href='" . STATIC_METM_WS_WAITING_LIST . "' target='_blank'>join the waiting list</a>. You may switch from another workshop if a vacancy arises.
   ");
define("STATIC_FORM_CONFERENCE_REGISTER_COMMENTS_PROMPT", "Accessibility and travel requirements");
define("STATIC_FORM_CONFERENCE_REGISTER_MINI_SESSION", "Minisessions (one or both)");
//define("STATIC_FORM_CONFERENCE_REGISTER_MINI_SESSION", "Tech Clinic sessions");
define("STATIC_FORM_CONFERENCE_REGISTER_CHECKBOX_NOT_REQUIRED", "I will be unable to attend any of the workshops");

define("STATIC_FORM_CONFERENCE_REGISTER_BADGE_TITLE", "Conference badge");
define("STATIC_FORM_CONFERENCE_REGISTER_BADGE_BODY", "Profession and/or affiliation");
define("STATIC_FORM_CONFERENCE_REGISTER_BADGE_BODY_EXAMPLE_1", "City and country");
define("STATIC_FORM_CONFERENCE_REGISTER_BADGE_BODY_EXAMPLE_2", "
    Andrea Rizzo, PhD<br>
    Open Science Journal<br>
    London, UK<br>
  ");
define("STATIC_FORM_CONFERENCE_REGISTER_BADGE_BODY_EXAMPLE_3", "
    Arnold Lane<br>
    Freelance translator<br>
    Dijon, France<br>
  ");
define("STATIC_FORM_CONFERENCE_REGISTER_BADGE_BODY_ACLARATION", "&nbsp;");
define("STATIC_FORM_CONFERENCE_REGISTER_BADGE_PRONOUNS", "Preferred pronouns (optional)");
define("STATIC_FORM_CONFERENCE_REGISTER_DINNER_TITLE", "Closing dinner");
define("STATIC_FORM_CONFERENCE_REGISTER_DINNER_BODY", "You may also opt out of the closing dinner (reduction of €");


define("STATIC_FORM_CONFERENCE_REGISTER_GUEST_DINNER_BODY", "You may bring guests to the closing dinner (€");
define("STATIC_FORM_CONFERENCE_REGISTER_PER_GUEST", "per guest):");
define("STATIC_FORM_CONFERENCE_REGISTER_WINE_RECEPTION_TITLE", "Friday evening reception");
define("STATIC_FORM_CONFERENCE_REGISTER_WINE_RECEPTION_BODY", "You may bring guests to the reception (€");
define("STATIC_FORM_CONFERENCE_REGISTER_WINE_RECEPTION_GUEST_1", "I will be bringing");
define("STATIC_FORM_CONFERENCE_REGISTER_WINE_RECEPTION_GUEST_2", "guests to the Friday evening reception.");

define("STATIC_FORM_CONFERENCE_REGISTER_DINNER_AGREE", "I will not be attending the closing dinner.");

define("STATIC_FORM_CONFERENCE_REGISTER_EMAIL_PERMISSION_TITLE", "Personal data consent");
define("STATIC_FORM_CONFERENCE_REGISTER_EMAIL_PERMISSION_BODY", "<span style='color:maroon;font-weight: bold;'>*</span> By taking part in METM25, I understand that my name and image may appear on the MET website and in social media channels.");
define("STATIC_FORM_CONFERENCE_REGISTER_ATTENDEE_LIST_PICTURE", "Attendee list");
define("STATIC_FORM_CONFERENCE_REGISTER_ATTENDEE_LIST_PERMISSION", "Yes, I would like to be included in the attendee list on the MET website.");


define("STATIC_FORM_CONFERENCE_REGISTER_CERTIFICATES_TITLE", "Certificate of attendance");
define("STATIC_FORM_CONFERENCE_REGISTER_CERTIFICATES_BODY", "Please check the appropriate box below if you require a certificate of attendance.");
define("STATIC_FORM_CONFERENCE_REGISTER_CERTIFICATES_NAME", "I would like a certificate of attendance");
define("STATIC_FORM_CONFERENCE_REGISTER_CONFERENCE_EXTRA_TITLE", "Conference extras");

define("STATIC_FORM_CONFERENCE_REGISTER_CONFERENCE_GUEST_1", "I will be bringing");
define("STATIC_FORM_CONFERENCE_REGISTER_CONFERENCE_GUEST_2", "guests to the closing dinner.");
define("STATIC_FORM_CONFERENCE_REGISTER_CONFERENCE_BADGE_EMPTY", "Please enter your conference badge information");
define("STATIC_FORM_CONFERENCE_REGISTER_MEMBER_REQUIRED", "To attend the conference you must be a member of MET or of one of MET’s sister associations");
define("STATIC_FORM_CONFERENCE_REGISTER_NO_SELECTED", "Please select at least one workshop or minisession or  check the ‘I will be unable to attend a workshop’ box");
/****************** FIN: FORM CONFERENCE ******************/


/****************** INICIO: MAIL_INSCRIPCION_CONFERENCE ******************/
// define("STATIC_MAIL_INSCRIPTION_CONFERENCE_SUBJECT", "METM23 registration");
// define("STATIC_MAIL_INSCRIPTION_CONFERENCE_TO_MET_BODY", "METM23 registration");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_SPEAKER", "Speaker/Key helper");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_BADGE", "Conference badge");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_DINNER", "Closing dinner");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_NO_DINNER", "no dinners");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_EMAIL_PERMISSION", "Personal data consent");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_CERTIFICATE", "Certificate");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_DINNER_GUEST", "Dinner guests");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_WINE_RECEPTION_GUEST", "Friday evening reception guests");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_PAYMENT_METHOD", "Payment method");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_FEE", "Conference fee");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_DINNER_GUEST_COST", "Dinner guest(s)");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_WINE_RECEPTION_GUEST_COST", "Friday evening reception guest(s)");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_DINNER_OPTOUT_COST", "No closing dinner");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_EXTRA_WORKSHOP_FEE", "Workshop/minisession fee");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_AMOUNT_PAYABLE", "Total amount payable");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_INVOICE_REQUIRED", "INVOICE REQUIRED");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_BILLING_DETAILS", "Billing details");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_BILLING_NIF", "VAT or tax ID number");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_BILLING_NAME", "Name");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_BILLING_COMPANY_NAME", "Company name");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_BILLING_ADDRESS", "Address");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_BILLING_ZIPCODE", "Postal code");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_BILLING_CITY", "City");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_BILLING_PROVINCE", "Province");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_BILLING_COUNTRY", "Country");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_COMMENTS", "Comments");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_TO_USER_PAYPAL", "Thank you for registering for METM25. We have received your registration information.");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_TO_USER_BANK_TRANSFER", "Thank you for registering for METM25! We have received your registration information and will let you know when we receive confirmation of payment. Remember that the early bird rate applies to registrations received <b>and paid</b> by Thursday, 20 July.");
define("STATIC_MAIL_INSCRIPTION_CONFERENCE_ADDITION_PROGRAM", "&nbsp;");
define("STATIC_MAIL_INSCRIPTION_CONFERENCE_FACEBOOK", "&nbsp;");
define("STATIC_MAIL_INSCRIPTION_CONFERENCE_PROVIDER_INFORMATION", "<b>YOUR REGISTRATION DETAILS:</b>");
define("STATIC_MAIL_INSCRIPTION_CONFERENCE_WORKSHOP", "Pre-conference workshop(s)/minisession(s)");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_INVOICE_REQUIRED_TO_USER", "Invoice required");
define("STATIC_MAIL_INSCRIPCION_CONFERENCE_FOOTER", "In the meantime, please do not hesitate to contact me if you have any further questions.
  <br><br>
  Best regards,<br>
" . STATIC_MEMBERSHIP . "<br><a href='mailto:membership@metmeetings.org'>Membership Chair</a>
<br><br>NB. This is an automatically generated message from a notification-only address. Please do not reply to this message.
  ");
/****************** FIN: MAIL_INSCRIPCION_CONFERENCE ******************/

/****************** INICIO: CONFERENCE_OPTIONS ******************/
define("STATIC_CONFERENCE_OPTIONS_PAGE_TITLE", "My METM");
define("STATIC_CONFERENCE_OPTIONS_LOGIN_INSTRUCTIONS", "If you are a MET member, sign in as usual (here to the right &rarr;).<br/> If you are <strong>not</strong> a MET member, sign in below.");
define("STATIC_CONFERENCE_OPTIONS_LOGIN_TITLE", "Non-member conference sign-in");
define("STATIC_CONFERENCE_OPTIONS_LOGIN_BUTTON", "Sign in as non-member");
define("STATIC_CONFERENCE_OPTIONS_IMPORTANT", "Important");
define("STATIC_INDICATE_FREQUENT_EMAIL", "We’ll use this email address to send you important updates and instructions before the event.");
define("STATIC_PRESS_SAVE", "<span style='color:maroon;font-weight: bold;'>After making changes remember to press the <i>Save</i> button at the bottom of the page.</span>");
define("STATIC_CHANGES_SAVED", "<span style='color:maroon;font-weight: bold;'>CHANGES SAVED!</span>");
define("DYNAMIC_CHANGES_WARNING", "Changes made here do not affect the data in your MET profile.");
define("STATIC_CONFERENCE_DETAILS_TITLE", "Event contact details");
define("STATIC_WORKSHOPS_SECTION_TITLE", "Workshops");
define("STATIC_CONFERENCE_WORKSHOPS_SIGNUPS", "You have signed up for the following workshops:");
define("STATIC_CONFERENCE_OPTIONS_BRINGING", "You are bringing");
define("STATIC_CONFERENCE_OPTIONS_NONE", "None");
define("DYNAMIC_WORKSHOP_CHANGE", "Contact the <a href='mailto:development@metmeetings.org'>CPD chair</a> to add or change a workshop.");
define("DYNAMIC_RECEPTION_CHANGE", "If you would like to change the number of reception guests, contact the <a href='mailto:treasurer@metmeetings.org'>treasurer</a>.");
define("DYNAMIC_DINNER_CHANGE", "</br>If you would like to opt in/out of the closing dinner or change the number of guests, contact the <a href='mailto:treasurer@metmeetings.org'>treasurer</a>.");
define("DYNAMIC_DINNER_TEASER", "<strong>You will shortly be able to choose your closing dinner dishes.</strong>");
define("DYNAMIC_LIMITATION_INSTRUCTION", "You may sign up to one off-METM meal group only.<br />");
define("DYNAMIC_SEE_PROGRAMME", "To remove a choice, click the button again.<br />See <a href='" . STATIC_OFF_METMS . "' target='_blank'>off-METM programme</a> for more details.");
define("DYNAMIC_SEE_PROGRAMME_W", "See <a href='" . STATIC_METM_PROGRAMME . "' target='_blank'>programme</a> for more details.");
define("STATIC_CONFERENCE_OPTIONS_DIET", "Dietary requirements");
// define("STATIC_CONFERENCE_OPTIONS_DIET", "Enter your dietary requirements for off-METM tables.");
define("STATIC_CONFERENCE_OPTIONS_DIET2", "Click in box below to select any allergies and intolerances. This information will be used to provide food for all; however, MET accepts no liability.");
define("DYNAMIC_OFFMETM_TEASER", "<strong>Coming soon...</strong>");
define("DYNAMIC_OFFMETM_TEASER2", "You have signed up for these off-METM activities (click the group icon to see who you are with):");
define("DYNAMIC_OFFMETM_FOOTNOTE", "<br>You are welcome to come along to any activities that don't need a sign-up. See the <a href='" . STATIC_OFF_METMS . "'>off-METM programme</a> for more details.");
define("STATIC_CHOIR_WARNING", "Note that our lunch groups are at the same time as the first choir rehearsal.");
define("DYNAMIC_CONFERENCE_OPTIONS_NO_DINNER", "You are <strong>not</strong> signed up for the closing dinner.");
define("DYNAMIC_CONFERENCE_OPTIONS_DINNER", "You have signed up for the closing dinner. <a href='" . STATIC_METM_DINNER_MENU . "' target='_blank'>See full menu</a>.");
define("DYNAMIC_CONFERENCE_OPTIONS_DINNER_GUESTS", "guests to the closing dinner.");
define("STATIC_CONFERENCE_CHOOSE_DISH_PROMPT", "Choose menu");
define("STATIC_CONFERENCE_CHOOSE_ALLERGIES_PROMPT", "Click to select allergies and intolerances");
define("STATIC_CONFERENCE_OFFMETM_TITLE", "Off-metm sign-ups");
define("STATIC_CONFERENCE_OPTIONS_FORM_SAVE", "Save changes");
$randomNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
//define("STATIC_NO_CONFERENCE_REGISTRATION", "<p>Your METM options and choices will appear here if you have registered for the conference and are signed in.</p><p>Non-members: find your password in the confirmation email, then enter your details in the sign-in section on this page.</p><p>You may also need to refresh your browser (press CTRL and F5 together).</p><p>Still can't see My METM? Try clicking <a href='https://www.metmeetings.org/en/my-metm:1415?reload=" . $randomNumber . "'>here</a>. If that fails, write to the <a href='mailto:webmaster@metmeetings.org'>webmaster</a>.</p>");
define("STATIC_NO_CONFERENCE_REGISTRATION", "<script>var formId = 'reloadForm';</script><p>Your METM options and choices will appear here if you have registered for the conference and are signed in.</p><p>Non-members: find your password in the confirmation email, then enter your details in the sign-in section on this page.</p><p>You may also need to refresh your browser (press CTRL and F5 together).</p><p>Still can't see My METM? Try clicking <a href='#' onclick='document.getElementById(formId).submit(); return false;'>here</a>. If that fails, write to the <a href='mailto:webmaster@metmeetings.org'>webmaster</a>.</p><form id='reloadForm' action='/en/my-metm:1415' method='POST' style='display:none;'><input type='hidden' name='reload' value=" . $randomNumber . "></form>");
define("STATIC_CONFERENCE_BREAKING_NEWS_TITLE", "METM25 latest");
define("STATIC_CONFERENCE_RAFFLE_TITLE", "Raffle ticket number");
define("STATIC_CONFERENCE_FIRST_TIMER", "Tick the box if this is your first-ever METM");
define("STATIC_CONFERENCE_LAST_MINUTE_TITLE", "Last minute");
define("STATIC_CONFERENCE_TMIE_OFFSET_MINUTES", "3600");
define("STATIC_CONFERENCE_TMIE_OFFSET_HOURS", "1");
define("STATIC_CONFERENCE_COUNCIL_LEAD", "Council (");
define("STATIC_CONFERENCE_COUNCIL_TRAIL", ")");
define("STATIC_CONFERENCE_BADGE_LENGTH", "25");
define("STATIC_CONFERENCE_FIRST_BAND", "4");
define("STATIC_CONFERENCE_SECOND_BAND", "5");
define("STATIC_DINNER_REMINDER", "Please complete your dietary requirements and/or closing dinner choices.");
define("STATIC_CONFERENCE_FIRST_WORKSHOP_DATE", "2025-10-16");
define("STATIC_CONFERENCE_FIRST_WORKSHOP_DAY", "</strong><i>Thursday afternoon</i><strong>");
define("STATIC_CONFERENCE_SECOND_WORKSHOP_DATE", "2025-10-17");
define("STATIC_CONFERENCE_SECOND_WORKSHOP_DAY", "</strong><i>Friday morning</i><strong>");
define("STATIC_CONFERENCE_RAFFLE_DISABLED", "<p class='form-text'>On Saturday morning, click here to get your raffle ticket.</p><div style='text-align:center;'><button disabled>Get raffle ticket</button></div>");
define("STATIC_CONFERENCE_RAFFLE_ENABLED", "<p class='form-text'>On Saturday morning, click here to get your raffle ticket.</p><div style='text-align:center;'><button onclick='pickRaffleTicket()'>Get raffle ticket</button></div>");
define("STATIC_CLINIC_QUESTION_PROMPT", "Enter your tech request here, e.g., how to set up a macro; how to use tags; quick demo. State operating system and tool version.");
define("STATIC_ALLERGIES_CLOSED", "Your allergies and intolerances are:");
define("STATIC_GUEST_ALLERGIES_START", "Click in box below to select any allergies and intolerances for guest n. ");
define("STATIC_GUEST_ALLERGIES_END", ". This information will be used to provide food for all; however, MET accepts no liability.");
define("STATIC_GUEST_ALLERGIES_FROZEN", "With the following allergies and intolerances:");
// define("STATIC_EMAIL_SHARE", "<small><sup>*</sup> Thursday/Friday meal groups are initially limited to one per person. By choosing a lunch or dinner group you agree to share your email address with your table leader.</small>");
define("STATIC_EMAIL_SHARE", "<small><sup>*</sup> By choosing a lunch or dinner group you agree to share your email address with your table leader.</small>");
define("STATIC_BADGE_INSTRUCTIONS", "Keep lines short. See badge preview below to check for cut-off text. Do <em>not</em> use ALL CAPS.");
define("STATIC_OFFMETM_ERROR", "Unfortunately, some of the off-METM activities you chose filled before you pressed the Save button. Please check your choices.");
define("STATIC_DINNER_GUEST_NAME", "First name(s) of dinner guest n. ");
define("STATIC_DINNER_GUEST_SURNAME", "Last name(s) of dinner guest n. ");
define("STATIC_RECEPTION_GUEST_NAME", "First name(s) of reception guest n. ");
define("STATIC_RECEPTION_GUEST_SURNAME", "Last name(s) of reception guest n. ");
/****************** FIN: CONFERENCE_OPTIONS ******************/
/****************** INICIO: FORM STRIPE ******************/
define("STATIC_STRIPE_TITLE", "Pay by credit or debit card");
define("STATIC_STRIPE_INTRO", "Enter your card details and press “Submit”. MET does not receive, process or store your card data. All card data is securely transmitted from your device directly to the <a href='https://stripe.com/about' target='_blank'>Stripe</a> payment platform.");
define("STATIC_STRIPE_PAY1", "Payment of €");
define("STATIC_STRIPE_PAY2", "for");
define("STATIC_STRIPE_NAME", "Name on card");
define("STATIC_STRIPE_DETAILS", "Card details <br /><small>(CVC is the 3 or 4-digit security code on the back of your card)</small>");
define("STATIC_STRIPE_SUBMIT", "Submit payment");
/****************** FIN: FORM STRIPE ******************/
?>