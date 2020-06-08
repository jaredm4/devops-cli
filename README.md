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
composer install
# setup database schema, using SQLite by default (see config/parameters.yaml)
vendor/bin/doctrine orm:schema-tool:update --force --dump-sql
```

### Basic Usage
To see all available commands to you, use `./devops list`. To narrow it down to a specific namespace, `./devops list release`.

#### Releases
A Release is a snapshot of your application at the time the Release was created. It maps the Git SHA1s of all your code repositories to a single entity (release). Usually, this is the latest version of the master branch of all those repositories.

Releases can also point to a specific branch name instead of master. This is useful for having a deployable Release for QA or Dev purposes only. If the branch name does not exist on a repository, it will default to master. If the branch isn't found on any repository, it should throw an error.

Metadata for Releases can contain properties like:
* "Master" if a Release is only using master branches. A Release that is not a Master can never go to Production.
* "Releasable" if a Release has been tested and vetted for deployment to Production. Meaning all tests (automated and UAT) have passed.

Create a Release with `./devops release:create`. List the latest releases with `./devops release:list`.

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
