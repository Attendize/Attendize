<?php
/**
 * Ok, glad you are here
 * first we get a config instance, and set the settings
 * $config = HTMLPurifier_Config::createDefault();
 * $config->set('Core.Encoding', $this->config->get('purifier.encoding'));
 * $config->set('Cache.SerializerPath', $this->config->get('purifier.cachePath'));
 * if ( ! $this->config->get('purifier.finalize')) {
 *     $config->autoFinalize = false;
 * }
 * $config->loadArray($this->getConfig());.
 *
 * You must NOT delete the default settings
 * anything in settings should be compacted with params that needed to instance HTMLPurifier_Config.
 *
 * @link http://htmlpurifier.org/live/configdoc/plain.html
 */

return [

    'encoding'  => 'UTF-8',
    'finalize'  => true,
    'cachePath' => storage_path('app/purifier'),
    'settings'  => [
        'default' => [
            'HTML.Doctype'             => 'HTML 4.01 Transitional',
            'HTML.Allowed'             => 'a[href|title],b,blockquote,br,code,dd,del,div,dl,dt,em,h1,h2,h3,h4,h5,h6,hr,i,img[width|height|alt|src],ins,kbd,li,ol,p,p[style],pre,q,s,samp,span[style],strike,strong,sub,sup,table[summary],tbody,td[abbr],tfoot,th[abbr],thead,tr,tt,ul,var',
            'CSS.AllowedProperties'    => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty'   => true,
            'AutoFormat.Linkify'       => true,
        ],
        'strip_all' => [
            'HTML.Allowed'             => '',
        ],
        'youtube' => [
            'HTML.SafeIframe'      => 'true',
            'URI.SafeIframeRegexp' => '%^(http://|https://|//)(www.youtube.com/embed/|player.vimeo.com/video/)%',
        ],
    ],

];
