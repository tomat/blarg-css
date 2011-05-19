Blarg CSS
--------------

Converts HTML with inline style-attributes to HTML and CSS

## Example

### HTML input:

    <div style="float: left; margin: 10px; text-align: center;">
        <p style="margin: 10px; text-align: center;">paragraph 1</p>
        <p style="text-align: center;">paragraph 2</p>
        <p style="text-align: center; float: left;">paragraph 3</p>
    </div>
    <div style="float: left; padding: 10px;">
        <p style="text-align: center;">paragraph 1</p>
        <p style="text-align: center;">paragraph 2</p>
        <p style="text-align: center; float: left; margin: 10px;">paragraph 3</p>
    </div>

### Generated CSS:

    .b6{text-align:center}
    .b9{float:left}
    .b10{margin:10px}
    .b12{padding:10px}

### Generated HTML:

    <div class="b6 b9 b10">
        <p class="b6 b10">paragraph 1</p>
        <p class="b6">paragraph 2</p>
        <p class="b6 b9">paragraph 3</p>
    </div>
    <div class="b9 b12">
        <p class="b6">paragraph 1</p>
        <p class="b6">paragraph 2</p>
        <p class="b6 b9 b10">paragraph 3</p>
    </div>

## Todo

* Group properties in pairs and triples
* Detect superfluous properties by keeping track of inheritance.
* Turn into a Bundle (Symfony2 style)
* Hook into the Symfony2 templating system somehow (or maybe just Twig?)
* Convert HTML/CSS to HTML with inline styles

## Minify

Uses the CSS compressor class from Minify, by Stephen Clay: http://code.google.com/p/minify/
Based on version 2.1.3

## CSS to Inline Styles

Uses a modified version of the CSS to Inline Styles class by Tijs Verkoyen: http://classes.verkoyen.eu/css_to_inline_styles
Based on version 1.0.3

## PHP Simple HTML DOM Parser

Uses a modified version of the PHP Simple HTML DOM Parser by S.C. Chen: http://sourceforge.net/projects/simplehtmldom/
Based on version 1.11