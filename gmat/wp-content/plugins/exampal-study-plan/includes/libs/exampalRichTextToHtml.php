<?php
class RichTextService{

    /**
     * Takes array where of CSS properties / values and converts to CSS stri$
     *
     * @param array
     * @return string
     */
    private function assembleCSS($pValue = array())
    {
        $pairs = array();
        foreach ($pValue as $property => $value) {
            $pairs[] = $property . ':' . $value;
        }
        $string = implode('; ', $pairs);
        return $string;
    }

    /**
     * Create CSS style (PHPExcel_Style_Font)
     *
     * @param    PHPExcel_Style_Font        $pStyle            PHPExcel_Style_Font
     * @return    array
     */
    private function createCSSStyleFont(PHPExcel_Style_Font $pStyle)
    {
        // Construct CSS
        $css = array();
        // Create CSS
        if ($pStyle->getBold()) {
            $css['font-weight'] = 'bold';
        }
        if ($pStyle->getUnderline() != PHPExcel_Style_Font::UNDERLINE_NONE && $pStyle->getStrikethrough()) {
            $css['text-decoration'] = 'underline line-through';
        } elseif ($pStyle->getUnderline() != PHPExcel_Style_Font::UNDERLINE_NONE) {
            $css['text-decoration'] = 'underline';
        } elseif ($pStyle->getStrikethrough()) {
            $css['text-decoration'] = 'line-through';
        }
        if ($pStyle->getItalic()) {
            $css['font-style'] = 'italic';
        }
        $css['color']       = '#' . $pStyle->getColor()->getRGB();
        $css['font-family'] = '\'' . $pStyle->getName() . '\'';
        $css['font-size']   = $pStyle->getSize() . 'pt';
        return $css;
    }

    /**
     *
     * @param PHPExcel_RichText|string $value
     * @return string
     */
    public function getHTML($value) {
        if(is_string($value)) {
            return $value;
        }

        $cellData = '';
        if ($value instanceof PHPExcel_RichText) {
            // Loop through rich text elements
            $elements = $value->getRichTextElements();
            foreach ($elements as $element) {
                // Rich text start?
                if ($element instanceof PHPExcel_RichText_Run) {
                    $cellData .= ' <span style="' . $this->assembleCSS($this->createCSSStyleFont($element->getFont())) . '">';
                    if ($element->getFont()->getSuperScript()) {
                        $cellData .= '<sup>';
                    } elseif ($element->getFont()->getSubScript()) {
                        $cellData .= '<sub>';
                    }
                }
                // Convert UTF8 data to PCDATA
                $cellText = $element->getText();
                $cellData .= htmlspecialchars($cellText);
                if ($element instanceof PHPExcel_RichText_Run) {
                    if ($element->getFont()->getSuperScript()) {
                        $cellData .= '</sup>';
                    } elseif ($element->getFont()->getSubScript()) {
                        $cellData .= '</sub>';
                    }
                    $cellData .= '</span>';
                }
            }
        }

        return str_replace("\n", "<br>\n", $cellData);
    }
}