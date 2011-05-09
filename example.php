<?php
require_once("BlargCSS.php");

$blargCSS = new BlargCSS();

$html = <<< HTML
<div style="float: left; margin: 10px; text-align: center;">
    <p style="margin: 10px; text-align: center;">paragraph 1</p>
    <p style="text-align: center;">paragraph 2</p>
    <p style="text-align: center; float: left;">paragraph 3</p>
</div>
HTML;

$html2 = <<< HTML
<div style="float: left; padding: 10px;">
    <p style="text-align: center;">paragraph 1</p>
    <p style="text-align: center;">paragraph 2</p>
    <p style="text-align: center; float: left; margin: 10px;">paragraph 3</p>
</div>
HTML;

$html_id = $blargCSS->addSource($html);
$html2_id = $blargCSS->addSource($html2);

$blargCSS->run();

$css = $blargCSS->getCSS();
$html_new = $blargCSS->getSource($html_id);
$html2_new = $blargCSS->getSource($html2_id);
?>
<html>
    <body>
        <pre>
<b>HTML input:</b>
<?=htmlentities($html)?>
<br />
<?=htmlentities($html2)?>
        </pre>
        <hr>
        <pre>
<b>Generated CSS:</b>
<?=$css?>
<br />
<b>Generated HTML:</b>
<?=htmlentities($html_new)?>
<br />
<?=htmlentities($html2_new)?>
        </pre>
    </body>
</html>