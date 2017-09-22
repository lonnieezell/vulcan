# Vulcan

Vulcan is a set of command line interface tools for CodeIgniter 4 that help you rapidly create boilerplate code, bypassing some of the developer's drudge work, and get on with the good parts.

**This project is in the early development stages is not currently fit for consumption. Keep checking in, though, since it's sure to launch on or before CodeIgniter 4 does.**

## Features

- Integrates seamlessly into a CodeIgniter 4 project
- One install can be used across any number of CodeIgniter 4 projects
- Create as many custom libraries of generators to keep work and hobby libraries separate
- Generate boilerplate code you don't like writing every time
- Rapidly generate test data sets (coming soon)
- Interactive REPL environment to test out features. (coming soon)
- and likely more!

##Installation

**Download the Code**

Until the project gets closer to completion, there is no packagist.com integration or other slick way to install. Just keep it simple for now and clone the repo to somewhere you'll find it again:

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

Also we need to do a symlink of the vulcan dependencies (this is provisional):

```
rm -rf vendor
ln -s /home/vulcan/vendor .
```

Remember to give it the actual path on your drive, not mine.

Now can we use it? You bet!

**Get Creating**

Using CodeIgniter 4's built-in cli tools you can call any of the Vulcan commands now:

```
$ php spark
```

This will provide a list of commands and a short explanation.
