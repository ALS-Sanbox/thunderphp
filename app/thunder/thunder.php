<?php

namespace Thunder;
use Exception;

class Thunder
{
    const PLUGINS_DIR = 'plugins';

    public function title(){
        $VERSION = function_exists('app_version') ? app_version() : '1.0.0';

        echo "
         ___________.__                      .___             __________  ___ _____________ 
         \\__    ___/|  |__  __ __  ____    __| _/___________  \\______   \\/   |   \\______   \\
           |    |   |  |  \\|  |  \\/    \\  / __ |/ __ \\_  __ \\  |     ___/    ~    \\     ___/
           |    |   |   Y  \\  |  /   |  \\/ /_/ \\  ___/|  | \\/  |    |   \\    Y    /    |    
           |____|   |___|  /____/|___|  /\\____ |\\___  >__|     |____|    \\___|_  /|____|    
                         \\/           \\/      \\/    \\/                         \\/  

                               ThunderPHP v$VERSION Command Line Tool";
    }

    public function help(){
        $this-> title();

        echo "

        Install Commands:
        - do:install --db-host=... --db-name=... --db-user=... --db-password=... --site-url=... --admin-email=... --admin-password=... --admin-first-name=... --admin-last-name=... [--site-name=...] [--site-contact-email=...] [--profile=standard|minimal]
              Non-interactive install: writes config.php, runs migrations, creates the admin account. Refuses to run if the site is already installed.

        Database Commands:
        - do:migrate <plugin|all> [file] : Run migrations for a plugin, or 'all' to migrate every plugin.
        - do:refresh <plugin|all> [file] : Rollback and rerun migrations for a plugin, or 'all'.
        - do:rollback <plugin|all> [file]: Undo migrations for a plugin, or 'all'.

        Generator Commands:
        - make:plugin   : Generate a new plugin.
        - make:migration: Create a new migration file.
        - make:model    : Generate a new model.

        Usage:
        php thunder <command> [arguments]
        ";
    }

    public function makePlugin($args){
        $name = $args[0] ?? 'UnnamedPlugin_' . time();
        $folder = 'plugins' . DIRECTORY_SEPARATOR . $name;

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

        $sampleFilesPath = FCPATH . "app" . DIRECTORY_SEPARATOR . "thunder" . DIRECTORY_SEPARATOR . "samples" . DIRECTORY_SEPARATOR;

        $filesToCopy = [
            'plugin-sample.php'     => 'plugin.php',
            'config-sample.json'    => 'config.json',
            'controller-sample.php' => 'controllers/controller.php',
            'view-sample.php'       => 'views/view.php',
            'js-sample.js'          => 'assets/js/plugin.js',
            'css-sample.css'        => 'assets/css/style.css',
        ];

        foreach ($filesToCopy as $source => $destination) {
            $sourcePath = $sampleFilesPath . $source;
            $destinationPath = $folder . DIRECTORY_SEPARATOR . $destination;

            if (!file_exists($sourcePath)) {
                echo "Base file not found: $sourcePath\n";
                continue;
            }

            if (!copy($sourcePath, $destinationPath)) {
                throw new Exception("Failed to copy file: $sourcePath to $destinationPath");
            }

            echo "- Copied $source to $destinationPath\n";
        }
    }


    public function makeMigration($args){
        $pluginName = $args[0] ?? null;
        $tableName = $args[1] ?? null;

        if (!$pluginName || !$tableName) {
            die("Usage: php thunder make:migrate <plugin_name> <table_name>\n");
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
            die("Error: Table name must contain only letters and numbers.\n");
        }
        
        $className = ucfirst(strtolower($tableName));

        $formattedTableName = strtolower($tableName);

        $timestamp = date('Ymd_His'); // Format: YYYYMMDD_HHMMSS

        $pluginMigrationsFolder = 'plugins' . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . 'migrations';
        $sampleMigrationFile = FCPATH . "app" . DIRECTORY_SEPARATOR . "thunder" . DIRECTORY_SEPARATOR . "samples" . DIRECTORY_SEPARATOR . "migration-sample.php";
        $newMigrationFile = $pluginMigrationsFolder . DIRECTORY_SEPARATOR . $timestamp . '_' . $className . '.php';

        $this->createFolder($pluginMigrationsFolder);

        if (!file_exists($sampleMigrationFile)) {
            die("Sample migration file not found: $sampleMigrationFile\n");
        }

        $migrationContent = file_get_contents($sampleMigrationFile);

        $migrationContent = str_replace('{CLASS_NAME}', $className, $migrationContent);
        $migrationContent = str_replace('{TABLE_NAME}', "'$formattedTableName'", $migrationContent);

        file_put_contents($newMigrationFile, $migrationContent);

        echo "\nMigration file created successfully: $newMigrationFile\n";
    }

    public function makeModel($args){
        $pluginName = $args[0] ?? null;
        $tableName = $args[1] ?? null;

        if (!$pluginName || !$tableName) {
            die("\nUsage: php thunder make:model <plugin_name> <table_name>\n");
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
            die("\nError: Table name must contain only letters and numbers.\n");
        }
        
        $className = ucfirst(strtolower($tableName));

        $formattedTableName = strtolower($tableName);

        $pluginmodelsFolder = 'plugins' . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . 'models';
        $samplemodelFile = FCPATH . "app" . DIRECTORY_SEPARATOR . "thunder" . DIRECTORY_SEPARATOR . "samples" . DIRECTORY_SEPARATOR . "model-sample.php";
        $newmodelFile = $pluginmodelsFolder . DIRECTORY_SEPARATOR . $className . '.php';

        $this->createFolder($pluginmodelsFolder);

        if (!file_exists($samplemodelFile)) {
            die("\nSample model file not found: $samplemodelFile\n");
        }

        $modelContent = file_get_contents($samplemodelFile);

        $modelContent = str_replace('{CLASS_NAME}', $className, $modelContent);
        $modelContent = str_replace('{TABLE_NAME}', "'$formattedTableName'", $modelContent);

        $namespace = str_replace("-"," ", $pluginName);
        $namespace = ucwords($namespace);
        $namespace = str_replace("","", $pluginName);
        $modelContent = str_replace('{NAMESPACE}', $namespace, $modelContent);

        file_put_contents($newmodelFile, $modelContent);

        echo "model file created successfully: $newmodelFile\n";
    }

    public function deletePlugin($args){
        $name = $args[0] ?? null;
    
        if (!$name) {
            die("Please specify the name of the plugin to delete.\n");
        }
    
        $folder = 'plugins' . DIRECTORY_SEPARATOR . $name;
    
        if (!file_exists($folder)) {
            die("The plugin '$name' does not exist.\n");
        }
    
        echo "Deleting plugin: $name\n";

        $this->deleteFolder($folder);
    
        echo "Plugin '$name' deleted successfully.\n";
    }
    
    public function deleteMigration($args){
        $name = $args[0] ?? null;
    
        if (!$name) {
            die("Please specify the name of the migration to delete.\n");
        }
    
        $folder = 'migrations';
        $file = $folder . DIRECTORY_SEPARATOR . $name . '.php';
    
        if (!file_exists($file)) {
            die("The migration '$name' does not exist.\n");
        }
    
        echo "Deleting migration: $name\n";

        unlink($file);
        echo "- Deleted migration file: $file\n";
    
        echo "Migration '$name' deleted successfully.\n";
    }
    
    public function deleteModel($args){
        $name = $args[0] ?? null;
    
        if (!$name) {
            die("Please specify the name of the model to delete.\n");
        }
    
        $folder = 'models';
        $file = $folder . DIRECTORY_SEPARATOR . $name . '.php';
    
        if (!file_exists($file)) {
            die("The model '$name' does not exist.\n");
        }
    
        echo "Deleting model: $name\n";

        unlink($file);
        echo "- Deleted model file: $file\n";
    
        echo "Model '$name' deleted successfully.\n";
    }
    
    public function doMigrate(array $args)
    {
        $pluginName = $args[0] ?? null;

        if (!$pluginName) {
            die("Usage: php thunder do:migrate <plugin_name>|all [migration_file]\n");
        }

        if ($pluginName === 'all') {
            $this->migrateAllPlugins();
            return;
        }

        $migrationFile = $args[1] ?? null;

        $this->validatePluginAndMigration($pluginName, $migrationFile);

        $pluginMigrationsFolder = self::PLUGINS_DIR . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . 'migrations';

        if ($migrationFile) {
            $this->runSingleMigration($pluginName, $pluginMigrationsFolder, $migrationFile);
        } else {
            $this->runAllMigrations($pluginName, $pluginMigrationsFolder);
        }
    }

    private function getAllPluginNames()
    {
        $pluginDirs = glob(self::PLUGINS_DIR . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
        $names = array_map('basename', $pluginDirs);

        // Plugins otherwise migrate in alphabetical folder order. Many
        // plugins seed rows into the shared `settings` table as part of
        // their own migration, so on a fresh database `settings` has to run
        // first or those inserts fail against a table that doesn't exist
        // yet - and since insert() logs the migration as applied even when
        // an individual insert fails, a failed seed here never gets a
        // second chance on a retry.
        usort($names, fn($a, $b) => ($b === 'settings') <=> ($a === 'settings') ?: $a <=> $b);

        return $names;
    }

    private function migrateAllPlugins()
    {
        echo "Running migrations for all plugins:\n";

        foreach ($this->getAllPluginNames() as $pluginName) {
            $pluginMigrationsFolder = self::PLUGINS_DIR . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . 'migrations';

            if (!is_dir($pluginMigrationsFolder) || empty(glob($pluginMigrationsFolder . DIRECTORY_SEPARATOR . '*.php'))) {
                continue;
            }

            echo "\n-- Plugin: $pluginName --\n";
            $this->runAllMigrations($pluginName, $pluginMigrationsFolder);
        }

        echo "\nAll plugin migrations completed successfully.\n";
    }

    private function validatePluginAndMigration($pluginName, $migrationFile = null)
    {
        if (!$pluginName) {
            die("Usage: php thunder do:migrate <plugin_name> [migration_file]\n");
        }

        $pluginMigrationsFolder = self::PLUGINS_DIR . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . 'migrations';

        if (!is_dir($pluginMigrationsFolder)) {
            die("Error: Migration folder not found for plugin '$pluginName'.\n");
        }

        if ($migrationFile && !file_exists($pluginMigrationsFolder . DIRECTORY_SEPARATOR . $migrationFile)) {
            die("Error: Migration file '$migrationFile' not found.\n");
        }
    }

    private function runSingleMigration($pluginName, $pluginMigrationsFolder, $migrationFile)
    {
        echo "Running migration: $migrationFile\n";
        require_once $pluginMigrationsFolder . DIRECTORY_SEPARATOR . $migrationFile;
        $this->executeMigration($pluginName, $migrationFile);
    }

    private function runAllMigrations($pluginName, $pluginMigrationsFolder)
    {
        $migrations = glob($pluginMigrationsFolder . DIRECTORY_SEPARATOR . '*.php');

        if (empty($migrations)) {
            die("No migration files found in '$pluginMigrationsFolder'.\n");
        }

        sort($migrations);

        echo "Running all migrations:\n";
        foreach ($migrations as $migration) {
            echo "Running migration: " . basename($migration) . "\n";
            require_once $migration;
            $this->executeMigration($pluginName, basename($migration));
        }

        echo "All migrations completed successfully.\n";
    }

    public function doRefresh($args = [])
    {
        $pluginName = $args[0] ?? null;
        $migrationFile = $args[1] ?? null;
        
        echo "\nRolling back migrations...\n";
        $this->doRollback([$pluginName, $migrationFile]);
        
        echo "\nMigrations rolled back. Running fresh migrations...\n";
        $this->doMigrate([$pluginName, $migrationFile]);
        
        echo "\nDatabase refresh complete.\n";
    }

    public function doRollback(array $args)
    {
        $pluginName = $args[0] ?? null;

        if (!$pluginName) {
            die("Usage: php thunder do:rollback <plugin_name>|all [migration_file]\n");
        }

        if ($pluginName === 'all') {
            $this->rollbackAllPlugins();
            return;
        }

        $migrationFile = $args[1] ?? null;

        $this->validatePluginAndMigration($pluginName, $migrationFile);

        $pluginMigrationsFolder = self::PLUGINS_DIR . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . 'migrations';

        if ($migrationFile) {
            $this->runSingleRollback($pluginName, $pluginMigrationsFolder, $migrationFile);
        } else {
            $this->runAllRollbacks($pluginName, $pluginMigrationsFolder);
        }
    }

    private function rollbackAllPlugins()
    {
        echo "Rolling back migrations for all plugins:\n";

        foreach ($this->getAllPluginNames() as $pluginName) {
            $pluginMigrationsFolder = self::PLUGINS_DIR . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . 'migrations';

            if (!is_dir($pluginMigrationsFolder) || empty(glob($pluginMigrationsFolder . DIRECTORY_SEPARATOR . '*.php'))) {
                continue;
            }

            echo "\n-- Plugin: $pluginName --\n";
            $this->runAllRollbacks($pluginName, $pluginMigrationsFolder);
        }

        echo "\nAll plugin migrations rolled back successfully.\n";
    }

    private function runSingleRollback($pluginName, $pluginMigrationsFolder, $migrationFile)
    {
        echo "\nRolling back migration: $migrationFile\n";
        require_once $pluginMigrationsFolder . DIRECTORY_SEPARATOR . $migrationFile;
        $this->executeRollback($pluginName, $migrationFile);
    }

    private function runAllRollbacks($pluginName, $pluginMigrationsFolder)
    {
        $migrations = glob($pluginMigrationsFolder . DIRECTORY_SEPARATOR . '*.php');

        if (empty($migrations)) {
            die("\nNo migration files found in '$pluginMigrationsFolder'.\n");
        }

        sort($migrations);

        echo "\nRolling back all migrations:\n";
        foreach ($migrations as $migration) {
            echo "\nRolling back migration: " . basename($migration) . "\n";
            require_once $migration;
            $this->executeRollback($pluginName, basename($migration));
        }

        echo "\nAll migrations rolled back successfully.\n";
    }

    private function executeMigration($pluginName, $migrationFile)
    {
        // Strip off the timestamp (e.g., '20250130_163917_')
        $className = pathinfo($migrationFile, PATHINFO_FILENAME);
        $className = preg_replace('/^\d{8}_\d{6}_/', '', $className); // Remove timestamp prefix

        if (!class_exists($className)) {
            die("\nError: Class '$className' not found in migration file '$migrationFile'.\n");
        }

        if ($this->isMigrationLogged($pluginName, $migrationFile)) {
            echo "\nMigration '$className' already applied - skipping.\n";
            return;
        }

        $migrationInstance = new $className();

        if (!method_exists($migrationInstance, 'up')) {
            die("\nError: Migration class '$className' does not have an 'up' method.\n");
        }

        $migrationInstance->up();
        $this->logMigration($pluginName, $migrationFile);
        echo "\nMigration '$className' applied successfully.\n";
    }


    private function executeRollback($pluginName, $migrationFile)
    {
        // Strip off the timestamp (e.g., '20250130_163917_')
        $className = pathinfo($migrationFile, PATHINFO_FILENAME);
        $className = preg_replace('/^\d{8}_\d{6}_/', '', $className); // Remove timestamp prefix

        if (!class_exists($className)) {
            die("Error: Class '$className' not found in migration file '$migrationFile'.\n");
        }

        $migrationInstance = new $className();

        if (!method_exists($migrationInstance, 'down')) {
            die("Error: Migration class '$className' does not have a 'down' method.\n");
        }

        $migrationInstance->down();
        $this->unlogMigration($pluginName, $migrationFile);
        echo "Migration '$className' rolled back successfully.\n";
    }

    /**
     * do:migrate has no "already ran" tracking beyond this table - without
     * it, re-running a migration with seed data (addData()+insert()) creates
     * duplicate rows every time, since createTable() alone is idempotent but
     * insert() isn't. Self-bootstrapping (created here, not via any plugin's
     * own migration) so it always exists before the first check, regardless
     * of which plugin's migrations happen to run first.
     */
    private function migrationsLogDb(): \Core\Database
    {
        static $db = null;

        if ($db === null) {
            $db = new \Core\Database();
            $db->query("CREATE TABLE IF NOT EXISTS migrations_log (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                plugin VARCHAR(255) NOT NULL,
                migration_file VARCHAR(255) NOT NULL,
                applied_at DATETIME NOT NULL,
                UNIQUE KEY plugin_migration (plugin, migration_file)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        return $db;
    }

    private function isMigrationLogged(string $pluginName, string $migrationFile): bool
    {
        $db = $this->migrationsLogDb();
        $row = $db->fetch("SELECT id FROM migrations_log WHERE plugin = ? AND migration_file = ?", [$pluginName, $migrationFile]);
        return (bool) $row;
    }

    private function logMigration(string $pluginName, string $migrationFile): void
    {
        $db = $this->migrationsLogDb();
        $db->query(
            "INSERT INTO migrations_log (plugin, migration_file, applied_at) VALUES (?, ?, ?)",
            [$pluginName, $migrationFile, date('Y-m-d H:i:s')]
        );
    }

    private function unlogMigration(string $pluginName, string $migrationFile): void
    {
        $db = $this->migrationsLogDb();
        $db->query("DELETE FROM migrations_log WHERE plugin = ? AND migration_file = ?", [$pluginName, $migrationFile]);
    }


    public function doInstall(array $args)
    {
        $options = $this->parseInstallArgs($args);

        if (!defined('INSTALL_ROOT')) {
            define('INSTALL_ROOT', FCPATH);
        }
        require_once FCPATH . 'install' . DIRECTORY_SEPARATOR . 'functions.php';

        if (install_already_completed()) {
            echo "This site is already installed - do:install refuses to run again.\n";
            echo "(config.php exists and the database already has an admin account.)\n";
            exit(1);
        }

        $required = ['db-host', 'db-name', 'db-user', 'site-url', 'admin-email', 'admin-password', 'admin-first-name', 'admin-last-name'];
        $missing = array_filter($required, fn($key) => !isset($options[$key]) || $options[$key] === '');

        if (!empty($missing)) {
            echo "Missing required arguments: --" . implode(', --', $missing) . "\n\n";
            echo $this->installUsage();
            exit(1);
        }

        $db = [
            'host'     => $options['db-host'],
            'name'     => $options['db-name'],
            'user'     => $options['db-user'],
            'password' => $options['db-password'] ?? '',
        ];

        $connectionError = install_test_db_connection($db['host'], $db['name'], $db['user'], $db['password']);
        if ($connectionError !== null) {
            echo "Database connection failed: $connectionError\n";
            exit(1);
        }

        $adminData = [
            'first_name'       => $options['admin-first-name'],
            'last_name'        => $options['admin-last-name'],
            'email'            => $options['admin-email'],
            'password'         => $options['admin-password'],
            'confirm_password' => $options['admin-password'],
        ];

        $errors = install_validate_admin_account($adminData);
        if (!empty($errors)) {
            echo "Invalid admin account details:\n";
            foreach ($errors as $field => $msg) {
                echo "  - $field: $msg\n";
            }
            exit(1);
        }

        $root = rtrim($options['site-url'], '/');
        $siteName = $options['site-name'] ?? 'ThunderPHP';
        $siteContactEmail = $options['site-contact-email'] ?? $options['admin-email'];
        $requestedProfile = $options['profile'] ?? 'standard';
        $profile = in_array($requestedProfile, ['standard', 'minimal'], true) ? $requestedProfile : 'standard';

        if (!install_write_config($db, $root)) {
            echo "Could not write config.php - check that the application root is writable.\n";
            exit(1);
        }
        echo "config.php written.\n";

        require_once FCPATH . 'config.php';
        require_once FCPATH . 'app' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'functions.php';
        require_once FCPATH . 'app' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Database.php';
        require_once FCPATH . 'app' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Migration.php';

        echo "Running migrations ($profile profile)...\n";

        if ($profile === 'minimal') {
            foreach (INSTALL_MINIMAL_PLUGINS as $pluginName) {
                $migrationsFolder = FCPATH . 'plugins' . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . 'migrations';
                if (!is_dir($migrationsFolder) || empty(glob($migrationsFolder . DIRECTORY_SEPARATOR . '*.php'))) {
                    continue;
                }
                $this->doMigrate([$pluginName]);
            }

            foreach (install_get_all_plugin_folders() as $pluginFolder) {
                install_set_plugin_active($pluginFolder, in_array($pluginFolder, INSTALL_MINIMAL_PLUGINS, true));
            }
        } else {
            $this->doMigrate(['all']);
        }

        echo "Creating admin account...\n";

        try {
            $pdo = new \PDO(DB_DRIVER . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASSWORD, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            $stmt = $pdo->prepare("INSERT INTO siteusers (first_name, last_name, email, password, date_created) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $adminData['first_name'],
                $adminData['last_name'],
                $adminData['email'],
                password_hash($adminData['password'], PASSWORD_DEFAULT),
                date('Y-m-d H:i:s'),
            ]);
            $newUserId = (int) $pdo->lastInsertId();

            $roleStmt = $pdo->prepare("SELECT id FROM user_roles WHERE role = 'admin' LIMIT 1");
            $roleStmt->execute();
            $adminRoleId = $roleStmt->fetchColumn();

            if ($adminRoleId !== false) {
                $pdo->prepare("INSERT INTO user_roles_map (role_id, user_id, disabled) VALUES (?, ?, 0)")
                    ->execute([(int) $adminRoleId, $newUserId]);
            }

            $settingsStmt = $pdo->prepare("UPDATE settings SET value = ? WHERE `key` = ?");
            $settingsStmt->execute([$siteName, 'site_name']);
            $settingsStmt->execute([$siteContactEmail, 'admin_email']);
            $settingsStmt->execute([$root, 'site_url']);
        } catch (\Throwable $e) {
            echo "Could not create the admin account: " . $e->getMessage() . "\n";
            exit(1);
        }

        echo "\nInstall complete. Log in at {$root}/login with {$adminData['email']}\n";
    }

    private function installUsage(): string
    {
        return "Usage: php thunder do:install --db-host=HOST --db-name=NAME --db-user=USER --db-password=PASS --site-url=URL --admin-email=EMAIL --admin-password=PASS --admin-first-name=NAME --admin-last-name=NAME [--site-name=NAME] [--site-contact-email=EMAIL] [--profile=standard|minimal]\n";
    }

    private function parseInstallArgs(array $args): array
    {
        $options = [];
        $i = 0;
        $count = count($args);

        while ($i < $count) {
            $arg = $args[$i];

            if (str_starts_with($arg, '--')) {
                $key = substr($arg, 2);

                if (str_contains($key, '=')) {
                    [$key, $value] = explode('=', $key, 2);
                    $options[$key] = $value;
                    $i++;
                    continue;
                }

                $next = $args[$i + 1] ?? null;
                if ($next !== null && !str_starts_with($next, '--')) {
                    $options[$key] = $next;
                    $i += 2;
                } else {
                    $options[$key] = '';
                    $i++;
                }
            } else {
                $i++;
            }
        }

        return $options;
    }

    private function createFolder($path)
    {
        if (!mkdir($path, 0777, true) && !is_dir($path)) {
            echo("Directory already exists: $path");
        }
    }

    private function deleteFolder($folder)
    {
        $items = array_diff(scandir($folder), ['.', '..']);
    
        foreach ($items as $item) {
            $itemPath = $folder . DIRECTORY_SEPARATOR . $item;
    
            if (is_dir($itemPath)) {
                $this->deleteFolder($itemPath);
            } else {
                unlink($itemPath);
                echo "- Deleted file: $itemPath\n";
            }
        }
    
        rmdir($folder);
        echo "- Deleted folder: $folder\n";
    }

}