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

## Minify

Uses the CSS compressor class from Minify: http://code.google.com/p/minify/