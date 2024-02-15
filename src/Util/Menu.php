<?php

namespace App\Util;

use App\Entity\Utilisateur;
use Symfony\Component\Routing\Router;

class Menu
{
    public static function getMenu(Utilisateur $utilisateur, $securityAuthorizationChecker, $router): array
    {
        $acceuil = [
            'libelle' => 'Accueil',
            'icone' => 'fa-home',
            'href' => 'bac_homepage',
            'href_params' => [],
            'routes' => ['bac_homepage'],
            'active' => false,
            'sousMenu' => null,
        ];

        $messagerie = [
            'libelle' => 'Messagerie',
            'icone' => 'fa-envelope',
            'href' => 'messagerie',
            'href_params' => [],
            'routes' => ['messagerie', 'show_message', 'message_favoris', 'message_corbeille'],
            'active' => false,
            'sousMenu' => null,
        ];



        $utilisateurs = [
            'libelle' => 'Utilisateurs',
            'icone' => 'fa-group',
            'href' => 'utilisateur',
            'href_params' => [],
            'routes' => [
                'utilisateur',
                'utilisateur_show',
                'utilisateur_new',
                'utilisateur_create',
                'utilisateur_edit',
                'utilisateur_update'
            ],
            'active' => false,
            'sousMenu' => null,
        ];



        $annuaire = [
            'libelle' => 'Annuaire',
            'icone' => 'fa-tty',
            'href' => 'annuaire_index',
            'href_params' => [],
            'routes' => ['annuaire_index'],
            'active' => false,
            'sousMenu' => null,
        ];

        $cap = [
            'libelle' => 'CAP',
            'icone' => 'fa-file-text',
            'href' => 'cap',
            'href_params' => [],
            'routes' => ['cap'],
            'active' => false,
            'sousMenu' => null,
        ];

        $documentsComplementaire = [
            'libelle' => 'Autres documents',
            'icone' => 'fa-file-text',
            'href' => 'documents_complementaire',
            'href_params' => [],
            'routes' => ['documents_complementaire'],
            'active' => false,
            'sousMenu' => null,
        ];

        $texteDeReference = [
            'libelle' => 'Textes de Référence',
            'icone' => 'fa-file-text',
            'href' => 'texte_reference',
            'href_params' => [],
            'routes' => ['texte_reference'],
            'active' => false,
            'sousMenu' => null,
        ];

        $baseConnaissance = [
            'libelle' => 'Base connaissance',
            'icone' => 'fa-book',
            'routes' => [],
            'active' => false,
            'sousMenu' => [$texteDeReference/* , $circulaires, $regleDeGestion */, $documentsComplementaire, $cap],
        ];

        $faq = [
            'libelle' => 'FAQ',
            'icone' => 'fa-comments-o',
            'href' => 'faq',
            'href_params' => [],
            'routes' => ['faq'],
            'active' => false,
            'sousMenu' => null,
        ];

        $guideUtilisateur = [
            'libelle' => 'Guides utilisateurs',
            'icone' => 'fa-book',
            'href' => 'guide_utilisateur',
            'href_params' => [],
            'routes' => ['guide_utilisateur'],
            'active' => false,
            'sousMenu' => null,
        ];

        $baseDocumentaire = [
            'libelle' => 'Base documentaire',
            'icone' => 'fa-book',
            'href' => 'bac_homepage',
            'href_params' => [],
            'routes' => [''],
            'active' => false,
            'sousMenu' => [$baseConnaissance, $faq, $guideUtilisateur],
        ];



        $statistiques = [
            'libelle' => 'Statistiques',
            'icone' => 'fa-area-chart',
            'href' => 'statistiques_generales',
            'href_params' => [],
            'routes' => ['statistiques_generales'],
            'active' => false,
            'sousMenu' => null,
        ];


        $ingres = [
            'libelle' => 'Ingres',
            'icone' => 'fa-tag',
            'href' => 'ingres_index',
            'href_params' => [],
            'routes' => self::getRoutesWithPrefix($router, 'ingres_'),
            'active' => false,
            'sousMenu' => null,
        ];


        $traces = [
            'libelle' => 'Traces',
            'icone' => 'fa-bug',
            'href' => 'trace_index',
            'href_params' => [],
            'routes' => self::getRoutesWithPrefix($router, 'traces_'),
            'active' => false,
            'sousMenu' => null,
        ];

        $mailtest = [
            'libelle' => 'Testeur de mail',
            'icone' => 'fa-envelope',
            'href' => 'mail_test',
            'href_params' => [],
            'routes' => ['mail_test'],
            'active' => false,
            'sousMenu' => null,
        ];

        $parametres = [
            'libelle' => 'Paramètres',
            'icone' => 'fa-cogs',
            'href' => 'parametre_index',
            'href_params' => [],
            'routes' => ['parametre_index'],
            'active' => false,
            'sousMenu' => null,
        ];

        // Constitution du menu en fonction des roles de l'utilisateur

        $menu = [];
        $menu[] = $acceuil;

        $menu[] = $messagerie;
        $menu[] = $annuaire;
        $menu[] = $baseDocumentaire;


        if ($securityAuthorizationChecker->isGranted('ROLE_DGAFP')) {
            $menu[] = $utilisateurs;
        } else {
            $menu[] = $statistiques;
        }

        if ($securityAuthorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu[] = $traces;
            $menu[] = $mailtest;
            $menu[] = $parametres;
        }

        if ($utilisateur->hasRole('ROLE_BT')) {
            $menu = [];
            $menu[] = $acceuil;
            $menu[] = $messagerie;
            $menu[] = $annuaire;
            $menu[] = $baseDocumentaire;
        }

        // $menu[] = $notifications;

//     	if($securityAuthorizationChecker->isGranted('ROLE_ADMIN')){
//     		$menu[] = $utilisateurs;
//     	}

        return $menu;
    }

    public static function setActiveMenu($route, &$menu)
    {
        foreach ($menu as &$item) {
            self::calculItemActif($route, $item);
        }

        return $menu;
    }

    private static function calculItemActif($route, &$menu): bool
    {
        if (null == $menu['sousMenu']) {
            $menu['active'] = in_array($route, $menu['routes']);

            return $menu['active'];
        } else {
            $is_active = false;

            foreach ($menu['sousMenu'] as &$item) {
                $is_active = $is_active || self::calculItemActif($route, $item);
            }
            $menu['active'] = $is_active;

            return $is_active;
        }
    }

    public static function getPathMenu($menu, &$breadCrumb = [])
    {
        foreach ($menu as $item) {
            if (true === $item['active']) {
                if (0 != strcmp($item['libelle'], 'Accueil')) {
                    $route = $item['routes'][0] ?? null;

                    $breadCrumb[] = ['libelle' => $item['libelle'], 'route' => $route];
                }
                if (null !== $item['sousMenu']) {
                    Menu::getPathMenu($item['sousMenu'], $breadCrumb);
                }
            }
        }

        return $breadCrumb;
    }

    private static function getRoutes(Router $router, $suffixe): array
    {
        $routes = [];

        $allRoutes = $router->getRouteCollection()->all();

        foreach ($allRoutes as $nom_route => $objet_route) {
            $tab_route = explode('_', $nom_route);

            if (end($tab_route) == $suffixe) {
                $routes[] = $nom_route;
            }
        }

        return $routes;
    }

    private static function getRoutesWithPrefix(Router $router, $prefix): array
    {
        $routes = [];

        $allRoutes = $router->getRouteCollection()->all();

        foreach ($allRoutes as $nom_route => $objet_route) {
            if (Util::str_starts_with($nom_route, $prefix)) {
                $routes[] = $nom_route;
            }
        }

        return $routes;
    }
}
