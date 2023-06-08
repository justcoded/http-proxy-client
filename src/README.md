<a href="https://supportukrainenow.org/"><img src="https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner-direct.svg" width="100%"></a>

------

# Webhook Proxy Client

The Webhook Proxy Client is built to forward webhooks from the Webhook Proxy Server to your local environment. It
listens to the Webhook Proxy Server via WebSocket and forwards the webhooks to your local environment via TCP/HTTP.

## Quickstart

After the installation, run the application with  the `--help` or `-h` flag to discover available options:

```bash
$ whp -h
```

Also run it for the `customize` command:

```bash
$ whp customize -h
```

**OK BYE ✋**

---

## Table of Contents

<!-- TOC -->
* [Webhook Proxy Client](#webhook-proxy-client)
  * [Quickstart](#quickstart)
  * [Table of Contents](#table-of-contents)
  * [Installing the binary](#installing-the-binary)
    * [Prerequisites](#prerequisites)
    * [Installation](#installation)
  * [CLI](#cli)
    * [Forwarding webhooks](#forwarding-webhooks)
      * [Options](#options)
    * [Customization](#customization)
    * [Examples](#examples)
<!-- TOC -->

---

## Installing the binary

### Prerequisites

The program requires PHP 8.1+ to be installed on the system.

### Installation

[TBW]

## CLI

### Forwarding webhooks

The CLI is available via the `whp` command.

The example syntax is:

```bash
$ whp --channel=c3fc8456-b1f6-4fd5-9b9a-736f01160c60 --forward-url=https://your-forward-url.com:8888/v1/webhooks/local-channel-id
```

#### Options

| Option          | Description                                                                                                                                                                                                                                                                                                                                                                            |
|-----------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `--channel`     | The channel ID to listen for webhooks. <br/>Can be either a UUID or a link to the channel. <br/>In case it's a link, the domain/port setting will be overwritten from the default ones. <br/> The default domain is `request-proxy.dev-net.co`<br/> Example values: `c3fc8456-b1f6-4fd5-9b9a-736f01160c60`, `https://request-proxy.dev-net.co/ch/c3fc8456-b1f6-4fd5-9b9a-736f01160c60` |
| `--forward-url` | The URL to forward the webhooks to. <br/>Example value: `https://your-forward-url.com:8888/v1/webhooks/local-channel-id`                                                                                                                                                                                                                                                               |

***NOTE:** these options are not strictly required to start the program. If some of them are not provided, the CLI will
ask for them.*

### Customization

The CLI can be customized via the `whp customize` command.

```bash
$ whp customize {?config} {?value}
```

If no arguments are provided it just generates the .env file that can be modified manually.
If the `config` argument is provided, it sets the `value` and rewrites the `.env` file.
The `.env` file itself can be edited manually either way.

| Config                  | Dotenv Equivalent           | Possible values                                                                                |
|-------------------------|-----------------------------|------------------------------------------------------------------------------------------------|
| timezone                | APP_TIMEZONE                | Any [supported timezone](https://www.php.net/manual/en/timezones.php) string. Default is `UTC` |
| socket.timeout          | WHP_SOCKET_TIMEOUT          | Any positive integer greater then 0. Default is `20`                                           |
| socket.self_signed_ssl  | WHP_SOCKET_SELF_SIGNED_SSL  | `true` or `false`. Default is `false`                                                          |
| socket.protocol_version | WHP_SOCKET_PROTOCOL_VERSION | Any positive integer greater then 0. Default is `7`                                            |
| socket.client_name      | WHP_SOCKET_CLIENT_NAME      | Any string. Default is `js`                                                                    |
| socket.version          | WHP_SOCKET_VERSION          | Any string. Default is `4.4.0`                                                                 |
| socket.flash            | WHP_SOCKET_FLASH            | `true` or `false`. Default is `false`                                                          |

### Examples

```bash
$ whp customize timezone Europe/Kiev
```

```bash
$ whp customize socket.timeout 30
```

```bash
$ whp customize socket.self_signed_ssl true
```

 ```bash
$ whp customize socket.protocol_version 8
```

```bash
$ whp customize socket.client_name my-client
```

 ```bash
$ whp customize socket.version 4.5.0
```

```bash
$ whp customize socket.flash true
```

