<?php
namespace FacebookBirthdays\HTML;

use DOMDocument;
use DOMElement;
use DOMXPath;

class HTML
{
    public static function getXPath($html)
    {
        if (empty($html)) {
            return new DOMXPath(new DOMDocument);
        }

        libxml_use_internal_errors(true);

        $DOM = new DOMDocument;
        $DOM->recover = true;
        $DOM->preserveWhiteSpace = false;

        if (is_string($html)) {
            $DOM->loadHtml($html);
        } elseif ($html instanceof DOMElement) {
            $DOM->appendChild($DOM->importNode($html, true));
        }

        $XPath = new DOMXPath($DOM);

        libxml_use_internal_errors(false);

        return $XPath;
    }

    public static function getFormInputs($html)
    {
        if (empty($html)) {
            return array();
        }

        $xpath = static::getXPath($html);
        $inputs = array();

        foreach ($xpath->query('.//input') as $input) {
            $inputs[$input->getAttribute('name')] = $input->getAttribute('value');
        }

        return $inputs;
    }

    public static function removeTags($html, $tags)
    {
        foreach ((is_array($tags) ? $tags : array($tags)) as $tag) {
            $html = preg_replace('#<'.$tag.'[^>]*>.*</'.$tag.'>#', '', $html);
        }

        return $html;
    }
}
