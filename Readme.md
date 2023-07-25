# PHP Version Manager
PHP Version Manager is a CLI tool designed to make it easier to manage multiple PHP versions on your system. As a developer, you often find yourself working with multiple projects, each requiring a different PHP version. This can be a bit frustrating as your system only allows one PHP version in the system path at a time. Changing the PHP version every time you need to work with a different project can become cumbersome. PHP Version Manager simplifies this process by enabling each project to have its own PHP version in the CLI.

## Installation
To install PHP Version Manager, you need to have Composer installed on your system. Once you have Composer, you can install PHP Version Manager globally by running the following command:

```bash
composer global require bluedot/php-version-manager
```

## Usage
To use PHP Version Manager, navigate to the root directory of your project in the CLI and run the `phpver` command. The tool will scan your project's composer.json file to determine the required PHP version.

If a composer.json file is not found, or if the file does not specify a PHP version, PHP Version Manager will prompt you to select a PHP version from the versions available on your system.

Here is how you use the phpver command:

```bash
cd /path/to/your/project
phpver
```
The first time you run the command, PHP Version Manager will ask for the directory where your PHP versions are stored. The tool will remember this directory for future use.

Once the command has run successfully, a `php.cmd` and `php` file will be created in your project's root directory. This file sets the PHP version for the CLI in that directory to the version determined by PHP Version Manager. This means that any PHP command you run in that directory will use the correct PHP version for your project.

## Contributing
Contributions are welcome! Please feel free to submit a pull request or open an issue on GitHub.

## License
PHP Version Manager is open-source software licensed under the MIT license.