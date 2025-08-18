# Résolutions de problèmes liés aux tests

## Erreur cUrl dès le démarrage

Si une erreur de ce genre est rencontrée :

```
Facebook\WebDriver\Exception\WebDriverCurlException: Curl error thrown for http POST to /session with params: {"capabilities":{"firstMatch":[{"browserName":"firefox","platformName":"windows","moz:firefoxOptions":{"prefs":{"reader.parse-on-load.enabled":false,"devtools.jsonview.enabled":false},"binary":"C:\\Program Files\\Mozilla Firefox\\firefox.exe"}}]},"desiredCapabilities":{"browserName":"firefox","platform":"WINDOWS","moz:firefoxOptions":{"prefs":{"reader.parse-on-load.enabled":false,"devtools.jsonview.enabled":false},"binary":"C:\\Program Files\\Mozilla Firefox\\firefox.exe"}}}

Operation timed out after 30012 milliseconds with 0 bytes received
```

Cela semble être dû au chargement dès l'ouverture du navigateur de l'URL `monalize.alize` (sans doute à cause de pages appelées qui ne répondent pas comme l'URL piwik).

Les solutions possibles sont les suivantes :

### Modifier la configuration de démarrage par défaut de Firefox

* Ouvrir `C:\Program Files\Mozilla Firefox\mozilla.cfg` ;
* Remplacer 
```
defaultPref("browser.startup.homepage", "data:text/plain,browser.startup.homepage=http://monalize.alize");
```
par
```
defaultPref("browser.startup.homepage", "about:blank");
```

La ligne suivante peut également être modifiée :
```
lockPref("browser.startup.page", 1);
```
par
```
lockPref("browser.startup.page", 0);
```
pour ne plus ouvrir d'URL par défaut.

Mais cela nécessite des droits administrateurs.

### Changer de page dès le démarrage

Dès le démarrage du navigateur, le site monalize va se charger.
Il suffit juste de changer d'URL (par exemple `wikipedia.org`) et les tests se lanceront ensuite normalement.
