<?php

declare(strict_types=1);

/**
 * Propriété du CISIRH Centre Interministériel de Services Informatiques relatifs aux Ressources Humaines
 * 41 Boulevard Vincent Auriol, 75013 Paris
 * Édité en 2022
 *
 * Ce logiciel est un programme informatique servant à assurer le fonctionnement de l’applicaton SICARDI
 * «  Système d’information pour les Compétences des Agents et la Reconnaissance des Carrières Individuelles ».
 *
 * Ce logiciel est régi par la licence CeCILL-B soumise au droit français et
 * respectant les principes de diffusion des logiciels libres. Vous pouvez
 * utiliser, modifier et/ou redistribuer ce programme sous les conditions
 * de la licence CeCILL-B telle que diffusée par le CEA, le CNRS et l'INRIA
 * sur le site "http://www.cecill.info".
 *
 * En contrepartie de l'accessibilité au code source et des droits de copie,
 * de modification et de redistribution accordés par cette licence, il n'est
 * offert aux utilisateurs qu'une garantie limitée.  Pour les mêmes raisons,
 * seule une responsabilité restreinte pèse sur l'auteur du programme,  le
 * titulaire des droits patrimoniaux et les concédants successifs.
 *
 * A cet égard  l'attention de l'utilisateur est attirée sur les risques
 * associés au chargement,  à l'utilisation,  à la modification et/ou au
 * développement et à la reproduction du logiciel par l'utilisateur étant
 * donné sa spécificité de logiciel libre, qui peut le rendre complexe à
 * manipuler et qui le réserve donc à des développeurs et des professionnels
 * avertis possédant  des  connaissances  informatiques approfondies.  Les
 * utilisateurs sont donc invités à charger  et  tester  l'adéquation  du
 * logiciel à leurs besoins dans des conditions permettant d'assurer la
 * sécurité de leurs systèmes et ou de leurs données et, plus généralement,
 * à l'utiliser et l'exploiter dans les mêmes conditions de sécurité.
 *
 * Le fait que vous puissiez accéder à cet en-tête signifie que vous avez
 * pris connaissance de la licence CeCILL-B, et que vous en avez accepté les
 * termes.
 */

namespace App\Util;


use function App\Util\mb_substr;
use function App\Util\str_contains;

final class Html
{
    /**
     * @var string
     */
    private $html;

    public function __construct(string $html)
    {
        $this->html = $html;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * Retourne si la chaîne de caractères contient une balise non présente dans celles autorisées.
     */
    public function contientAutreBaliseHtml(array $balisesAutorisees = []): bool
    {
        // Si aucune balise autorisée, on retourne si la chaine en contient une
        if (empty($balisesAutorisees)) {
            return Chaine::contientBaliseHtml($this->html);
        }

        return $this->html !== strip_tags($this->html, '<'.implode('><', $balisesAutorisees).'>') || str_contains($this->html, 'javascript:');
    }

    public function contientLienNeCommencantPasPar(array $debutsLiensAutorises): bool
    {
        $resultats = [];
        // On récupère l'intérieur de toutes les balises <a>
        preg_match_all("/<\s*a([^>]*)>(.*?)<\s*\/\s*a\s*>/i", $this->html, $resultats);

        // Pour chaque balise <a>
        foreach ($resultats[1] as $baliseInterieur) {
            $hrefResultats = [];
            // On récupère ce qu'il y a après href=
            preg_match_all("/\s*href\s*=\s*(.*)/i", $baliseInterieur, $hrefResultats);
            $debutLien = ltrim($hrefResultats[1][0], ' "');

            $debutValide = false;

            foreach ($debutsLiensAutorises as $debutLienAutorise) {
                // Si le début du href est valide
                if ($debutLienAutorise === mb_substr($debutLien, 0, mb_strlen($debutLienAutorise))) {
                    $debutValide = true;

                    break;
                }
            }

            if (!$debutValide) {
                return false;
            }
        }

        return true;
    }

    public function contientAttributNonAutorise(array $attributsAutorises): bool
    {
        // Récupère les attributs
        $balisesAvecAttributs = $this->getBalisesAvecAttributs();

        foreach ($balisesAvecAttributs as $balise => $attributs) {
            if (empty($attributs)) {
                continue;
            }

            $baliseAttributsAutorises = array_merge($attributsAutorises[''], isset($attributsAutorises[$balise]) ? $attributsAutorises[$balise] : []);

            foreach ($attributs as $attribut) {
                if (!\in_array($attribut, $baliseAttributsAutorises, true)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function getBalisesAvecAttributs(): array
    {
        $balisesAvecAttributs = [];
        $regexBalises = [];
        // On récupère toutes les balises HTML (par exemple `<a href="http://toto.fr/">Toto</a>`)
        preg_match_all("/<\s*([^>]*)>(.*?)<\s*\/\s*([^>]*)\s*>/i", $this->html, $regexBalises);

        foreach ($regexBalises[1] as $i => $regexBalise) {
            $baliseContenu = $regexBalises[0][$i];
            $attributs = [];
            // Pour chaque balise HTML, on récupère le nom de la balise et tous ses attributs
            preg_match_all("/(\S+)=[\"\']?((?:.(?![\"\']?\s+(?:\S+)=|\s*\/?[>\"\']))+.)[\"\']?/i", $baliseContenu, $attributs);

            $baliseNom = $regexBalises[3][$i];
            $balisesAvecAttributs[$baliseNom] = array_unique(array_merge(isset($balisesAvecAttributs[$baliseNom]) ? $balisesAvecAttributs[$baliseNom] : [], $attributs[1]));
        }

        return $balisesAvecAttributs;
    }
}
