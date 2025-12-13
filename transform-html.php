<?php

$filename = 'test-text.txt';

// Tags to exclude from attribute stripping (case-insensitive)
$exclude = "a, p, img";
$excludedTags = array_map('strtolower', array_map('trim', explode(',', $exclude)));

// Attributes to preserve per tag (case-insensitive)
$preserveAttributes = [
    'a' => ['href', 'title'],
    'img' => ['src', 'alt', 'title', 'width', 'height'],
    'p' => []
];

// Tags to remove entirely (case-insensitive), including closing tag if applicable
$remove = "meta, form";
$removeTags = array_map('strtolower', array_map('trim', explode(',', $remove)));

// Read HTML from file
$html = file_get_contents($filename);
if ($html === false) {
    die("Failed to read file: $filename");
}

// Load HTML
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

// Remove entire elements in $removeTags
$xpath = new DOMXPath($dom);
foreach ($removeTags as $tag) {
    foreach ($xpath->query('//' . $tag) as $nodeToRemove) {
        $nodeToRemove->parentNode->removeChild($nodeToRemove);
    }
}

// Strip attributes from all other elements
$allElements = $dom->getElementsByTagName("*");
foreach ($allElements as $element) {
    $tagName = strtolower($element->nodeName);

    if (in_array($tagName, $excludedTags)) {
        // Preserve only specific attributes
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
        // Strip all attributes
        while ($element->attributes->length > 0) {
            $element->removeAttributeNode($element->attributes->item(0));
        }
    }
}

// Define semantic replacements (tag name => new tag name)
$semanticMap = [
    'b' => 'strong',
    'i' => 'em',
    'u' => 'ins',
    's' => 'del',
];

// Replace traditional tags with semantic equivalents
foreach ($semanticMap as $oldTag => $newTag) {
    $nodes = $dom->getElementsByTagName($oldTag);
    while ($nodes->length) {
        $oldNode = $nodes->item(0);
        $newNode = $dom->createElement($newTag);

        // Copy child nodes (preserving structure)
        foreach (iterator_to_array($oldNode->childNodes) as $child) {
            $newNode->appendChild($child->cloneNode(true));
        }

        $oldNode->parentNode->replaceChild($newNode, $oldNode);
    }
}

// Remove <font> and <center> by replacing with content or styled div
$removeAndReplaceTags = ['font', 'center'];
foreach ($removeAndReplaceTags as $tag) {
    $nodes = $dom->getElementsByTagName($tag);
    while ($nodes->length) {
        $oldNode = $nodes->item(0);

        if ($tag === 'center') {
            $newNode = $dom->createElement('div');
            $newNode->setAttribute('style', 'text-align: center;');
        } else {
            // Just unwrap <font> (remove the tag but keep children)
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

// Remove bold from inside headers
foreach (range(1, 6) as $level) {
    $headings = $dom->getElementsByTagName('h' . $level);
    foreach ($headings as $heading) {
        foreach (iterator_to_array($heading->getElementsByTagName('strong')) as $strong) {
            while ($strong->firstChild) {
                $heading->insertBefore($strong->firstChild, $strong);
            }
            $heading->removeChild($strong);
        }
        foreach (iterator_to_array($heading->getElementsByTagName('b')) as $b) {
            while ($b->firstChild) {
                $heading->insertBefore($b->firstChild, $b);
            }
            $heading->removeChild($b);
        }
    }
}

// Overwrite class on all <h2> tags
$h2Tags = $dom->getElementsByTagName('h2');
foreach ($h2Tags as $h2) {
    $h2->setAttribute('class', 'titleSection');
}

// Overwrite class on all <h3> tags
$h3Tags = $dom->getElementsByTagName('h3');
foreach ($h3Tags as $h3) {
    $h3->setAttribute('class', 'subTitleSection');
}

// Remove <p><strong>...</strong></p> wrappers inside list items
$liList = $dom->getElementsByTagName('li');

// Convert to array to avoid live DOM issues
$liArray = iterator_to_array($liList);

foreach ($liArray as $li) {
    // Normalize to merge adjacent text nodes
    $li->normalize();

    // Check that <li> has exactly one child node and it's a <p>
    if (
        $li->childNodes->length === 1 &&
        $li->firstChild instanceof DOMElement &&
        strtolower($li->firstChild->nodeName) === 'p'
    ) {
        $p = $li->firstChild;

        // Normalize <p> as well
        $p->normalize();

        // Check that <p> has exactly one child and it's a <strong>
        if (
            $p->childNodes->length === 1 &&
            $p->firstChild instanceof DOMElement &&
            strtolower($p->firstChild->nodeName) === 'strong'
        ) {
            $strong = $p->firstChild;

            // Clone all contents of <strong>
            $fragment = $dom->createDocumentFragment();
            foreach (iterator_to_array($strong->childNodes) as $child) {
                $fragment->appendChild($child->cloneNode(true));
            }

            // Remove <p> entirely from <li>
            $li->removeChild($p);

            // Append the contents directly to <li>
            $li->appendChild($fragment);
        }
    }
}

// Define replacements
$find = "&nbsp;, <?xml encoding=\"utf-8\" ?>";
$replace = " ,";

// Convert to arrays (preserve empty replacements)
$findArray = array_map('trim', preg_split('/,/', $find, -1, PREG_SPLIT_NO_EMPTY));
$replaceArray = preg_split('/,/', $replace, -1); // do not trim here

// Sanity check: match count
if (count($findArray) !== count($replaceArray)) {
    die("Error: The number of find and replace terms must match.\n");
}

// Get final HTML from DOM
$outputHtml = $dom->saveHTML();

// Perform string replacements
$outputHtml = str_replace($findArray, $replaceArray, $outputHtml);

// Remove empty tags (e.g. <p></p>, <div>   </div>)
$outputHtml = preg_replace('/<([a-z0-9]+)(\s[^>]*)?>\s*<\/\1>/i', '', $outputHtml);

// Save cleaned HTML back to file
file_put_contents($filename, $outputHtml);

echo "File successfully cleaned and saved.\n";
?>
