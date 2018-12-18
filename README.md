# Project architect: Mortar
Mortar is the template engine in Architect.
It provides a simple syntax to perform actions such as including a file, getting a \_GET variable or just displaying stuff, with very short instructions.

_it's not obligatory but strongly recommended that you put your mortar directory outside of the user's reach, and use custom directories for anything public. .htaccess files are provided but may not be enough!_

## Change log
#### _Changes listed in no particular order_

* __V0.1__ _(initial version)_
    * Autoloader
    * Singleton core
    * Http routing

* __V0.2__
    * Middleware and controller support
    * Csrf protection
    * Customizable settings

* __V0.3__
    * Dynamic template parsing
    * Customizable parser tags
    * High performance modular http routing

## Todo
* ✅ Refactor objects to route into a core component for better dependancy management
* ✅ Add a dynamic flexible way to add functions to the template parser
* ✅ Rework the router into a contextuable object to avoid reinstanciating too much