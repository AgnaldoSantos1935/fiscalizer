<?php
// Simple DOCX text extractor for console usage
// Usage: php scripts/extract_docx.php path/to/file.docx

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "Run from CLI: php scripts/extract_docx.php <docx>\n");
    exit(1);
}

if ($argc < 2) {
    fwrite(STDERR, "Usage: php scripts/extract_docx.php <docx>\n");
    exit(1);
}

$docxPath = $argv[1];
if (!file_exists($docxPath)) {
    fwrite(STDERR, "File not found: {$docxPath}\n");
    exit(1);
}

if (!class_exists('ZipArchive')) {
    fwrite(STDERR, "ZipArchive extension is required.\n");
    exit(1);
}

$zip = new ZipArchive();
if ($zip->open($docxPath) !== true) {
    fwrite(STDERR, "Failed to open DOCX: {$docxPath}\n");
    exit(1);
}

$index = $zip->locateName('word/document.xml');
if ($index === false) {
    fwrite(STDERR, "word/document.xml not found in DOCX\n");
    $zip->close();
    exit(1);
}

$xmlContent = $zip->getFromIndex($index);
$zip->close();

if ($xmlContent === false) {
    fwrite(STDERR, "Failed to read document.xml\n");
    exit(1);
}

// Load XML and extract text from w:p and w:t nodes
$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$dom->formatOutput = false;
// Suppress errors for malformed XML; DOCX is usually valid though
if (!@$dom->loadXML($xmlContent)) {
    // Fallback: very naive strip tags
    $plain = strip_tags($xmlContent);
    echo trim($plain), "\n";
    exit(0);
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
    // Handle explicit line breaks within runs
    $brs = $xpath->query('.//w:br', $p);
    $line = implode('', $runs);
    if ($brs && $brs->length > 0) {
        // Replace <w:br/> occurrences with newlines where possible
        // Not perfect: Word can split runs around breaks; acceptable for simple extraction
        $line = preg_replace('/\x{000B}/u', "\n", $line); // vertical tab as placeholder if any
    }
    // Trim and add
    $line = trim($line);
    $out[] = $line;
}

// Join paragraphs as lines, collapse multiple blank lines
$text = implode("\n", $out);
$text = preg_replace("/\n{3,}/", "\n\n", $text);
echo trim($text), "\n";