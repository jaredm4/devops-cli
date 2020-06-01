# Devops

## Preface
This is a rewrite of a proprietary devops tool I wrote for a company. The goal is to remove a lot of the company-specific business logic while keeping the guts in-tact.

More details to follow.


### Why?
Because PHP is not as singular focused as some make it out to be. Symfony is an amazing framework, and using it as the foundation of a cli tool can allow PHP devs to do more with their code without having to resort to mixing languages to get the job done. If you write a PHP cli tool to help a PHP team, you've now enabled that team to help with maintaining the cli too.

### Should I use this?
For the foreseeable future, no. This is a working code sample for me. Maybe in the future I'll throw it out for more adoption.

## Usage

### Installation
```bash
composer install
```

### Testing
```bash
vendor/bin/phpunit tests
```

### Code Quality
Install [FriendsOfPHP/PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) to your path (recommended `brew install php-cs-fixer`).

```bash
php-cs-fixer fix --config=.php_cs.dist -v --dry-run
```
