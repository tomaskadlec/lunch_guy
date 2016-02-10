# d2s

A simple application for retrieving lunch menus of selected restaurants posting 
them directly on the web. Menus may be also sent to configured services. Supported
services are currently: 
  * [Slack](https://slack.com/)

## Installation

Download the project and run ``composer install`` from the project directory.

## Usage

A configuration must be provided in order to use the application. The configuration
is stored in ``app/config/parameters.yml``. Default values are stored in 
``app/config/parameters.yml.dist``.

### Configuration

The application requires two types of configuration - restaurants and output. Available
options can be found using an ``d2s:info`` command:

The following command prints all registered _parsers_ and _outputs_:
```
bin/console d2s:info 
```

Restaurant configuration consist of an _id_, _uri_ where menu can be found and type
of _parser_ to be used. Example configuration may look like:

```
d2s:
  restaurants:
    'U Pětníka':
      'uri': 'http://www.drest.cz/u-petnika'
      'parser': 'drest'
```

_Output_ cofiguration differs with each implementation. 

stdout:

```
d2s:
  output:
    stdout: yes
```

slack:

```
d2s:
  output:
    slack:
      uri: "uri_with_token"
      channel: "#test"
      username: "obědář"
      icon_emoji: ":stew:"
```

### Accessing the web

Web interface consists of a single page accessible as root resource of the application 
(e.g. ``http://server/`` if application runs in the webspace root or ``http://server/app/``
if the application runs at ``/app``).

Other resources are available for each configured restaurant but these are not supposed
for direct use (URL is ``/restaurantId``, attention ``restaurantID`` must be 
[URL encoded](https://en.wikipedia.org/wiki/Percent-encoding)).

### Using command line interface

Retrieving the menu of selected restaurant (or restaurants) and sending it to selected
service is accomplished through command line interaface of the application. Use the
``bin/console d2s:run`` command. Output may be selected using an ``--output`` option. 
Default is ``stdout``. Restaurants (their IDs) are given as arguments of the command.  
Multiple restaurant IDs may be given at the same time.

```
bin/console d2s:run  'U Topolů' 'U Pětníka' 'Na Slamníku'
```

## Contributing

Fork the project and then make pull request to [``develop``](https://github.com/tomaskadlec/d2s/tree/develop) branch.
Or in case of bugfixes against the [``release``](https://github.com/tomaskadlec/d2s/releases) that should be fixed.

Testing server running in a container can be used to verify your code without hassle. You just need to run `docker-compose up` and wait for the server to start listening at http://127.0.0.1:8000. Happy coding!

## Extending the application

### Writing custom parsers

TBD

### Writing custom outputs

TBD


