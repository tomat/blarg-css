<?php
require_once("BlargCSS.php");
require_once("css_to_inline_styles/css_to_inline_styles.php");

ini_set('max_execution_time', '3600');
set_time_limit(3600);
ignore_user_abort(1);
ini_set('display_errors', '1');

/**
 * Fetch content and CSS for example page
 */
$html_org = file_get_contents("http://www.familjeliv.se/Forum-20-163/m58861834.html");
$css = file_get_contents("http://static.familjeliv.se/css/sprite/a734798a2e5c521dea0e334ad03611c7.css");

/**
 * Remove CSS reference from HTML
 */
$html_org = preg_replace('#<link rel="stylesheet" href="http://static.familjeliv.se/css/sprite/.+?.css" type="text/css" media="all" />#', '', $html_org);

/**
 * Since this page uses CSS and not inline styles we first have to translate classes and id:s into style-attributes
 */
$cssInlineStyles = new CSSToInlineStyles($html_org, $css);
$html = $cssInlineStyles->convert();

/**
 * BlargCSS
 */
$blargCSS = new BlargCSS();

/**
 * Add the HTML (with inline styles)
 */
$html_id = $blargCSS->addSource($html);

/**
 * Run conversion
 */
$blargCSS->run();

/**
 * Get generated CSS and HTML
 */
$css = $blargCSS->getCSS();
$html_new = $blargCSS->getSource($html_id);

/**
 * Add CSS to end of <head>.
 */
$html_new = str_replace('</head>', '<style type="text/css">'.$css.'</style>', $html_new);

/**
 * Get size (in bytes) of all the CSS-rules that matched elements in the HTML
 *
$cssSize = $cssInlineStyles->sizeOfMatchingCSSRules;
echo "html/css input: ".number_format((strlen($html_org)+$cssSize)/1024, 1)." KB<br>";
echo "html output: ".number_format(strlen($html_new)/1024, 1)." KB<br>";
 */

echo $html_new;