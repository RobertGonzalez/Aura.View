<?php
/**
 * 
 * This file is part of the Aura Project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\View\Helper;

/**
 * 
 * Escapes special characters for HTML.
 * 
 * @package Aura.View
 * 
 */
class Escape extends AbstractHelper
{
    /**
     * 
     * Escapes a text string.
     * 
     * @param string $text The text to escape.
     * 
     * @return string The escaped string.
     * 
     */
    public function __invoke($text)
    {
        return $this->escape($text);
    }
}
