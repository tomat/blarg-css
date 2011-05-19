Blarg CSS
--------------

Converts HTML with inline style-attributes to HTML and CSS

## Random thoughts

Best practice tells us to refactor early, and refactor often, especially in regards to CSS. But more often than not, we don't. Our CSS files grow out of control as we add new rules, overriding the old ones. Even if we did refactor with every addition, this is time consuming, and things break way too easily.
 
The CSS optimization process should be (almost) fully automated. With the right algorithm, we may end up with dramatically smaller stylesheets, and a lot of time saved.

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

    .b0{margin:10px}
    .b1{padding:10px}
    .b2{text-align:center}
    .b3{float:left}

### Generated HTML:

    <div class="b0 b2 b3">
        <p class="b0 b2">paragraph 1</p>
        <p class="b2">paragraph 2</p>
        <p class="b2 b3">paragraph 3</p>
    </div>

    <div class="b1 b3">
        <p class="b2">paragraph 1</p>
        <p class="b2">paragraph 2</p>
        <p class="b0 b2 b3">paragraph 3</p>
    </div>

## Known limitations

* When converting from CSS to inline styles, selectors using :hover/:visited/etc. will not be saved

## Todo

* Group properties in two, three, or more, per class (optimize for size)
* Detect superfluous properties by keeping track of inheritance
* Turn into a Bundle (Symfony2 style)
* Hook into the Symfony2 templating system somehow (or maybe just Twig?)
* Convert HTML/CSS to HTML with inline styles

## Minify

Uses the CSS compressor class from Minify version 2.1.3, by Stephen Clay: http://code.google.com/p/minify/

## CSS to Inline Styles

Uses a modified version of the CSS to Inline Styles class version 1.0.3 by Tijs Verkoyen: http://classes.verkoyen.eu/css_to_inline_styles

## PHP Simple HTML DOM Parser

Uses a modified version of the PHP Simple HTML DOM Parser version 1.11 by S.C. Chen: http://sourceforge.net/projects/simplehtmldom/
