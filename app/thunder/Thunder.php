<?php

namespace Thunder;

use Exception;

class Thunder
{
    const VERSION = '1.0.0';
    const PLUGINS_DIR = 'plugins';
    const MODELS_DIR = 'models';
    const MIGRATIONS_DIR = 'migrations';
    const SAMPLES_DIR = 'app/thunder/samples';

    public function title()
    {
        echo "
         ___________.__                      .___             __________  ___ _____________ 
         \\__    ___/|  |__  __ __  ____    __| _/___________  \\______   \\/   |   \\______   \\
           |    |   |  |  \\|  |  \\/    \\  / __ |/ __ \\_  __ \\  |     ___/    ~    \\     ___/
           |    |   |   Y  \\  |  /   |  \\/ /_/ \\  ___/|  | \\/  |    |   \\    Y    /    |    
           |____|   |___|  /____/|___|  /\\____ |\\___  >__|     |____|    \\___|_  /|____|    
                         \\/           \\/      \\/    \\/                         \\/  

                               ThunderPHP v" . self::VERSION . " Command Line Tool";
    }

    public function help()
    {
        $this->title();

        echo "

        Database Commands:
        - do:migrate    : Run pending migrations.
        - do:refresh    : Rollback all migrations and rerun them.
        - do:rollback   : Undo the last batch of migrations.

        Generator Commands:
        - make:plugin   : Generate a new plugin.
        - make:migration: Create a new migration file.
        - make:model    : Generate a new model.

        Usage:
        php thunder <command> [arguments]
        ";
    }

    public function makePlugin($args)
    {
        $name = $args[0] ?? 'UnnamedPlugin_' . time();
        $folder = $this->getPluginPath($name);

        if (file_exists($folder)) {
            throw new Exception("The plugin folder '$name' already exists.");
        }

        echo "Generating plugin: $name\n";

        $this->createFolder($folder);

        $folders = [
            'assets/css', 'assets/js', 'assets/fonts',
            'assets/images', 'controllers', 'views',
            'models', 'includes',
        ];

        foreach ($folders as $subFolder) {
            $this->createFolder($folder . DIRECTORY_SEPARATOR . $subFolder);
        }

        echo "Plugin structure created successfully.\n\n";

        $filesToCopy = [
            'plugin-sample.php'     => 'plugin.php',
            'config-sample.json'    => 'config.json',
            'controller-sample.php' => 'controllers/controller.php',
            'view-sample.php'       => 'views/view.php',
            'js-sample.js'          => 'assets/js/plugin.js',
            'css-sample.css'        => 'assets/css/style.css',
        ];

        foreach ($filesToCopy as $source => $destination) {
            $sourcePath = $this->getSamplePath($source);
            $destinationPath = $folder . DIRECTORY_SEPARATOR . $destination;
            $this->copySampleFile($sourcePath, $destinationPath);
        }
    }

    public function makeMigration($args)
    {
        [$pluginName, $tableName] = $args + [null, null];

        if (!$pluginName || !$tableName) {
            throw new Exception("Usage: php thunder make:migration <plugin_name> <table_name>");
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
            throw new Exception("Error: Table name must contain only letters and numbers.");
        }

        $className = ucfirst(strtolower($tableName));
        $timestamp = date('Ymd_His');
        $fileName = $timestamp . '_' . $className . '.php';

        $this->generateFromTemplate(
            $pluginName,
            $tableName,
            'migration-sample.php',
            $this->getPluginPath($pluginName, self::MIGRATIONS_DIR),
            [
                '{CLASS_NAME}' => $className,
                '{TABLE_NAME}' => "'" . strtolower($tableName) . "'"
            ],
            $fileName
        );
    }

    public function makeModel($args)
    {
        [$pluginName, $tableName] = $args + [null, null];

        if (!$pluginName || !$tableName) {
            throw new Exception("Usage: php thunder make:model <plugin_name> <table_name>");
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
            throw new Exception("Error: Table name must contain only letters and numbers.");
        }

        $className = ucfirst(strtolower($tableName));
        $namespace = str_replace(" ", "", ucwords(str_replace("-", " ", $pluginName)));

        $this->generateFromTemplate(
            $pluginName,
            $tableName,
            'model-sample.php',
            $this->getPluginPath($pluginName, self::MODELS_DIR),
            [
                '{CLASS_NAME}' => $className,
                '{TABLE_NAME}' => "'" . strtolower($tableName) . "'",
                '{NAMESPACE}'  => $namespace
            ]
        );
    }

    public function deletePlugin($args)
    {
        $name = $args[0] ?? null;
        if (!$name) throw new Exception("Please specify the name of the plugin to delete.");

        $folder = $this->getPluginPath($name);

        if (!file_exists($folder)) {
            throw new Exception("The plugin '$name' does not exist.");
        }

        echo "Deleting plugin: $name\n";
        $this->deleteFolder($folder);
        echo "Plugin '$name' deleted successfully.\n";
    }

    public function deleteMigration($args)
    {
        $name = $args[0] ?? null;
        if (!$name) throw new Exception("Please specify the name of the migration to delete.");

        $file = self::MIGRATIONS_DIR . DIRECTORY_SEPARATOR . $name . '.php';
        if (!file_exists($file)) throw new Exception("The migration '$name' does not exist.");

        unlink($file);
        echo "Deleted migration: $file\n";
    }

    public function deleteModel($args)
    {
        $name = $args[0] ?? null;
        if (!$name) throw new Exception("Please specify the name of the model to delete.");

        $file = self::MODELS_DIR . DIRECTORY_SEPARATOR . $name . '.php';
        if (!file_exists($file)) throw new Exception("The model '$name' does not exist.");

        unlink($file);
        echo "Deleted model: $file\n";
    }

    // --- Shared helpers below ---

    private function getSamplePath($filename)
    {
        return FCPATH . DIRECTORY_SEPARATOR . self::SAMPLES_DIR . DIRECTORY_SEPARATOR . $filename;
    }

    private function getPluginPath($pluginName, $subPath = '')
    {
        $base = self::PLUGINS_DIR . DIRECTORY_SEPARATOR . $pluginName;
        return $subPath ? $base . DIRECTORY_SEPARATOR . $subPath : $base;
    }

    private function copySampleFile($source, $destination)
    {
        if (!file_exists($source)) {
            echo "Base file not found: $source\n";
            return;
        }

        if (!copy($source, $destination)) {
            throw new Exception("Failed to copy file: $source to $destination");
        }

        echo "- Copied " . basename($source) . " to $destination\n";
    }

    private function generateFromTemplate($pluginName, $tableName, $templateName, $destinationDir, $replacements, $customFileName = null)
    {
        $this->createFolder($destinationDir);
        $templatePath = $this->getSamplePath($templateName);

        if (!file_exists($templatePath)) {
            throw new Exception("Sample file not found: $templatePath");
        }

        $content = file_get_contents($templatePath);

        foreach ($replacements as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        $fileName = $customFileName ?? ucfirst(strtolower($tableName)) . '.php';
        $fullPath = $destinationDir . DIRECTORY_SEPARATOR . $fileName;
        file_put_contents($fullPath, $content);

        echo ucfirst(pathinfo($templateName, PATHINFO_FILENAME)) . " created successfully: $fullPath\n";
    }

    private function createFolder($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    private function deleteFolder($folder)
    {
        $items = array_diff(scandir($folder), ['.', '.']);
        foreach ($items as $item) {
            $path = $folder . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->deleteFolder($path) : unlink($path);
        }

        rmdir($folder);
        echo "- Deleted folder: $folder\n";
    }
}
