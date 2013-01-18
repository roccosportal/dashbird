<?php

namespace Pvik\Web\ViewHelpers;

use Pvik\Core\Path;

/**
 * Contains useful functionc for a view.
 */
class HtmlHelper {

    /**
     * Creates a html link.
     * <a href="/blog/overview/">Title</a>
     * @param string $Path Resolves the ~/ to a relative path
     * @param string $Title
     * @param array $HtmlAttributes 
     */
    public function Link($Path, $Title, $HtmlAttributes = array()) {
        $RelativePath = Path::RelativePath($Path);
        $LinkHtml = '<a';
        $HtmlAttributes['href'] = $RelativePath;
        $LinkHtml .= $this->GenerateAttributes($HtmlAttributes);
        $LinkHtml .= '>' . $Title . '</a>';
        echo $LinkHtml;
    }

    /**
     * Same as echo.
     * @param string $Html 
     */
    public function Out($Html) {
        echo $Html;
    }

    /**
     * Converts an assosciative array to a html string.
     * array ("ID" => "myid", "class" = "myclass")
     * to
     * ID="myID" class="myclass"
     * @param array $HtmlAttributes
     * @return string 
     */
    public function GenerateAttributes(array $HtmlAttributes) {
        $Html = '';
        foreach ($HtmlAttributes as $Name => $Value) {
            $Html .= ' ' . $Name . '="' . $Value . '"';
        }
        return $Html;
    }

    /**
     * Creates a link to a stylesheet file.
     * Output example:
     * <link rel="stylesheet" type="text/css" href="/css/stylesheet.css" />
     * @param string $Path Resolves the ~/ to a relative path
     */
    public function StyleSheetLink($Path) {
        $RelativePath = Path::RelativePath($Path);
        $Html = '<link rel="stylesheet" type="text/css" href="' . $RelativePath . '" />';
        echo $Html;
    }

    /**
     * Creates a link to a javascript file.
     * Output example:
     * <script type="text/javascript" src="/js/javascript.js"></script>
     * @param string $Path Resolves the ~/ to a relative path
     */
    public function JavaScriptLink($Path) {
        $RelativePath = Path::RelativePath($Path);
        $Html = '<script type="text/javascript" src="' . $RelativePath . '"></script>';
        echo $Html;
    }

    /**
     * Creates a link to a fav icon.
     * Output example:
     * <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
     * <link rel="icon" href="/favicon.ico" type="image/x-icon" />
     * @param type $Path 
     */
    public function FaviconLink($Path) {
        $RelativePath = Path::RelativePath($Path);
        $Html = '<link rel="shortcut icon" href="' . $RelativePath . '" type="image/x-icon" />';
        $Html .= '<link rel="icon" href="' . $RelativePath . '" type="image/x-icon" />';
        echo $Html;
    }

    /**
     * Creates a errofield if a error exists in the validation state for the field.
     * Output example:
     * <span class="errorfield">Field can not be empty.</span>
     * @param ValidationState $ValidationState
     * @param string $Field
     * @param string $Class Html class
     */
    public function Errorfield(\Pvik\Utils\ValidationState $ValidationState, $Field, $Class = 'errorfield') {
        if ($ValidationState != null) {
            if ($ValidationState->GetError($Field) != null) {
                echo '<span class="' . $Class . '">' . $ValidationState->GetError($Field) . '</span>';
            }
        }
    }

}
