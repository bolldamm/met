# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

MET Meetings Website - Membership, conference, and workshop management system for Mediterranean Editors and Translators (MET) organization.

**Tech Stack:** PHP (procedural), MySQL stored procedures, XTemplate engine, Stripe/PayPal payments

## Architecture

### Template-Based Rendering (XTemplate)
```
PHP page → load data → assign variables → XTemplate parses HTML → output
```
- Templates in `/html/` use `{VARIABLE_NAME}` syntax
- PHP assigns values: `$plantilla->assign("VARIABLE", $value)`
- Block parsing for lists: `$plantilla->parse("contenido_principal.list_items")`

### Database Interaction
- All database calls use stored procedures via `$db->callProcedure()`
- Stored procedure naming convention: `ed_sp_*` prefix
- Example: `$db->callProcedure("CALL ed_sp_web_home_noticias_obtener_listado()")`

### Key Directories
- `/ajax/` - Form submission endpoints and payment processing
- `/classes/` - Core classes (XTemplate, DatabaseConnection, generalUtils)
- `/config/` - Constants and language dictionaries
- `/includes/` - Reusable PHP includes, form processors, email templates
- `/html/` - XTemplate HTML templates
- `/easygestor/` - Admin backend/CMS system
- `/private/` - Stripe keys and webhook secrets (sensitive)

### Page Flow
Every page loads these in order:
1. `includes/load_main_components.inc.php` - Bootstrap dependencies
2. `includes/load_structure.inc.php` - Menus, breadcrumbs, page metadata
3. Page-specific template from `/html/`

## Configuration

### Environment
Set in `includes/load_environment.inc.php`:
- `MET_ENV = 'LOCAL'` for local development
- `MET_ENV = 'PRODUCTION'` for production

### Key Constants (`config/constants.php`)
- User types: SOCIO=1, EDITOR=2, CONSEJO=3, ADMIN=4, GUEST=5
- Payment types: STRIPE=4, PAYPAL=2, TRANSFERENCIA=1
- Inscription states: Pending=1, Confirmed=2, Rejected=3

### Language
Dictionary files in `config/dictionary/` (en_EN.php, es_ES.php)

## Development

### Local Setup
- Laragon environment (Apache + PHP + MySQL)
- Local database: `met` with root user (no password)
- Access at `http://localhost/`

### Form Processing Pattern
1. HTML form: `/html/forms/form_*.html`
2. PHP processor: `/includes/forms/form_*.php`
3. AJAX handler: `/ajax/save_*.php`
4. Payment callback: `/ajax/last_step_inscription_*.php`
5. Email notification via `/includes/load_send_mail_*.inc.php`

### Adding a New Page
1. Create PHP entry point in root
2. Create template in `/html/`
3. Include `load_main_components.inc.php` and `load_structure.inc.php`
4. Assign variables and parse template

## Key Files

- `classes/databaseConnection.php` - MySQL wrapper with `callProcedure()` method
- `classes/generalUtils.php` - Utility methods (escaping, redirects, URL generation)
- `classes/xtemplate.class.php` - Template engine
- `includes/settings.php` - Database-driven settings (pricing, email credentials)
- `proceed.php` - Main payment processing entry point
- `procedure_outcome.php` - Payment result handling

## Naming Conventions

- Variables: camelCase
- Template variables: UPPERCASE_SNAKE_CASE
- Include files: `load_*.inc.php`
- Stored procedures: `ed_sp_*`
- Test files: `*_TEST.php`

## Verifactu Integration

### Overview
Spanish electronic invoicing compliance via Verifacti API. Invoices are registered with AEAT (Spanish tax authority) and receive a QR code for verification.

### Key Files
- `includes/VerifactiService.php` - Main service class for API communication
- `private/verifacti_keys.php` - API keys (test/production)
- `easygestor/ajax/refresh_invoice_verifacti_status.php` - Status refresh endpoint
- `easygestor/ajax/cancel_invoice_verifacti.php` - Invoice cancellation endpoint
- `logs/verifacti_YYYY-MM.log` - Monthly API interaction logs

### Invoice Flow
1. Invoice created in database (via signup or admin)
2. When admin sends invoice (`send_invoice.php`):
   - Submits to Verifacti API
   - Stores UUID, QR code, huella in database
   - Regenerates PDF with QR code
   - Sends email to customer
3. Bulk send ("Send all unsent") processes each invoice through same flow

### Configuration
Edit `private/verifacti_keys.php` to set your API key:
- **Placeholder** (`YOUR_API_KEY_HERE`) → Verifactu disabled, no QR on invoices
- **Test key** (`vf_test_...`) → Simulated submission, QR appears but not registered with AEAT
- **Live key** (`vf_prod_...`) → Real AEAT submission

**WARNING:** Once you send ONE invoice with a live key, MET is committed to Verifactu mode for the rest of the calendar year (per AEAT regulations). You cannot switch back to "No Verifactu" until January 1st.

### Go Live Checklist

**Prerequisites:**
1. Obtain production API key from Verifacti:
   - Register at https://www.verifacti.com
   - Submit certificate of representation (certificado de representación)
   - Verifacti will provide a production API key

**Configuration Steps:**
1. Edit `private/verifacti_keys.php`:
   ```php
   $verifactiKeys = [
       "api_key" => "vf_prod_YOUR_PRODUCTION_KEY_HERE",
       "api_url" => "https://api.verifacti.com"
   ];
   ```

2. The service auto-enables when a valid API key is detected (not placeholder text)

**Verification:**
1. Create a test invoice in admin panel
2. Send the invoice - check for Verifacti submission
3. Verify QR code appears on PDF
4. Check `logs/verifacti_*.log` for successful submission
5. Use "Refresh Verifacti" button to confirm AEAT registration

**Rollback:**
To disable Verifactu, set the API key back to placeholder:
```php
"api_key" => "YOUR_API_KEY_HERE"
```

### Admin UI Features
- **Verifactu column** in invoice list shows status
- **Refresh Verifacti** button - check status of single invoice
- **Refresh Verifacti (batch)** - refresh status for multiple invoices
- **Anular Verifacti** button - cancel invoice in AEAT system

### Invoice Types Supported
- F1: Standard invoice (requires customer NIF)
- F2: Simplified invoice (no customer details required)
- R1-R5: Rectificativa (corrective) invoices
