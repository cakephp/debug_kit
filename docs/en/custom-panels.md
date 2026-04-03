# Custom Panels

You can create custom DebugKit panels to expose application-specific debugging data.

## Creating a Panel Class

Place panel classes in `src/Panel`. The filename must match the class name. For example, `MyCustomPanel` should live in `src/Panel/MyCustomPanel.php`:

```php
namespace App\Panel;

use DebugKit\DebugPanel;

class MyCustomPanel extends DebugPanel
{
    // ...
}
```

Custom panels must extend `DebugPanel`.

## Callbacks

Panel objects hook into the current request through the `Controller.initialize` and `Controller.shutdown` events by default. If your panel needs additional events, implement `implementedEvents()` and return the full event map your panel requires.

The built-in panels are the best reference when you need examples.

## Panel Elements

Each panel should have a matching view element that renders the panel content. The element name is the underscored form of the class name:

* `SessionPanel` maps to `session_panel.php`
* `SqllogPanel` maps to `sqllog_panel.php`

Store panel elements in `templates/element`.

## Custom Titles and Elements

Panels usually infer their toolbar title and element name by convention. Override these methods when you need custom behavior:

* `title()` sets the label shown in the toolbar.
* `elementName()` sets the element name used for rendering.

## Panel Hook Methods

You can implement these methods to customize collection and rendering:

* `shutdown(EventInterface $event)` collects and prepares panel data, usually into `$this->_data`.
* `summary()` returns short summary text for the collapsed toolbar state.
* `data()` returns the serialized data exposed to the panel element.

## Panels in Other Plugins

Panels shipped by plugins work the same way as application panels, with one extra requirement: set `public string $plugin` to the plugin directory name so that the panel element can be resolved when rendering.

```php
namespace MyPlugin\Panel;

use DebugKit\DebugPanel;

class MyCustomPanel extends DebugPanel
{
    public string $plugin = 'MyPlugin';
}
```

To register application or plugin panels, update your DebugKit configuration:

```php
// In src/Application.php bootstrap()
Configure::write('DebugKit.panels', ['App', 'MyPlugin.MyCustom']);
$this->addPlugin('DebugKit', ['bootstrap' => true]);
```

This keeps the default panels enabled and adds `AppPanel` plus `MyCustomPanel` from `MyPlugin`.
