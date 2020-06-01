# Devops

## Preface
This is a rewrite of a proprietary devops tool I wrote for a company. The goal is to remove a lot of the company-specific business logic while keeping the guts in-tact.

More details to follow.


### Why?
Because PHP is not as singular focused as some make it out to be. Symfony is an amazing framework, and using it as the foundation of a cli tool can allow PHP devs to do more with their code without having to resort to mixing languages to get the job done. If you write a PHP cli tool to help a PHP team, you've now enabled that team to help with maintaining the cli too.

### Should I use this?
For the foreseeable future, no. This is a working code sample for me. Maybe in the future I'll throw it out for more adoption.

## Usage
By default, this tool will use SQLite to manage releases and deploys. For production environments, it is recommended to use a better persistent data storage with redundancy and backups. Database configuration will be found in config/parameters.yaml.

### Installation
```bash
cp config/parameters.yaml.dist config/parameters.yaml
composer install
vendor/bin/doctrine orm:schema-tool:update --force --dump-sql
```

### Testing
```bash
vendor/bin/pest
# optinonally with XDEBUG 2.0+, or PCOV, or PHPDBG installed, generate coverage:
vendor/bin/pest --coverage --min=90
```

### Code Quality
Install [FriendsOfPHP/PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) to your path (recommended `brew install php-cs-fixer`).

```bash
php-cs-fixer fix --config=.php_cs.dist -v --dry-run
```
