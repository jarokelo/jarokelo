<?php

namespace app\components\helpers;

use yii\helpers\Html as HtmlParent;

/**
 *
 */
class Html extends HtmlParent
{
    /**
     * @var string
     */
    const REGEX_HTML_LINEBREAKS = '(<br>|<br \\/>|<br\\/>)';

    /**
     * Format the urls in the text into clickable anchors.
     *
     * @param string $text
     * @param array $linkClasses [optional]
     * @return mixed|null|string
     */
    public static function formatText($text = null, $linkClasses = null)
    {
        if (!$text) {
            return '';
        }

        // striping out HTML codes
        $text = strip_tags(trim($text));

        // making URLs links
        // Catch all links with protocol
        $reg = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,}(\/\S*)?/';
        $text = preg_replace($reg, sprintf('<a href="$0" class="%s" rel="nofollow" target="_blank" title="$0">$0</a>', $linkClasses), $text);

        // catch all emails
        $text = preg_replace_callback(
            '/(\S+\@\S+\.\S+)/',
            function ($matches) use ($linkClasses) {
                list($_, $link) = $matches;

                // Google is not transformed to link
                if (preg_match('/google\.[^.]+/', $link)) {
                    return $link;
                }

                return static::a(
                    $link,
                    sprintf('mailto:%s', $link),
                    [
                        'class' => $linkClasses,
                        'rel' => 'nofollow',
                        'target' => '_blank',
                        'title' => $link,
                    ]
                );
            },
            $text
        );

        // line breaks
        $text = nl2br($text);
        $text = preg_replace('/^(\s+)?' . self::REGEX_HTML_LINEBREAKS . '{1,}(\s+)?/', '', $text);
        $text = preg_replace('/((\s+)?' . self::REGEX_HTML_LINEBREAKS . '(\s+)?){3,}/', '<br /><br />', $text);
        return $text;
    }

    /**
     * @param string $text
     * @return string|string[]|null
     */
    public static function replaceMultipleLineBreaks($text)
    {
        return preg_replace("/((\\s+)?([\r\n])(\\s+)?){3,}/", "\n\n", $text);
    }
}
