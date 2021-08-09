<?php
if ( ! function_exists('clean_whitespace')) {
    /**
     * ToDo remove this and sanitise on output
     */
    function sanitise($str) {
        return clean($str . '', 'strip_all');
    }

    /**
     * Remove Whitespace
     *
     * @param string $input
     * @return string
     */
    function clean_whitespace($input)
    {
        $clear = preg_replace('~[\r\n\t]+~', ' ', trim($input));
        $clear = preg_replace('/ +/', ' ', $clear);
        return $clear;
    }

    /**
     * Remove any HTML or PHP content for cleanliness
     * not for security/XSS use md_to_html() instead
     *
     * @param string $markdown
     * @return string untrusted markdown
     */
    function prepare_markdown($markdown)
    {
        // return strip_tags($markdown);
        return urldecode(html_entity_decode(clean($markdown . '', 'strip_all')));
    }

    /**
     * Convert markdown to HTML and sanitise HTML through a whitelist (htmlpurifier)
     *
     * @param string $markdown
     * @return string sanitised html
     */
    function md_to_html($markdown)
    {
        return clean(Markdown::convertToHtml($markdown . ''));
    }

    /**
     * Convert markdown to Plain Text. Removes Markdown/HTML/PHP tags
     *
     * @param string $markdown
     * @return string
     */
    function md_to_str($markdown)
    {
        return clean_whitespace(clean(Markdown::convertToHtml($markdown . ''), 'strip_all'));
    }

}
