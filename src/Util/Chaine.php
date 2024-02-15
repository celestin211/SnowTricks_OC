<?php

declare(strict_types=1);


namespace App\Util;

class Chaine
{
    public static function contientBaliseHtml(string $chaine): bool
    {
        return $chaine !== strip_tags($chaine);
    }

    /**
     * Détermine si le texte contient du HTML suspect.
     */
    public static function contientHtmlSuspect(string $chaine): bool
    {
        $resultats = [];

        // Recherche = suivi de >
        preg_match("/\=(.*)\>/i", $chaine, $resultats);

        if (!empty($resultats)) {
            return true;
        }

        // Recherche < suivi de =
        preg_match("/\<(.*)\=/i", $chaine, $resultats);

        if (!empty($resultats)) {
            return true;
        }

        // Recherche ' ou " suivi de >
        preg_match("/([\\'\\\"]{1})(.*)\>/i", $chaine, $resultats);

        return !empty($resultats);
    }

    public static function supprimeHtmlSuspect(string $chaine): string
    {
        return str_replace(['<', '>', '='], '', $chaine);
    }
}
