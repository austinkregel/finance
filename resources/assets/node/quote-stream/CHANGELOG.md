## CHANGELOG

### Verson 0.3.0

- Switched to pulling quotes via https *(port 443)* from Google.
- Upgraded npm dependencies.
- Supports CORS.
- Added `package.json` and remove `node_modules` from being checked in.
- License updated to Apache 2.0.

### Version 0.2.1
- Fixed a bug when not providing a trailing slash after the ticker symbol. https://github.com/nodesocket/quote-stream/issues/1
- Slight update to the documentation to reflect a trailing slash after the ticker symbol is not required.

### Version 0.2.0
- Fixed a bug where other ticker requests would stream in. For example, if a client requested GOOG, and then another client connected requesting MSFT, the first connected client would see MSFT stream in.

- Changed `index.html` to request the socket.io javascript file, and connect on `localhost`, instead of hard-coding the NodeSocket demo machine.

### Version 0.1.1
- Added `clearInterval()` on `socket.disconnect()`

### Version 0.1.0
- Initial release
