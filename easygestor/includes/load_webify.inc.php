<?php

function Webify($string) {
  
    $find = "&nbsp;,
    [QUESTION],
    [ANSWER]<br />,
    [ANSWER],
    [FIRST QUESTION],
    [KILL ACCORDION],
    <?xml encoding=\"utf-8\" ?>
    ";
    $replace = " ,
    </p></div><button class='privacy-accordion'>,
    </button><div class='panel'><p>,
    </button><div class='panel'><p>,
    </h3><button class='privacy-accordion'>,
    </p></div>,
    ";
  
    // Tags to exclude from attribute stripping
    $exclude = "a, p, img, button, div, table, td, tr, b, i, strong, em";
    $excludedTags = array_map('strtolower', array_map('trim', explode(',', $exclude)));

    // Attributes to preserve per tag
    $preserveAttributes = [
        'a' => ['href', 'title'],
        'img' => ['src', 'alt', 'title', 'style', 'width', 'height'],
        'p' => [],
        'button' => ['class'],
        'table' => ['style'],
        'td' => ['style'],
        'div' => ['class']
    ];

    // Tags to remove entirely
    $remove = "span, meta, form";
    $removeTags = array_map('strtolower', array_map('trim', explode(',', $remove)));

    // Load HTML
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $string, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
  
// change small fonts into <small> tags
$spans = $dom->getElementsByTagName('span');
$toReplace = [];

foreach ($spans as $span) {
    $style = $span->getAttribute('style');
    if (preg_match('/font-size:\s*(\d+(?:\.\d+)?)pt/i', $style, $matches)) {
        $fontSize = floatval($matches[1]);
        if ($fontSize < 10) {
            $toReplace[] = $span;
        }
    }
}

foreach ($toReplace as $oldSpan) {
    $small = $dom->createElement('small');

    // Copy children
    foreach (iterator_to_array($oldSpan->childNodes) as $child) {
        $small->appendChild($child->cloneNode(true));
    }

    $oldSpan->parentNode->replaceChild($small, $oldSpan);
}
  
  // Improved Word paragraph handling
  // Get all <br> elements
$brs = $dom->getElementsByTagName('br');

// We need to iterate in reverse because we're modifying the DOM
for ($i = $brs->length - 1; $i >= 0; $i--) {
    $br = $brs->item($i);
    $prev = $br->previousSibling;

    // Check if previous sibling is a </span>
    if ($prev && $prev->nodeType === XML_ELEMENT_NODE && $prev->nodeName === 'span') {
        // Close current span with </p>
        $pClose = $dom->createElement('p');
        $br->parentNode->replaceChild($pClose, $prev);
        $pClose->appendChild($prev);

        // Replace <br> with <p>
        $pOpen = $dom->createElement('p');
        $br->parentNode->replaceChild($pOpen, $br);
    }
}
  
    // Remove entire elements
  $xpath = new DOMXPath($dom);
foreach ($removeTags as $tag) {
    foreach ($xpath->query('//' . $tag) as $nodeToRemove) {
        // Move all child nodes up one level (into the parent of the node to be removed)
        while ($nodeToRemove->firstChild) {
            $nodeToRemove->parentNode->insertBefore($nodeToRemove->firstChild, $nodeToRemove);
        }
        // Now remove the empty tag
        $nodeToRemove->parentNode->removeChild($nodeToRemove);
    }
}

    // Strip attributes
    $allElements = $dom->getElementsByTagName("*");
    foreach ($allElements as $element) {
        $tagName = strtolower($element->nodeName);
        if (in_array($tagName, $excludedTags)) {
            $allowed = isset($preserveAttributes[$tagName]) ? array_map('strtolower', $preserveAttributes[$tagName]) : [];
            $attrsToRemove = [];

            foreach ($element->attributes as $attr) {
                if (!in_array(strtolower($attr->name), $allowed)) {
                    $attrsToRemove[] = $attr->name;
                }
            }

            foreach ($attrsToRemove as $attrName) {
                $element->removeAttribute($attrName);
            }
        } else {
            while ($element->attributes->length > 0) {
                $element->removeAttributeNode($element->attributes->item(0));
            }
        }
    }  

    // Semantic replacements
    $semanticMap = [
        'b' => 'strong',
        'i' => 'em',
        'u' => 'ins',
        's' => 'del',
    ];

    foreach ($semanticMap as $oldTag => $newTag) {
        $nodes = $dom->getElementsByTagName($oldTag);
        while ($nodes->length) {
            $oldNode = $nodes->item(0);
            $newNode = $dom->createElement($newTag);
            foreach (iterator_to_array($oldNode->childNodes) as $child) {
                $newNode->appendChild($child->cloneNode(true));
            }
            $oldNode->parentNode->replaceChild($newNode, $oldNode);
        }
    }

    // Remove or replace <font> and <center>
    $removeAndReplaceTags = ['font', 'center'];
    foreach ($removeAndReplaceTags as $tag) {
        $nodes = $dom->getElementsByTagName($tag);
        while ($nodes->length) {
            $oldNode = $nodes->item(0);
            if ($tag === 'center') {
                $newNode = $dom->createElement('div');
                $newNode->setAttribute('style', 'text-align: center;');
            } else {
                $fragment = $dom->createDocumentFragment();
                foreach (iterator_to_array($oldNode->childNodes) as $child) {
                    $fragment->appendChild($child->cloneNode(true));
                }
                $oldNode->parentNode->replaceChild($fragment, $oldNode);
                continue;
            }
            foreach (iterator_to_array($oldNode->childNodes) as $child) {
                $newNode->appendChild($child->cloneNode(true));
            }
            $oldNode->parentNode->replaceChild($newNode, $oldNode);
        }
    }

// Unwrap specific placeholder tokens from surrounding tags
$placeholders = ['[QUESTION]', '[FIRST QUESTION]', '[ANSWER]', '[KILL ACCORDION]'];

$xpath = new DOMXPath($dom);

foreach ($placeholders as $token) {
    // Find all elements whose text content is exactly the token
    $nodes = $xpath->query("//*[normalize-space(text()) = '$token']");

    foreach (iterator_to_array($nodes) as $node) {
        // Ensure the node contains only the token and nothing else
        if (trim($node->textContent) === $token && $node->childNodes->length === 1 && $node->firstChild->nodeType === XML_TEXT_NODE) {
            $textNode = $dom->createTextNode($token);
            $node->parentNode->replaceChild($textNode, $node);
        }
    }
}

// New bold handling routine
//  $tags = ['p', 'div', 'li', 'h1', 'h2', 'h3']; // Adjust based on your needs

// foreach ($tags as $tagName) {
//    $elements = $dom->getElementsByTagName($tagName);
//    $toProcess = [];

    // We must clone the list because DOM updates live NodeLists
//    foreach ($elements as $el) {
//        $toProcess[] = $el;
//    }

//    foreach ($toProcess as $element) {
//        if (
//            $element->childNodes->length === 1 &&
//            $element->firstChild instanceof DOMElement &&
//            in_array(strtolower($element->firstChild->tagName), ['b', 'strong'])
//        ) {
//            $boldNode = $element->firstChild;

            // Move children out of <b>/<strong> safely
//            $fragment = $dom->createDocumentFragment();
//            foreach (iterator_to_array($boldNode->childNodes) as $child) {
//                $fragment->appendChild($child);
//            }

//            $element->replaceChild($fragment, $boldNode);
//        }
//    }
// }

  
// Normalize heading structure: promote all <h1>-<h6> by one level
// Step 1: Promote only <h1> to <h2>
$h1Nodes = $dom->getElementsByTagName('h1');
foreach (iterator_to_array($h1Nodes) as $h1) {
    $h2 = $dom->createElement('h2');
    foreach (iterator_to_array($h1->childNodes) as $child) {
        $h2->appendChild($child->cloneNode(true));
    }
    $h1->parentNode->replaceChild($h2, $h1);
}

// Step 2: Keep only the first <h2>, demote all <h2>–<h6> to <h3>
$seenFirstH2 = false;
for ($level = 2; $level <= 6; $level++) {
    $tag = 'h' . $level;
    $nodes = $dom->getElementsByTagName($tag);
    foreach (iterator_to_array($nodes) as $node) {
        if ($tag === 'h2' && !$seenFirstH2) {
            $seenFirstH2 = true;
            continue; // Keep first h2
        }

        // Replace any remaining heading with <h3>
        $h3 = $dom->createElement('h3');
        foreach (iterator_to_array($node->childNodes) as $child) {
            $h3->appendChild($child->cloneNode(true));
        }
        $node->parentNode->replaceChild($h3, $node);
    }
}
  
    // Set class on headers
    foreach ($dom->getElementsByTagName('h2') as $h2) {
        $h2->setAttribute('class', 'titleSection');
    }
    foreach ($dom->getElementsByTagName('h3') as $h3) {
        $h3->setAttribute('class', 'subTitleSection');
    }
  
    // Force links to open in a new tab, except those pointing to metmeetings.org
    $links = $dom->getElementsByTagName('a');
    foreach ($links as $link) {
    $href = $link->getAttribute('href');

    // Skip if href starts with metmeetings.org (absolute or relative)
    if (preg_match('#^https?://(www\.)?metmeetings\.org#', $href)) {
        continue;
    }

    // Set target="_blank"
    $link->setAttribute('target', '_blank');
    }
  
  // Unwrap all <button class="privacy-accordion">...</button> elements from their parent tags
$buttons = $dom->getElementsByTagName('button');

// Convert to array to avoid live DOM issues
foreach (iterator_to_array($buttons) as $button) {
    // Only process buttons with the specific class
    if ($button->getAttribute('class') === 'privacy-accordion') {
        $parent = $button->parentNode;

        // Skip if parent is body or html
        if ($parent && !in_array(strtolower($parent->nodeName), ['body', 'html'])) {
            $grandparent = $parent->parentNode;

            if ($grandparent) {
                // Move the button after the parent
                $grandparent->insertBefore($button->cloneNode(true), $parent->nextSibling);

                // Remove parent entirely
                $grandparent->removeChild($parent);
            }
        }
    }
}

  // Make images responsive
  $images = $dom->getElementsByTagName('img');
  
  foreach (iterator_to_array($images) as $img) {
    
    // PART THAT SKIPS IMAGES IN PROFILES STRAT
    $parent = $img->parentNode;

    // Skip <img> tags inside <p> tags with image + text pattern
    if ($parent && $parent->nodeName === 'p' && $parent->firstChild === $img) {
        continue;
    }
    // PART THAT SKIPS IMAGES IN PROFILES ENDES

    $width = $img->getAttribute('width');
    $height = $img->getAttribute('height');

    if ($width && $height) {
        $img->removeAttribute('width');
        $img->removeAttribute('height');
        $img->setAttribute('style', "max-width: {$width}px; max-height: {$height}px; width: 100%; height: auto;");
    }
}
  
  // kill occurrences of <p>&nbsp;</p>
  $paragraphs = $dom->getElementsByTagName('p');
foreach (iterator_to_array($paragraphs) as $p) {
    $text = trim($p->textContent, "\xC2\xA0 \t\n\r\0\x0B");
    if ($text === '') {
        $p->parentNode->removeChild($p);
    }
}
  
  // Remove <br> tags inside <button> elements
$buttons = $dom->getElementsByTagName('button');
foreach ($buttons as $button) {
    $brs = [];
    foreach ($button->childNodes as $child) {
        if ($child instanceof DOMElement && strtolower($child->tagName) === 'br') {
            $brs[] = $child;
        }
    }
    foreach ($brs as $br) {
        $button->removeChild($br);
    }
}

    //Switch from DOM handling to string handling
    $outputHtml = $dom->saveHTML();
  
    // Convert old format profiles to table format
    $outputHtml = convertProfilesToTableSection($outputHtml);

    // Convert [profiles] to table format (being developed)
    // $outputHtml = convertProfilesToTableSectionMarkers($outputHtml);
  
    // Normalize <br> tags (self-closing or not) to <br />
    $outputHtml = preg_replace('/<br\s*\/?>/i', '<br />', $outputHtml);

    // Replace double or more <br /> tags with </p><p>
    $outputHtml = preg_replace('/(<br\s*\/>\s*){2,}/i', '</p><p>', $outputHtml);

    // Remove empty div tags double or more <br /> tags with </p><p>
    $outputHtml = preg_replace('/<div\s*[^>]*>\s*<\/div>/i', '', $outputHtml);

// THIS MY BE CAUSING EXCESSIVE SPACING  
    // Replace &nbsp; outside of any HTML tags with <br /> inside next tag wrapper
//    $outputHtml = preg_replace_callback('/(^|\>|\<\/[^>]+>)(\s*)&nbsp;(\s*)(?=<|\z)/', function($matches) {
    // Replace &nbsp; with <br /> when it's outside wrapper tags
//    return $matches[1] . $matches[2] . '<br />' . $matches[4];
//    }, $outputHtml);
  
    // Final replacements

    $findArray = array_map('trim', preg_split('/,/', $find, -1, PREG_SPLIT_NO_EMPTY));
    $replaceArray = preg_split('/,/', $replace, -1);

    $outputHtml = str_replace($findArray, $replaceArray, $outputHtml);
  
// THIS MAY ONLY BE WORKING FOR THE EMPTY TAGS
  // Remove empty tags or tags with only non-breaking spaces (e.g. <p></p>, <div>   </div>, <p>&nbsp;</p>)
$outputHtml = preg_replace('/<([a-z0-9]+)(\s[^>]*)?>\s*(?:&nbsp;|\s)*<\/\1>/i', '', $outputHtml);

// NOT SURE IF THIS IS NEEDED ANY MORE
    //  Remove <p><strong>...</strong></p> inside <li>
//    $outputHtml = preg_replace(
//    '#<li>\s*<p>\s*<strong>(.*?)</strong>\s*</p>\s*</li>#is',
//    '<li>$1</li>',
//    $outputHtml
//    );
  
  // Remove <p>...</p> inside <li>
$outputHtml = preg_replace(
    '#<li>\s*<p>(.*?)</p>\s*</li>#is',
    '<li>$1</li>',
    $outputHtml
);
  
// Check for presence of [QUESTION] in original input
if (strpos($string, '[QUESTION]') !== false) {
    // Check if the script is already included in the output
    if (strpos($outputHtml, 'var acc = document.getElementsByClassName("privacy-accordion");') === false) {
        $script = <<<EOD
<script>
var acc = document.getElementsByClassName("privacy-accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("activated");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight){
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    } 
  });
}
</script>
EOD;

        // Append the script only if it's not already there
        $outputHtml .= "\n\n" . $script;
    }
}

    return $outputHtml;
}

// FUNCTIONS

function convertProfilesToTableSection($html) {
    $pattern = '#(<p>\s*<img[^>]+>\s*.*?</p>)#is';
    preg_match_all($pattern, $html, $matches, PREG_OFFSET_CAPTURE);

    if (empty($matches[0])) {
        return $html; // No matches
    }

    // Identify the first and last matched positions to extract the full block
    $startPos = $matches[0][0][1];
    $lastMatch = end($matches[0]);
    $endPos = $lastMatch[1] + strlen($lastMatch[0]);

    // Extract the profile block from the original HTML
    $profileBlock = substr($html, $startPos, $endPos - $startPos);

    // Generate table rows from the matched paragraphs
    $rows = '';
    foreach ($matches[0] as $match) {
        $pBlock = $match[0];

        // Extract <img> tag
        if (preg_match('#<img([^>]+)>#i', $pBlock, $imgMatch)) {
            $imgTag = '<img' . $imgMatch[1] . '>';
        } else {
            continue; // skip malformed
        }

        // Remove the <img> tag from the paragraph to isolate text
        $text = preg_replace('#<img[^>]+>#i', '', $pBlock);
        $text = strip_tags($text, '<br><strong><em><b><i><a>'); // preserve light formatting
        $text = trim($text);

        $rows .= '<tr>';
        $rows .= '<td style="border: none; vertical-align: top; padding-top: 15px;">' . $imgTag . '</td>';
        $rows .= '<td style="border: none;">' . $text . '</td>';
        $rows .= '</tr>';
    }

    // Build the final table
    $table = '<table style="border-collapse: collapse; border: 0; outline: none; box-shadow: none;"><tbody>' . $rows . '</tbody></table>';

    // Replace the profile block with the table
    $html = substr_replace($html, $table, $startPos, $endPos - $startPos);

    return $html;
}

// This function is not working properly (being developed)
function convertProfilesToTableSectionMarkers($content) {
    $startMarker = '[PROFILES]';
    $endMarker = '[KILL PROFILES]';

    $startPos = strpos($content, $startMarker);
    $endPos = strpos($content, $endMarker);

    if ($startPos === false || $endPos === false || $endPos <= $startPos) {
        return $content; // no valid section found
    }

    // Extract everything between the markers
    $before = substr($content, 0, $startPos + strlen($startMarker));
    $middleRaw = substr($content, $startPos + strlen($startMarker), $endPos - ($startPos + strlen($startMarker)));
    $after = substr($content, $endPos);

    // Load HTML fragment into DOM
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="UTF-8"><div>' . $middleRaw . '</div>');

    $body = $dom->getElementsByTagName('div')->item(0);
    $tableRows = [];

    $children = iterator_to_array($body->childNodes);
    $i = 0;

    while ($i < count($children)) {
        $node = $children[$i];

        // Look for an <img> tag (directly or wrapped)
        $imgTag = null;
        if ($node->nodeType === XML_ELEMENT_NODE && $node->tagName === 'img') {
            $imgTag = $node;
        } elseif ($node->nodeType === XML_ELEMENT_NODE) {
            $imgs = $node->getElementsByTagName('img');
            if ($imgs->length > 0) {
                $imgTag = $imgs->item(0);
            }
        }

        if ($imgTag !== null) {
            $imgHTML = $dom->saveHTML($imgTag);

            // Advance to next node to get the description
            $i++;
            $textNode = ($i < count($children)) ? $children[$i] : null;
            $textHTML = '';

            if ($textNode) {
                $textHTML = trim($dom->saveHTML($textNode));
            }

            // Add row to table
            $tableRows[] = "<tr>
                <td style=\"border: none; vertical-align: top; padding-top: 15px;\">{$imgHTML}</td>
                <td style=\"border: none;\">{$textHTML}</td>
            </tr>";
        }

        $i++;
    }

    // Build the table
    $tableHTML = '<table style="border-collapse: collapse; border: 0; outline: none; box-shadow: none;"><tbody>' . implode("\n", $tableRows) . '</tbody></table>';

    // Reconstruct full content
    return $before . "\n" . $tableHTML . "\n" . $after;
}

?>
