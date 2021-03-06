<?php
require_once("minify/Compressor.php");

class BlargCSS
{
    public $cssClassPrefix = 'b';
    public $cssClassPrefixDot = '.b';

    private $sources = array();
    private $sourcesStripped = array();
    private $sourcesAfter = array();
    private $styleBlocks = array();
    private $styleBlocksAfter = array();
    private $cssProperties = array();
    private $cssSortedProperties = array();
    private $cssClassCounter = 0;

    private $cssBlockClass = '.blarg123';
    private $cssBlockPrefix = '.blarg123 {';
    private $cssBlockSuffix = '}';

    public function addSource($code)
    {
        $codeHash = md5($code);
        $this->sources[$codeHash] = $code;
        return $codeHash;
    }

    public function getSource($codeHash)
    {
        return $this->sourcesAfter[$codeHash];
    }

    public function getCSS()
    {
        $css = "";
        foreach ($this->styleBlocksAfter as $styleBlock)
        {
            $css .= $styleBlock . "\n";
        }
        return $css;
    }

    public function run()
    {
        /**
         * Look for style-attributes in all code blocks,
         * store in $this->styleBlocks,
         * replace style-attribute with class="{{ blargClass(<hash>) }}"
         */
        foreach ($this->sources as $codeHash => $code)
        {
            /**
             * Strip class-declarations from source
             */
            $code = preg_replace("#(<[^ ]+?[^>]*)(?P<classDeclaration>class=\".+?\")([^>]*>)#i", "$1$3", $code);
            $code = preg_replace("#(<[^ ]+?[^>]*)(?P<idDeclaration>id=\".+?\")([^>]*>)#i", "$1$3", $code);

            /**
             * Finds style="*" inside HTML tags (not safe in any way)
             * @todo use xml parser
             */
            preg_match_all("#<(?P<tag>[^/][^ ]*?) [^>]*?(?P<styleAttribute>style=\"(?P<styleBlock>.+?)\")[^>]*>#is", $code, $styleAttributes, PREG_SET_ORDER);
            foreach ($styleAttributes as $styleAttributes)
            {
                $styleBlockWithClass = $this->cssBlockPrefix . $styleAttributes["styleBlock"] . $this->cssBlockSuffix;
                $parsedCSS = Minify_CSS_Compressor::process($styleBlockWithClass);
                $cssHash = md5($parsedCSS);

                if (!isset($this->styleBlocks[$cssHash]))
                {
                    $cssClass = $this->cssClassCounter;
                    $parsedCSS = str_replace($this->cssBlockClass, $this->cssClassPrefixDot . $cssClass, $parsedCSS);

                    /**
                     * Finds the properties-part of the class declaration produced by Minify_CSS_Compressor.
                     */
                    preg_match("#\{(.+?);?\}#s", $parsedCSS, $parsedPropertiesStr);

                    $parsedProperties = explode(";", $parsedPropertiesStr[1]);

                    /**
                     * Check for superfluous background/border/padding/margin properties
                     */
                    $parsedPropertiesReverse = array_reverse($parsedProperties, true);
                    $background_found = false;
                    $border_found = false;
                    $padding_found = false;
                    $margin_found = false;
                    foreach($parsedPropertiesReverse as $key => $property)
                    {
                        if (!$background_found && strpos($property, "background:") === 0)
                        {
                            $background_found = true;
                        }
                        elseif (!$border_found && strpos($property, "border:") === 0)
                        {
                            $border_found = true;
                        }
                        elseif (!$padding_found && strpos($property, "padding:") === 0)
                        {
                            $padding_found = true;
                        }
                        elseif (!$margin_found && strpos($property, "margin:") === 0)
                        {
                            $margin_found = true;
                        }
                        elseif ($background_found && strpos($property, "background") === 0
                                || $border_found && strpos($property, "border-width") === 0
                                || $border_found && strpos($property, "border-color") === 0
                                || $border_found && strpos($property, "border-top") === 0
                                || $border_found && strpos($property, "border-bottom") === 0
                                || $border_found && strpos($property, "border-left") === 0
                                || $border_found && strpos($property, "border-right") === 0
                                || $padding_found && strpos($property, "padding") === 0
                                || $margin_found && strpos($property, "margin") === 0)
                        {
                            unset($parsedProperties[$key]);
                        }
                    }

                    $this->styleBlocks[$cssHash] = array(
                        'css' => $parsedCSS,
                        'class' => $cssClass,
                        'properties' => $parsedProperties,
                    );

                    $this->cssClassCounter++;
                }

                foreach($this->styleBlocks[$cssHash]['properties'] as $cssProperty)
                {
                    $propertyHash = md5($cssProperty);
                    
                    if(!isset($this->cssProperties[$propertyHash]))
                    {
                        $this->cssProperties[$propertyHash] = array(
                            'property' => $cssProperty,
                            'count' => 0,
                        );
                    }

                    $this->cssProperties[$propertyHash]['count']++;
                    $this->cssProperties[$propertyHash]['codeBlocks'][$codeHash] = & $this->sources[$codeHash];
                    $this->cssProperties[$propertyHash]['styleBlocks'][$cssHash] = & $this->styleBlocks[$cssHash];

                    /**
                     * Keep track of how many times each property/value is used,
                     * store each property in $this->cssSortedProperties[<count>]
                     *
                     * Prioritize background:/border:/padding:/margin:
                     */
                    if(strpos($cssProperty,"background:") === 0
                        || strpos($cssProperty,"border:") === 0
                        || strpos($cssProperty,"padding:") === 0
                        || strpos($cssProperty,"margin:") === 0)
                    {
                        $countNow = 1000000;
                        $countBefore = 0;
                    }
                    else
                    {
                        $countNow = $this->cssProperties[$propertyHash]['count'];
                        $countBefore = $countNow - 1;
                    }

                    if($countBefore > 0)
                    {
                        unset($this->cssSortedProperties[$countBefore][$propertyHash]);
                    }

                    $this->cssSortedProperties[$countNow][$propertyHash] = & $this->cssProperties[$propertyHash];
                }

                $code = str_replace($styleAttributes["styleAttribute"], 'class="{{ blargClass("'.$cssHash.'") }}"', $code);
            }

            $this->sourcesStripped[$codeHash] = $code;
        }

        /**
         * Loop through sorted CSS properties and assign new classes.
         */
        $this->cssClassCounter = 0;
        krsort($this->cssSortedProperties, SORT_NUMERIC);
        foreach ($this->cssSortedProperties as $count => $properties)
        {
            foreach ($properties as $propertyHash => $property)
            {
                $this->styleBlocksAfter[] = $this->cssClassPrefixDot . $this->cssClassCounter . "{" . $property['property'] . "}";

                foreach ($property['styleBlocks'] as $cssHash => $styleBlock)
                {
                    if(!isset($this->styleBlocks[$cssHash]['newClass']))
                    {
                        $this->styleBlocks[$cssHash]['newClass'] = "";
                    }
                    $this->styleBlocks[$cssHash]['newClass'] .= $this->cssClassPrefix . $this->cssClassCounter . " ";
                }

                $this->cssClassCounter++;
            }
        }

        /**
         * Loop through all style blocks and insert newClass into code
         */
        foreach ($this->styleBlocks as $cssHash => $styleBlock)
        {
            if (!isset($styleBlock['newClass']))
            {
                throw new Exception("No new class found for styleblock: $cssHash");
            }
            else
            {
                foreach($this->sourcesStripped as $codeHash => $code)
                {
                    if(!isset($this->sourcesAfter[$codeHash]))
                    {
                        $this->sourcesAfter[$codeHash] = $code;
                    }

                    $this->sourcesAfter[$codeHash] = str_replace('{{ blargClass("'.$cssHash.'") }}', trim($styleBlock['newClass']), $this->sourcesAfter[$codeHash]);
                }
            }
        }
    }
}