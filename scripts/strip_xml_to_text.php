<?php
// Strip Word document.xml to plain text
// Usage: php scripts/strip_xml_to_text.php path/to/document.xml

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "Run from CLI: php scripts/strip_xml_to_text.php <document.xml>\n");
    exit(1);
}

if ($argc < 2) {
    fwrite(STDERR, "Usage: php scripts/strip_xml_to_text.php <document.xml>\n");
    exit(1);
}

$xmlPath = $argv[1];
if (!file_exists($xmlPath)) {
    fwrite(STDERR, "File not found: {$xmlPath}\n");
    exit(1);
}

$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$dom->formatOutput = false;
if (!@$dom->load($xmlPath)) {
    fwrite(STDERR, "Failed to load XML: {$xmlPath}\n");
    exit(1);
}

$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$paragraphs = $xpath->query('//w:document/w:body/w:p');
$out = [];
foreach ($paragraphs as $p) {
    $texts = $xpath->query('.//w:t', $p);
    $runs = [];
    foreach ($texts as $t) {
        $runs[] = $t->textContent;
    }
    $brs = $xpath->query('.//w:br', $p);
    $line = implode('', $runs);
    if ($brs && $brs->length > 0) {
        $line = preg_replace('/\x{000B}/u', "\n", $line);
    }
    $line = trim($line);
    $out[] = $line;
}

$text = implode("\n", $out);
$text = preg_replace("/\n{3,}/", "\n\n", $text);
echo trim($text), "\n";