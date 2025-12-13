<?php

/**
 * 
 * @author Mike
 * @version 1.0
 */

require "includes/load_main_components.inc.php";

// Instanciamos la clase Xtemplate con la plantilla base
$plantilla = new XTemplate("html/index.html");

// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
$subPlantilla = new XTemplate("html/programme_conference.html");

// Asignamos el CSS que corresponde a este apartado
$plantilla->assign("SECTION_FILE_CSS",
        "directory_list.css");

require "includes/load_structure.inc.php";

//Obtenemos la conferencia actual
$conferenciaactual = $db->callProcedure("CALL ed_sp_web_conferencia_actual()");
$datoConferencia = $db->getData($conferenciaactual);
$numeroConferencia = $datoConferencia["id_conferencia"];

//Obtenemos la url asociada alla pagina web
$resultadoMenuSeo = $db->callProcedure("CALL ed_sp_web_menu_seo_obtener(" . $idMenu . "," . $_SESSION["id_idioma"] . ")");
$datoMenuSeo = $db->getData($resultadoMenuSeo);
$vectorAtributosMenu["idioma"] = $_SESSION["siglas"];
$vectorAtributosMenu["id_menu"] = $idMenu;
$vectorAtributosMenu["seo_url"] = $datoMenuSeo["seo_url"];
$urlActualAux = generalUtils::generarUrlAmigableMenu($vectorAtributosMenu);

$subPlantilla->assign("CONTENIDO_DESCRIPCION",
        $datoMenuSeo["descripcion"]);

// IMPORTANT BIT
$parts = explode(":", $urlActualAux);
$number = end($parts);  // Gets the last element
$filename = $_SERVER['DOCUMENT_ROOT'] . '/documentacion/conference_data/sessions.*.' . $number . '.txt';
$files = glob($filename);

if (empty($files)) {
    // Construct the full file path
    $filename = $_SERVER['DOCUMENT_ROOT'] . '/documentacion/conference_data/sessions.' . $numeroConferencia . '.' . $number . '.txt';

    // Create an empty file
    file_put_contents($filename, '');
} else {
    $filename = $files[0];
}

$talleres = [];
$resultadoTallerConferencia = $db->callProcedure("CALL ed_sp_web_taller_conferencia_obtener_concreto(3,-1)");

while ($datoTallerConferencia = $db->getData($resultadoTallerConferencia)) {
    $id = $datoTallerConferencia["id_taller"];
    $talleres[$id] = $datoTallerConferencia; // Store the whole row
}

$html = '';
$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$currentGroup = null;
$openTalkBlock = false;
$talks = [];
$talkGroups = [];
$openTalkGroupBlock = false;
$sponsor = '';
$icons = [];

foreach ($lines as $line) {
    $fields = explode('|', $line);

    $type = trim($fields[0]);

    if ($type === 'Day') {
        if ($openTalkBlock) {
             printTalks($talks);
            $talks = [];
            $openTalkBlock = false;
        }
		if ($openTalkGroupBlock) {
            printTalkGroup($talkGroups);
            $talkGroups = [];
            $openTalkGroupBlock = false;
        }
      
        $dayName = trim($fields[2] ?? $fields[1]);
        // Extract first word and clean it (e.g., \"Thursday,\" → \"thursday\")
        $firstWord = strtolower(preg_replace('/[^a-zA-Z]/', '', strtok($dayName, ' ')));
        $idName = $firstWord;

        $html .= '<div class="row programme-row day">';
        $html .= '<div class="col-12" id="' . htmlspecialchars($idName) . '">' . htmlspecialchars($dayName) . '</div>';
        $html .= '</div>' . "\n\n";

    } elseif ($type === 'Catering-Desk' || $type === 'Off-METM' || $type === 'Keynote-AG' || $type === 'Session') {
        if ($openTalkBlock) {
             printTalks($talks);
            $talks = [];
            $openTalkBlock = false;
        }
		if ($openTalkGroupBlock) {
            printTalkGroup($talkGroups);
            $talkGroups = [];
            $openTalkGroupBlock = false;
        }

        $time = trim($fields[1]);
        $room = trim($fields[2]);
        $title = trim($fields[3]);
        $url = trim($fields[4]);
        $extra = trim($fields[5] ?? '');
		
		if ($type === 'Catering-Desk') {
		$rowEvent = 'catering';
		} elseif ($type === 'Keynote-AG') {
		$rowEvent = 'assembly';
		} elseif ($type === 'Session') {
		$rowEvent = 'workshop';
		}else {
		$rowEvent = 'offmetm';
		}
		
		$rowClass = 'programme-row ' . $rowEvent;
        if (empty($time)) {
            $rowClass = 'programme-row-topless ' . $rowEvent;
        }

        $html .= '<div class="row ' . $rowClass . '">';
        $html .= '<div class="col-2 time">' . htmlspecialchars($time) . '</div>';
        $html .= '<div class="col-10 align-self-center';
		
        if (empty($room)) $html .= ' title';
        $html .= '">';

		// Insert <hr /> if current is topless
        if (empty($time) && $type !== 'Session') {
            $html .= '<hr />';
        }	

        if (!empty($room)) {
            $html .= '<div class="room">' . htmlspecialchars($room) . '</div>';
            $html .= '<span class="title">';
        }

        if (empty($url)) {
            $html .= htmlspecialchars($title) . ' ';
		} else {
            $html .= '<a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($title) . '</a> ';
		}      

        if (!empty($extra)) {
            $html .= ' <small>' . htmlspecialchars($extra) . '</small>';
        }

        if (!empty($room)) {
            $html .= '</span>';
        }

        $html .= '</div>';
        $html .= '</div>' . "\n\n";

    } elseif ($type === 'Talk') {
		if ($openTalkGroupBlock) {
            printTalkGroup($talkGroups);
            $talkGroups = [];
            $openTalkGroupBlock = false;
        }
		
        $time = trim($fields[1]);
        $room = trim($fields[2]);
        $title = trim($fields[3]);
        $url = trim($fields[4]);
        $presenter = trim($fields[5] ?? '');
        $full = trim($fields[6] ?? '');
		
		if (!empty($time) && $openTalkBlock) {
            printTalks($talks);
            $talks = [];
            $openTalkBlock = false;			
				}

        $talks[] = [$time, $room, $title, $url, $presenter, $full];
        $openTalkBlock = true;
    } elseif ($type === 'Talk-Group') {
        if ($openTalkBlock) {
            printTalks($talks);
            $talks = [];
            $openTalkBlock = false;
        }

        $time = trim($fields[1]);
        $room = trim($fields[2]);
        $title = trim($fields[3]);
        $url = trim($fields[4]);
        $presenter = trim($fields[5] ?? '');

        $talkGroups[$room][] = [$time, $title, $url, $presenter];
        $openTalkGroupBlock = true;
    } elseif ($type === 'Sponsors') {
        $sponsor = trim($fields[3]);
        $sponsorUrl = trim($fields[4]);
    } elseif ($type === 'Icon') {
    $icons[] = [
        'name' => trim($fields[3]),
        'url'  => trim($fields[4]),
        'alt'  => trim($fields[5])
    ];
    } elseif ($type === 'Advert') { 
    if ($openTalkBlock) {
        printTalks($talks);
        $talks = [];
        $openTalkBlock = false;
    }
    if ($openTalkGroupBlock) {
        printTalkGroup($talkGroups);
        $talkGroups = [];
        $openTalkGroupBlock = false;
    }

    $adLink = $fields[4] ?? '';
    $adAlt = $fields[5] ?? '';
    $adLogo = $fields[2] ?? '';
    $adText = $fields[3] ?? '';

    // Final HTML
    $html .= '<div id="advert" style="border: 2px solid #d8fff1; background-color: white; padding: 16px; margin: 24px 0; display: flex; align-items: center; gap: 16px; border-radius: 6px;">';
    $html .= '<div style="flex-shrink: 0;"><a href="' . htmlspecialchars($adLink) . '" style="text-decoration: none;" target="_blank">';
    $html .= '<img alt="' . htmlspecialchars($adAlt) . '" src="' . htmlspecialchars($adLogo) . '" style="height: 40px;" />';
    $html .= '</a></div>';
    $html .= '<div style="flex: 1; display: flex; flex-direction: column; justify-content: center;">';
    $html .= '<a href="' . htmlspecialchars($adLink) . '" style="text-decoration: none;" target="_blank">';
    $html .= '<span style="color: #6c5f26; font-size: 16px; font-family: sans-serif; line-height: 1.4;">' . htmlspecialchars($adText) . '</span>';
    $html .= '</a></div>';
    $html .= '</div>' . "\n\n";
    }
}

// Print remaining talks
if ($openTalkBlock) {
     printTalks($talks);
}
if ($openTalkGroupBlock) {
    printTalkGroup($talkGroups);
}

// Add programme-row-last to the last row
$html = preg_replace('#(<div class="row [^\"]*)"(?!.*<div class="row)#s', '$1 programme-row-last"', $html);

// Add programme-row-last to the last row before <div id="advert">
$html = preg_replace_callback(
    '#(.*?)(<div id="advert")#s',
    function ($matches) {
        $before = $matches[1];
        $advert = $matches[2];

        // Add class to the last row before the advert
        $before = preg_replace(
            '#(<div class="row [^"]*)"(?!.*<div class="row)#s',
            '$1 programme-row-last"',
            $before
        );

        return $before . $advert;
    },
    $html
);

// replace \' with ' everywhere
$html = str_replace("\\'", "'", $html);

// Link sponsors to METM sponsorship webpage

if (!empty(trim($sponsor))) {
    $sponsors = array_map('trim', explode(',', $sponsor));

    foreach ($sponsors as $name) {
        if ($name === '') continue; // extra safety if the list contains empty entries

        $pattern = '/' . preg_quote($name, '/') . '/i';

        $html = preg_replace_callback(
            $pattern,
            function ($matches) use ($sponsorUrl, $name) {
                return '<a href="' . htmlspecialchars($sponsorUrl) . '">' . htmlspecialchars($name) . '</a>';
            },
            $html
        );
    }
}

// Remove carets  
    $html = str_replace('^', '', $html);

// Force link colour
$html = str_replace("a href", "a style='color: #0dbabe; text-decoration: none;' href", $html);

// Insert icons

foreach ($icons as $icon) {
    // Check if $icon['url'] is a valid URL (icon image or emoji)
    $isIconUrl = filter_var($icon['url'], FILTER_VALIDATE_URL);

    // Check if $icon['alt'] is a valid URL (download link)
    $isDownloadUrl = filter_var($icon['alt'], FILTER_VALIDATE_URL);

    if ($isIconUrl) {
        // The icon itself is an image
        $iconHtml = '<img src="' . htmlspecialchars($icon['url'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($icon['alt'], ENT_QUOTES, 'UTF-8') . '">';
    } else {
        // Otherwise, use the emoji or text as the icon
        $iconHtml = '<span role="img" aria-label="' . htmlspecialchars($icon['alt'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($icon['url'], ENT_QUOTES, 'UTF-8') . '</span>';
    }

    if ($isDownloadUrl) {
        // If tooltip text is a URL, make the icon clickable (download link)
        $replacement = '<a href="' . htmlspecialchars($icon['alt'], ENT_QUOTES, 'UTF-8') . '" target= "_blank" download>' . $iconHtml . '</a>';
    } else {
        // Otherwise, use tooltip as before
        $replacement = str_replace('<img', '<img title="' . htmlspecialchars($icon['alt'], ENT_QUOTES, 'UTF-8') . '"', $iconHtml);
        $replacement = str_replace('<span', '<span title="' . htmlspecialchars($icon['alt'], ENT_QUOTES, 'UTF-8') . '"', $replacement);
    }

    // Replace standalone word, case-insensitively
    $pattern = '/\b' . preg_quote($icon['name'], '/') . '\b/i';
    $html = preg_replace($pattern, $replacement, $html);
}

// Functions

function printTalks($talks) {
	global $html, $talleres;
    if (empty($talks)) return;

    $first = true;
    foreach ($talks as $talk) {
        list($time, $room, $title, $url, $presenter, $full) = $talk;

        if (!$first && !$time) {
            $html .= '<hr />';
        } else {
            $html .= '<div class="row programme-row-topless">';
            $html .= '<div class="col-2 time">' . htmlspecialchars($time) . '</div>';
            $html .= '<div class="col-10 align-self-center">';
        }
        $first = false;

        if (!$time) {
        $html .= '<div class="room">' . htmlspecialchars($room) . '</div>';
        $html .= '<span class="title">';
          
        if (empty($url)) {
            $html .= htmlspecialchars($title) . ' ';
		} else {
            $html .= '<a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($title) . '</a> ';
		}

        if (is_numeric($full) && isTallerFull((int)$full)) {
		    $html .= '<span style="color:red; font-weight:bold;">FULL!</span>';
		}
          
        $html .= '<span class="presenter">' . htmlspecialchars($presenter) . '</span>';
        $html .= '</span>';
        } else {
        $html .= '<div class="room">' . htmlspecialchars($room) . '</div>';
        $html .= '<span class="title">';

        if (empty($url)) {
            $html .= htmlspecialchars($title) . ' ';
		} else {
            $html .= '<a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($title) . '</a> ';
		}

        $html .= '<span class="presenter">' . htmlspecialchars($presenter) . '</span>';
        $html .= '</span>';
        }
    }

    $html .= '</div>';
    $html .= '</div>' . "\n\n";
}

function printTalkGroup($talkGroups) {
	global $html;
    if (empty($talkGroups)) return;

	$first = true;
    foreach ($talkGroups as $room => $talks) {
      
        $html .= '<div class="row programme-row-topless">';
        $html .= '<div class="col-2 time">&nbsp;</div>';
        $html .= '<div class="col-10 align-self-center">';
      
      	if ($first) {
            $html .= '<hr />';
        } 
        $first = false;
      
        $html .= '<div class="room">' . htmlspecialchars($room) . '</div>';
        $html .= '</div>';

        foreach ($talks as $talk) {
            list($time, $title, $url, $presenter) = $talk;

            $html .= '<div class="col-2 time">' . htmlspecialchars($time) . '</div>';
            $html .= '<div class="col-10 align-self-center">';
            $html .= '<a href="' . htmlspecialchars($url) . '"><span class="title">' . htmlspecialchars($title) . '</span></a>';
            $html .= '<span class="presenter">' . htmlspecialchars($presenter) . '</span>';
            $html .= '</div>';
        }

        $html .= '</div>' . "\n\n"; // End one room block
    }
}

function isTallerFull($id_taller) {
	global $talleres;
    if (isset($talleres[$id_taller])) {
        $dato = $talleres[$id_taller];
        return $dato["total_inscritos"] >= $dato["plazas"];
    }
    return false; // ID not found
}

// IMPORTANT BIT ENDS

$subPlantilla->assign("CONTENIDO_PROGRAMME",
        $html);

$subPlantilla->assign("MENU_ID",
        $idMenu);

//Cargamos el breadcrumb
require "includes/load_breadcrumb.inc.php";

//Cargamos los menus hijos del lateral derecho
require "includes/load_menu_left.inc.php";

//Cargamos el slider en caso de que tenga imagenes
require "includes/load_slider.inc.php";

$subPlantilla->parse("contenido_principal");

/**
 * Realizamos todos los parse realcionados con este apartado
 */
$plantilla->parse("contenido_principal.css_form");
$plantilla->parse("contenido_principal.control_superior");
$plantilla->parse("contenido_principal.bloque_ready");


//Exportamos plantilla secundaria a la principal
$plantilla->assign("CONTENIDO",
$subPlantilla->text("contenido_principal"));

//Parse inner page content with lefthand menu
$plantilla->parse("contenido_principal.menu_left");

//Parseamos y sacamos informacion por pantalla
$plantilla->parse("contenido_principal");
$plantilla->out("contenido_principal");
?>