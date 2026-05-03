# Debug Kit

DebugKit est un plugin supporté par la core team qui fournit une toolbar et des outils de débogage avancés pour les applications CakePHP. Il permet d'inspecter rapidement la configuration, les logs, les requêtes SQL et les données de temps d'exécution.

::: warning
DebugKit est destiné uniquement aux environnements de développement local à utilisateur unique. Évitez de l'utiliser sur un environnement partagé, de préproduction ou tout autre environnement où les variables d'environnement et la configuration doivent rester privées.
:::

## Installation

Par défaut, DebugKit est installé avec le squelette d'application. Si vous l'avez retiré, réinstallez-le depuis le répertoire racine de l'application :

```bash
php composer.phar require --dev cakephp/debug_kit:"^5.0"
```

Chargez ensuite le plugin :

```bash
bin/cake plugin load DebugKit --only-debug
```

## Configuration

* `DebugKit.ignoreAuthorization` doit être défini à `true` si vous souhaitez ignorer le plugin Cake Authorization pour les requêtes DebugKit.

## Stockage de DebugKit

Par défaut, DebugKit utilise une base SQLite dans le répertoire `tmp` de l'application pour stocker les données des panneaux. Si vous souhaitez utiliser une autre base, définissez une connexion `debug_kit` dans `config/app.php`.

## Utilisation de la Toolbar

La toolbar DebugKit comprend plusieurs panneaux accessibles depuis l'icône CakePHP en bas à droite du navigateur.

Chaque panneau inspecte un aspect différent de l'application :

* **Cache** montre l'utilisation du cache et permet de le vider.
* **Environment** affiche les variables d'environnement liées à PHP et CakePHP.
* **History** affiche la liste des requêtes précédentes et permet de recharger leurs données.
* **Include** montre les fichiers inclus par type.
* **Log** affiche les écritures de log de la requête.
* **Packages** affiche les dépendances installées et les versions obsolètes.
* **Mail** affiche les emails envoyés pendant la requête.
* **Request** affiche les informations de requête, de route et les cookies.
* **Session** affiche le contenu de la session.
* **Sql Logs** affiche les logs SQL pour chaque connexion.
* **Timer** affiche les timers créés avec `DebugKit\\DebugTimer` ainsi que l'usage mémoire via `DebugKit\\DebugMemory`.
* **Variables** affiche les variables de vue définies par le contrôleur.

## Utiliser le panneau History

Le panneau History permet de consulter les données de requêtes précédentes, y compris après une erreur ou une redirection.

![Capture d'écran du panneau History](/history-panel.png)

Lorsque des données historiques sont chargées, les titres des panneaux changent pour indiquer que vous ne regardez plus la requête active.

<video controls preload="metadata" src="/history-panel-use.mp4"></video>

## Utiliser le panneau Mail

Le panneau Mail permet de suivre tous les emails envoyés pendant une requête.

<video controls preload="metadata" src="/mail-panel.mp4"></video>

## Développer vos propres panneaux

Vous pouvez créer vos propres panneaux personnalisés pour DebugKit.

### Créer une classe de panneau

Les classes doivent être placées dans `src/Panel` et étendre `DebugKit\\DebugPanel` :

```php
namespace App\Panel;

use DebugKit\DebugPanel;

class MyCustomPanel extends DebugPanel
{
    // ...
}
```

### Callbacks

Par défaut, les panneaux s'abonnent aux événements `Controller.initialize` et `Controller.shutdown`. Si vous avez besoin d'autres événements, implémentez `implementedEvents()`.

### Éléments de panneau

Chaque panneau s'appuie sur un élément de vue. Le nom suit la convention underscore de la classe, par exemple `SessionPanel` devient `session_panel.php`.

### Titres et éléments personnalisés

Vous pouvez redéfinir :

* `title()` pour configurer le titre de la toolbar.
* `elementName()` pour choisir l'élément utilisé.

### Méthodes de hook

Les méthodes suivantes permettent de personnaliser le comportement du panneau :

* `shutdown(Event $event)` collecte les données.
* `summary()` retourne un résumé affiché dans la toolbar réduite.
* `data()` retourne des données sérialisables pour l'élément.

### Panneaux dans d'autres plugins

Pour un panneau fourni par un plugin, définissez `public $plugin` avec le nom du répertoire du plugin :

```php
namespace MyPlugin\Panel;

use DebugKit\DebugPanel;

class MyCustomPanel extends DebugPanel
{
    public string $plugin = 'MyPlugin';
}
```

Pour enregistrer un panneau applicatif ou plugin :

```php
Configure::write('DebugKit.panels', ['App', 'MyPlugin.MyCustom']);
$this->addPlugin('DebugKit', ['bootstrap' => true]);
```
