<?php
/**

 * @package Mehrwertsteuer_Rechner
 * @version 1.2.1
 */

// Verhindere direkten Zugriff
if (!defined('ABSPATH')) {
    // Wenn nicht über WordPress aufgerufen, 403 Forbidden zurückgeben
    http_response_code(403);
    exit('Direkter Zugriff auf diese Datei ist nicht erlaubt.');
}

// Silence is golden.