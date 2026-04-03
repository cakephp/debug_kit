# API Requests

If your application is API-only and does not render a frontend, you can still inspect DebugKit toolbar data directly.

Open the toolbar endpoint with the request's DebugKit ID:

```text
http://localhost/debug-kit/toolbar/<debugkit-id>
```

The `<debugkit-id>` value is returned in the response headers:

```text
X-DEBUGKIT-ID: 5ef39604-ad5d-4ca4-85d8-8595e52373bb
```

For example:

```text
http://localhost/debug-kit/toolbar/5ef39604-ad5d-4ca4-85d8-8595e52373bb
```
