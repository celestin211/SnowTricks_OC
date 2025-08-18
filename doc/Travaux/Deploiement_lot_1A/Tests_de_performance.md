# Déploiement du lot 1A : Tests de performance

## Compte-rendu de l'atelier atelier du 13/06/2022

Des tests de performance doivent être réalisés pour l'application SICARDI.
Ces tests font partie du processus de mise en production mais les délais ne permettront pas de les réaliser avant la MEP prévue début juillet.
Le nombre d'utilisateurs est estimé à moins de cinq pour la mise en production, ce faible nombre écarte tous risques liés à des problèmes d'infrastructure.

* Ticket ITOP : R-024544
* Environnement de réalisation des tests : FOR1
* Connexion : User / Mot de passe sans certificat (A priori, actuellement la connexion se fait via un certificat)
* Utilisateur de test déjà créé
* Infos et scénario : Contenu dans le powerpoint en piece jointe du ticket ITOP
* Nombre d'utilisateurs concurrents :  100 utilisateurs max
 
Scénario sensibles :  
* Requêtes libres
* Transaction au niveau du dossier agent dans la partie affectation pour l'ajout de métier

Suite à la relecture du powerpoint et à un échange sur les tests envisagés, Hervé indique ses besoins et remarques dans le ticket ITOP et Rémi mettra à jour le powerpoint dans ce ticket.

Planning :
* 30/06/2022 : Point d'étape
* 08/07/2022 : Réalisation du test de performance continue afin de surveiller les temps de réponse de l'application
* 31/07/2022 : Réalisation des tests de charge
