# La création d'un service d'import ou d'export

Pour créer un nouveau service d'import, il suffit de créer :
* un `FichierImporter` (étendant `AbstractFichierImporter`) ;
* autant de `FeuilleImporter` (étendant `AbstractFeuilleImporter`) que le fichier comporte de feuilles ;
* autant de `Feuille` (étendant `AbstractFeuille`) que de `FeuilleImporter`.

Ensuite, pour chacune des classes, il faut définir le corps de la ou des méthodes abstraites.

Le processus est identique pour l'export et les `Feuille` sont communes aux imports et exports.
