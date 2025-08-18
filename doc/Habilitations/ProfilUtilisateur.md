# Le profil utilisateur

## L'utilisation

Après la connexion, l'utilisateur se connecte à un profil utilisateur. S'il en possède plusieurs, il devra en choisir un avant de continuer sur l'application.

Le service `ProfilUtilisateurChoisi` permet de récupérer l'ensemble des informations relatives au profil que l'utilisateur a choisi, notamment :

* Son `ProfilUtilisateur` qui permet notamment de récupérer ses périmètres,
* Son `Profil` - lié au profil utilisateur - qui permet notamment de récupérer ses fonctionnalités,
* Sa ressource globale.


## Le cas du super-administrateur

Il n'existe pas de profil `Super-administrateur`. Si l'utilisateur est connecté comme tel, il ne possède donc ni `ProfilUtilisateur` ni `Profil` mais il possède un ressource globale.

La méthode `estSuperAdmin()` permet de savoir si l'utilisateur est connecté comme tel.
