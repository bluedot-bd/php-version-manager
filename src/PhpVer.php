<?php
namespace Bluedot\PhpVersionManager;

class PhpVer
{
    protected $phpDirFile = 'phpdir.txt';
    protected $composerFile = 'composer.json';
    protected $phpDir;

    public function getPhpDir()
    {
        $phpDirFilePath = __DIR__ . DIRECTORY_SEPARATOR . $this->phpDirFile;
        if (!file_exists($phpDirFilePath)) {
            $phpDir = readline("Enter your PHP directory path: ");
            file_put_contents($phpDirFilePath, $phpDir);
        } else {
            $phpDir = trim(file_get_contents($phpDirFilePath));
        }

        return $phpDir;
    }

    public function findPhpVersions($phpDir)
    {
        return glob($phpDir . DIRECTORY_SEPARATOR . 'php*');
    }

    public function getRequiredPhpVersion()
    {
        if (file_exists($this->composerFile)) {
            $composerJson = json_decode(file_get_contents($this->composerFile), true);

            if (isset($composerJson['require']['php'])) {
                return $composerJson['require']['php'];
            }
        }

        return null;
    }

    public function askUserForVersion(array $versions)
    {
        $versionNumbers = array_map(function($versionPath) {
            preg_match('/php(\d+\.\d+\.\d+)/', $versionPath, $matches);
            return $matches[1] ?? '';
        }, $versions);

        echo "Composer.json file not found. Please select a PHP version:\n";
        foreach ($versionNumbers as $index => $versionNumber) {
            echo ($index + 1) . ". PHP $versionNumber\n";
        }

        $selected = readline("Enter your choice (1-" . count($versionNumbers) . "): ");
        if (!isset($versions[$selected - 1])) {
            return false;
        }

        return array(
            'path' => $versions[$selected - 1],
            'text' => $versionNumbers[$selected - 1]
        );
    }


    public function writePhpCmdFile($version, $version_number)
    {
        echo "Setting version to {$version_number}\n";

        if (PHP_OS_FAMILY == "Windows") {
            $phpCmdFile = getcwd() . DIRECTORY_SEPARATOR . 'php.cmd';
            $versionPath = $version.DIRECTORY_SEPARATOR . "php.exe";
            $content = '@ECHO OFF' . PHP_EOL . '"' . $versionPath . '" %*' . PHP_EOL;
        } else { // Assuming *nix
            $phpCmdFile = getcwd() . DIRECTORY_SEPARATOR . 'php';
            $versionPath = $version.DIRECTORY_SEPARATOR . "php";
            $content = '#!/bin/sh' . PHP_EOL . '"' . $versionPath . '" "$@"' . PHP_EOL;
        }
        
        if (file_exists($versionPath)) {
            file_put_contents($phpCmdFile, $content);
            if (PHP_OS_FAMILY != "Windows") {
                @chmod($phpCmdFile, 0755); // Making the file executable
                echo "\n\nrun `chmod +x php` to make php executable\n\n";
            }
            echo "\n\nRun php -v to check php version\n";
        }
    }

    public function process()
    {
        $phpDir = $this->getPhpDir();
        $phpVersions = $this->findPhpVersions($phpDir);

        $phpVersionNumbers = array_map(function($versionPath) {
            preg_match('/php(\d+\.\d+\.\d+)/', $versionPath, $matches);
            return $matches[1] ?? '';
        }, $phpVersions);

        $requiredVersion = $this->getRequiredPhpVersion();

        if ($requiredVersion) {
            $requiredVersion = ltrim($requiredVersion, '>=');
            echo "Required version is {$requiredVersion}\n";
            foreach ($phpVersionNumbers as $index => $versionNumber) {
                if (version_compare($versionNumber, $requiredVersion, '>=')) {
                    $this->writePhpCmdFile($phpVersions[$index], $versionNumber);
                    return;
                }
            }
        }

        $selectedVersion = $this->askUserForVersion($phpVersions);
        if ($selectedVersion) {
            $this->writePhpCmdFile($selectedVersion['path'], $selectedVersion['text']);
        }
    }

}
