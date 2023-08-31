A CLI Git Repository manager.

***FYI:***
***This is still a work in progress, unit testing, static analysis etc are not complete and this brief guide may change.***

## Usage
Download the Git Destroyer codebase or use Composer `composer create-project danc0/git-destroyer` and create an alias to `src/git-destroyer`. This will allow you to use this file in the CLI with an alias instead of having to type `php [PATH TO INSTALL]/src/git-destroyer`. Examples going forward will assume an alias of `git-destroyer` is set up.


## Basics

Git Destroyer offers robust help menu options `git-destroyer help` will show the available commands as well as some package info. The commands are shown below.

```txt
|Command           |Description                                               |
|------------------|----------------------------------------------------------|
|init              |Initialize a project                                      |
|clone             |Clone the remote repository                               |
|new-branch        |Create a new branch                                       |
|update            |Switch to a new branch                                    |
|commit <message>  |Commit your changes                                       |
|staging-push      |Merge your changes into the staging branch and push them  |
|live-push         |Merge your changes into the live branch and push them     |
|push              |Push your changes to the remote                           |
|script <name>     |Run a script from the config                              |
|version           |Prints the version information for git-destroyer          |
```

Each of these commands also have their own help menus you can access using `--help` for example `git-destroyer commit --help`

```txt
Command: commit <message>
About: Commit your changes
Usage:

|Arg              |Alias       |Description                               |Required  |Is Flag  |
|-----------------|------------|------------------------------------------|----------|---------|
|--add-all        |-a          |Add all files to the commit               |False     |True     |
|--files=[VALUE]  |-f=[VALUE]  |CSV string of files to add to the commit  |False     |False    |
|--local-only     |-l          |Only commit locally, do not push          |False     |True     |
```

This shows you the usage, available flags, available options, and if they are required.

## Getting Started

Run `git-destroyer init` to create a new Git Destroyer config for your project. This will create both a config file and a hooks file.

## Hooks File

The hooks file is a JSON file that allows you to customize the run time of Git Destroyer.

```json
{
    "new_branch": {
        "pre": [],
        "post": []
    },
    "commit": {
        "pre": [],
        "post": []
    },
    "staging": {
        "pre": [],
        "post": []
    },
    "live": {
        "pre": [],
        "post": []
    },
    "scripts": {
        "example": "echo \"hello world\""
    }
}
```
The `pre` and `post` arrays should be strings of bash commands you wish to run at those times. The `staging` and `live` keys are for merging code into your staging or production branch. The `scripts` section should be an object so you can call them using `git-destroyer script example`.

## Dev Environment Notes:
Need to create `src/stan.php` for PHPStan to find constants, this file should look like this:
```php
<?php
define('ROOT', getcwd());
define('APP_ROOT', __DIR__);
```

If you try to work on this and don't add that PHPStan will yell at you.