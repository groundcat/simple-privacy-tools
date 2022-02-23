# Simple Privacy Tools

Just some dead simple tools for personal privacy protection.

## URL Cleaner

A web UI for clean the tracking elements in URLs.

Web UI path: `/url-cleaner`

API path: `/url-cleaner/api.php`

Usage:

- Deploy with PHP 7+
- Run `runes_updater.php` to fetch and update rules
- Set up a cron job for `runes_updater.php` if necessary
- Go to `https://<SERVICE_DOMAIN>/url-cleaner` to use


## Email Forwarder

A web UI for quickly adding email alias using the forwardemail.net API.

Web UI path: `/forwarder`

Usage：

- Deploy with PHP 7+
- Rename `config-example.php` to `config.php`
- Update `config.php` with API tokens and other configurations
- Go to `https://<SERVICE_DOMAIN>/forwarder?alias_domain=aliasdomain.com&email_des=destination@example.com`

