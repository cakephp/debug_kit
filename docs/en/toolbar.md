# Toolbar Usage

The DebugKit toolbar appears after you click the CakePHP icon in the lower-right corner of the browser. Each panel combines a panel class with a view element and focuses on a specific kind of runtime information.

Built-in panels include:

* **Cache** shows cache usage during the request and lets you clear caches.
* **Deprecations** renders deprecation warnings in a less disruptive format.
* **Environment** shows PHP and CakePHP environment details.
* **History** lists previous requests and lets you inspect their panel data.
* **Log** shows log entries created during the request.
* **Mail** captures mail sent during the request and supports previews.
* **Packages** lists installed dependencies, their versions, and outdated packages.
* **Plugins** lists the plugins loaded by the application.
* **Request** shows request data, route information, cookies, and request parameters.
* **Routes** lists the routes matched during the request.
* **Sql Log** shows SQL logs for each datasource.
* **Timer** displays timers from `DebugKit\DebugTimer` and memory readings from `DebugKit\DebugMemory`.
* **Variables** shows view variables set in the controller.

The deprecated **Include** and **Session** panels still exist but are disabled by default. Use the Environment panel instead of Include, and the Request panel instead of Session.

You can use the built-in panels as-is or register your own custom panels alongside them.
