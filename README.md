# Magento2 cache hosts Manager

Allows to manage your cache hosts via the CLI. You can add for instance Varnish servers and remove them again in the configuration.

## Status
I believe this package is stable. Yet I want to warn you about using it in production: 

- It has not been used intensively by the community
- Magento core changes often - I cannot guarenty this package will work on the next update.
- Make sure you have the correct backups of your configurations. 

When you are in need of a tool like this, I assume you have somewhat decent knowledge the make the right calls ;)

## Why
Because the default CLI setting `(config:set --http-cache-hosts)` is not usefull. 
The config:set commands overrides *all* your hosts. Imagine a dynamic setup where you add a new server. The configuration (env.php) is shared via
a NFS (or whatever volume). You must know *all* hosts if you want to add your new host.

With this manager you can simply do `bin/magento cachehosts:add {currenthost}` and it will append the server, rather than overriding all your servers

## About me
I'm a DevOps engineer for a full service digital agency in the Netherlands. When possible I try to create opensource
scripts / extentions and tools. If you appriciate my work, please be so kind to donate so I can keep drinking beer.


[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UDG2ZGDZ9TMEE)

## How the CLI works
There are 2 commands:

` bin/magento cachehosts:add`

` bin/magento cachehosts:remove`

Arguments are the host(s), comma seperated


### CLI Usage

Add
```bash
// Using default port
bin/magento cachehosts:add 127.0.0.1
// Adding multiple hosts
bin/magento cachehosts:add 127.0.0.1,127.0.0.2
// Adding with port
bin/magento cachehosts:add 127.0.0.1:1337
```

Remove
```bash
// Using default port
bin/magento cachehosts:remove 127.0.0.1
// Removing multiple hosts
bin/magento cachehosts:remove 127.0.0.1,127.0.0.2
// Removing with port
bin/magento cachehosts:remove 127.0.0.1:1337
```

Mixing up
```bash
bin/magento cachehosts:remove 127.0.0.1:1337,127.0.0.1
```

It is ok to have the same host on different ports.

## Install with Composer 

```bash
composer require webfixit/cachehostsmanager
```
