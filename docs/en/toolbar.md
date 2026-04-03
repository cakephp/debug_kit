# Toolbar Usage

The DebugKit toolbar appears after you click the CakePHP icon in the lower-right corner of the browser. Each panel combines a panel class with a view element and focuses on a specific kind of runtime information.

Built-in panels include:

* **Cache** shows cache usage during the request and lets you clear caches.
* **Environment** shows PHP and CakePHP environment details.
* **History** lists previous requests and lets you inspect their panel data.
* **Include** groups included files by type.
* **Log** shows log entries created during the request.
* **Packages** lists installed dependencies, their versions, and outdated packages.
* **Mail** captures mail sent during the request and supports previews.
* **Request** shows request data, route information, cookies, and request parameters.
* **Session** displays the active session contents.
* **Sql Logs** shows SQL logs for each datasource.
* **Timer** displays timers from `DebugKit\DebugTimer` and memory readings from `DebugKit\DebugMemory`.
* **Variables** shows view variables set in the controller.
* **Deprecations** renders deprecation warnings in a less disruptive format.

You can use the built-in panels as-is or register your own custom panels alongside them.
