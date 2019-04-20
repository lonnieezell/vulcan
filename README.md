# Vulcan

Vulcan is a set of command line interface tools for CodeIgniter 4 that help you rapidly create boilerplate code, bypassing some of the developer's drudge work, and get on with the good parts.

**This project is in the early development stages is not currently fit for consumption. Keep checking in, though, since it's sure to launch on or before CodeIgniter 4 does.**

## Features

- Integrates seamlessly into a CodeIgniter 4 project
- Generate boilerplate code you don't like writing every time
- Interactive REPL environment to test out features.
- and likely more!

##Installation

**Download the Code**

### Composer

The preferred method to install is via Composer. Add the following to the `require-dev` section of your project's 
composer.json file: 

```
"lonnieezell/vulcan": "dev-develop"
```

### Manual Installation

Clone the repo to somewhere you'll find it again:

```
$ git clone git@github.com:lonnieezell/vulcan.git
```

For this example, we'll pretend we're installing it to `/home/vulcan`, mainly to keep the typing minimal.

Next, install the dependencies with composer:

```
cd /home/vulcan
composer install
```

Great! I can use it now, right? Not so fast...

**Let Your CodeIgniter Project Know**

We need to let your project know where to find the commands, so open up `/application/Config/Autoload.php` and create the `Vulcan` namespace in the `$psr4` array:

```
public $psr4 = [
    'Vulcan' => '/home/vulcan'
];
```

Now can we use it? You bet!

**Get Creating**

Using CodeIgniter 4's built-in cli tools you can call any of the Vulcan commands now:

```
$ php spark
```

This will provide a list of commands and a short explanation.

## Using the Interactive Debugger

Vulcan packages the excellent [PsySH](https://psysh.org/) repl and interactive debugger. Be sure to use read its 
manual to see all of the great things it can do.
