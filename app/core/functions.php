<?php  

defined('ROOT') or die("Direct script access denied");

// Caching plugin configs to avoid redundant reads
global $PLUGIN_CACHE;
$PLUGIN_CACHE = [];

function APP($key = '') {
    global $APP;

    return $key !== '' ? ($APP[$key] ?? null) : $APP;
}

function show_plugins() {
    global $APP;
    $names = array_column($APP['plugins'] ?? [], 'name');
    debug_dump($names ?? []);
}

function load_json_file(string $path) {
    if (!file_exists($path)) return null;

    $json_str = file_get_contents($path);
    $data = json_decode($json_str);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "JSON error in '$path': " . json_last_error_msg() . "<br>";
        return null;
    }

    return $data;
}

function set_value(string|array $key, mixed $value = ''): bool {
    global $USER_DATA;

    $path = get_plugin_dir(get_caller_file()) . 'config.json';
    $config = load_json_file($path);
    $plugin_id = $config->id ?? null;

    if (!$plugin_id) return false;

    if (is_array($key)) {
        foreach ($key as $k => $v) {
            $USER_DATA[$plugin_id][$k] = $v;
        }
    } else {
        $USER_DATA[$plugin_id][$key] = $value;
    }

    return true;
}

function get_value(string $key = ''): mixed {
    global $USER_DATA;

    $path = get_plugin_dir(get_caller_file()) . 'config.json';
    $config = load_json_file($path);
    $plugin_id = $config->id ?? null;

    if (!$plugin_id) return null;

    return $key === '' ? ($USER_DATA[$plugin_id] ?? null) : ($USER_DATA[$plugin_id][$key] ?? null);
}

function get_caller_file(): string {
    $trace = debug_backtrace();
    $caller = $trace[1] ?? end($trace);
    return $caller['file'] ?? '';
}

function plugin_config(): ?object {
    $path = get_plugin_dir(get_caller_file()) . 'config.json';
    return load_json_file($path);
}


function split_url($url) {
    $parts = explode("/", trim($url, '/'));

    $cleaned_parts = array_map(function($part) {
        return preg_replace('/[^a-zA-Z0-9-]/', '', $part);
    }, $parts);

    return $cleaned_parts;
}

function URL($key = '') {
    global $APP;

    if (is_numeric($key) || !empty($key)) {
        if (!empty($APP['URL'][$key])) {
            return $APP['URL'][$key];
        }
    } else {
        return $APP['URL'];
    }

    return '';
}

function getPhpFiles($directory) {
    $phpFiles = [];

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && strtolower($file->getExtension()) == 'php') {
            $phpFiles[] = $file->getPathname();
        }
    }

    return $phpFiles;
}

function get_plugin_folders() {
    $plugins_folder = 'plugins/';
    $folders = scandir($plugins_folder);
    $result = [];

    foreach ($folders as $folder) {
        if ($folder != '.' && $folder != ".." && is_dir($plugins_folder . $folder))
            $result[] = $folder;
    }

    return $result;
}

function load_plugins($plugin_folders) {
    global $APP;
    $loaded = false;
    $all_plugins = [];

    foreach ($plugin_folders as $folder) {
        $config_path = "plugins/$folder/config.json";

        $json = load_json_file($config_path);
        if (!$json || !isset($json->id)) continue;

        $json->path = "plugins/$folder/";
        $json->http_path = ROOT . '/' . $json->path;
        $json->index_file = "plugins/$folder/plugin.php";

        $all_plugins[$json->id] = $json;
    }

    foreach ($all_plugins as $plugin_id => $json) {
        if (!empty($json->dependencies)) {
            foreach ($json->dependencies as $dependency_id => $required_version) {
                if (!isset($all_plugins[$dependency_id])) {
                    echo "Error: Missing dependency '$dependency_id' for plugin '{$json->name}'<br>";
                    continue 2;
                }

                if (version_compare($all_plugins[$dependency_id]->version, $required_version, '<')) {
                    echo "Error: Plugin '{$json->name}' requires '$dependency_id' version $required_version or higher, but found {$all_plugins[$dependency_id]->version}<br>";
                    continue 2;
                }
            }
        }

        if (!empty($json->active) && file_exists($json->index_file) && valid_route($json)) {
            $APP['plugins'][] = $json;
        }
    }

    if (!empty($APP['plugins'])) {
        usort($APP['plugins'], fn($a, $b) => ($a->index ?? 0) <=> ($b->index ?? 0));

        foreach ($APP['plugins'] as $json) {
            if (file_exists($json->index_file)) {
                require $json->index_file;
                $loaded = true;
            }
        }
    }

    return $loaded;
}

function add_action(string $hook, mixed $func, int $priority = 10): bool {
    global $ACTIONS;

    while (!empty($ACTIONS[$hook][$priority])) {
        $priority++;
    }

    $ACTIONS[$hook][$priority] = $func;

    return true;
}

function do_action(string $hook, array $data = []) {
    global $ACTIONS;

    if (!empty($ACTIONS[$hook])) {
        ksort($ACTIONS[$hook]);
        foreach ($ACTIONS[$hook] as $key => $func) {
            $func($data);
        }
    }
}

function add_filter(string $hook, mixed $func, int $priority = 10) {
    global $FILTER;

    while (!empty($FILTER[$hook][$priority])) {
        $priority++;
    }

    $FILTER[$hook][$priority] = $func;

    return true;
}

function do_filter(string $hook, mixed $data = ''): mixed {
    global $FILTER;

    if (!empty($FILTER[$hook])) {
        ksort($FILTER[$hook]);
        foreach ($FILTER[$hook] as $key => $func) {
            $data = $func($data);
        }
    }

    return $data;
}

function dd($data) {
    echo "<pre><div style='margin:1px;background-color:#444;color:white;padding:5px 10px'>";
    print_r($data);
    echo "</div></pre>";
}

function page() {
    return URL(0);
}

function redirect($url) {
    header("Location: " . ROOT . '/' . $url);
    die;
}

function esc(?string $str): string|null {
    return htmlspecialchars($str);
}

function get_date(string $date): string {
    return date("jS M, Y", strtotime($date));
}

function message(string $msg = '', string $type = '') {
    $session = new \Core\Session();

    if (!empty($msg)) {
        $session->set('message', ['text' => $msg, 'type' => $type]);
        return null;
    }

    $output = $session->get('message');

    if (!empty($output)) {
        $session->set('message', null);
    }

    return $output;
}

function valid_route(object $json): bool {
    if (!empty($json->routes->off) && is_array($json->routes->off)) {
        if (in_array(page(), $json->routes->off)) {
            return false;
        }
    }

    if (!empty($json->routes->on) && is_array($json->routes->on)) {
        if ($json->routes->on[0] == 'all' || in_array(page(), $json->routes->on)) {
            return true;
        }
    }

    return false;
}

function plugin_path(string $path = ''): string {
    $called_from = debug_backtrace();
    $key = array_search(__FUNCTION__, array_column($called_from, 'function'));
    return get_plugin_dir(debug_backtrace()[$key]['file']) . $path;
}

function plugin_http_path(string $path = ''): string {
    $called_from = debug_backtrace();
    $key = array_search(__FUNCTION__, array_column($called_from, 'function'));
    $base = ROOT . DIRECTORY_SEPARATOR . get_plugin_dir(debug_backtrace()[$key]['file']);
    $base_path = str_replace("views\\", "", $base);
    return $base_path . $path;
}

function get_plugin_dir(string $filepath): string {
    if (preg_match('#plugins[/\\\\]([^/\\\\]+)#', $filepath, $matches)) {
        return 'plugins' . DIRECTORY_SEPARATOR . $matches[1] . DIRECTORY_SEPARATOR;
    }
    return '';
}

function plugin_id(): string {
    $called_from = debug_backtrace();
    $ikey = array_search(__FUNCTION__, array_column($called_from, 'function'));
    $path = get_plugin_dir(debug_backtrace()[$ikey]['file']) . 'config.json';
    $json = load_json_file($path);
    return $json->id ?? '';
}

function user_can(?string $permission): ?bool {
    if (empty($permission)) return true;

    $ses = new \Core\Session;

    if ($permission == 'logged_in') {
        return $ses->is_logged_in();
    }

    if ($permission == 'not_logged_in') {
        return !$ses->is_logged_in();
    }

    if ($ses->is_admin()) return true;

    global $APP;
    $APP['user_permissions'] ??= [];

    $APP['user_permissions'] = do_filter('user_permissions', $APP['user_permissions']);

    return in_array('all', $APP['user_permissions']) || in_array($permission, $APP['user_permissions']);
}

function old_value(string $key, string $default = '', string $type = 'post'): string {
    $session = new \Core\Session();
    return $session->oldValue($key, $default, $type);
}

function old_select(string $key, string $default = '', string $type = 'post'): string {
    return old_value($key, $default, $type);
}

function old_checked(string $key, string $default = '', string $type = 'post'): string {
    $value = old_value($key, $default, $type);
    return (isset($_POST[$key]) || isset($_GET[$key]) || $value) ? 'checked' : '';
}

function save_old_values_to_session() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $_SESSION['old_values'] = $_POST;
    }
}

function csrf() {
    $session = new \Core\Session();
    return $session->generateCSRFToken();
}

function csrf_verify($token) {
    $session = new \Core\Session();
    return $session->validateCSRFToken($token);
}

function plugin_exists($plugin_name) {
    $plugin_dir = __DIR__ . '/plugins/' . $plugin_name;
    return is_dir($plugin_dir);
}

function sort_plugins($plugins) {
    sort($plugins, SORT_NATURAL | SORT_FLAG_CASE);
    return $plugins;
}

function get_image(?string $path = '', string $type = 'post') {
    if (file_exists($path)) {
        return ROOT . '/' . $path;
    }

    return match ($type) {
        'male'   => ROOT . '/assets/images/user_male.jpg',
        'female' => ROOT . '/assets/images/user_female.jpg',
        default  => ROOT . '/assets/images/no_image.jpg',
    };
}
