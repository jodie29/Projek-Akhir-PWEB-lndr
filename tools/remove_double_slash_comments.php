<?php
// Tool: remove_double_slash_comments.php
// Purpose: Remove full-line comments that start with // from selected files (php, blade.php, js, ts, jsx, tsx)
// Usage: php tools/remove_double_slash_comments.php [--apply] [--ext=php,blade.php,js,ts] [--exclude=vendor,node_modules,storage,public] [--backup-dir=backups/comments_removed]
// WARNING: This will alter files. By default it runs in dry-run mode and DOES NOT modify files. Use --apply to change files.

$root = __DIR__ . DIRECTORY_SEPARATOR . '..';
$argv = $_SERVER['argv'];
array_shift($argv); // drop script name
$options = [];
foreach ($argv as $arg) {
    if (strpos($arg, '--') === 0) {
        $part = substr($arg, 2);
        if (strpos($part, '=') !== false) {
            [$k, $v] = explode('=', $part, 2);
            $options[$k] = $v;
        } else {
            $options[$part] = true;
        }
    }
}

$apply = isset($options['apply']);
$extList = isset($options['ext']) ? explode(',', $options['ext']) : ['php','blade.php','js','ts','jsx','tsx'];
$excludeDirs = isset($options['exclude']) ? explode(',', $options['exclude']) : ['vendor','node_modules','storage','public','backups','.git'];
$backupDir = isset($options['backup-dir']) ? $options['backup-dir'] : 'backups/comments_removed';
$root = realpath($root);
$report = ['files' => []];

function isExcluded($path, $excludeDirs) {
    foreach ($excludeDirs as $d) {
        if (strpos($path, DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR) !== false) return true;
        if (substr($path, -strlen(DIRECTORY_SEPARATOR . $d)) === DIRECTORY_SEPARATOR . $d) return true;
    }
    return false;
}

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
foreach ($iterator as $file) {
    if (!$file->isFile()) continue;
    $filePath = $file->getRealPath();
    $rel = substr($filePath, strlen($root) + 1);
    if (isExcluded($rel, $excludeDirs)) continue;
    // Match extensions
    foreach ($extList as $ext) {
        if (substr($ext, 0, 1) === '.') $ext = substr($ext, 1);
        if (strtolower(substr($rel, -strlen($ext))) === strtolower($ext)) {
            // Check if this file contains any comment lines
            $lines = file($filePath, FILE_IGNORE_NEW_LINES);
            $modified = false;
            $newLines = [];
            $countRemoved = 0;
            foreach ($lines as $line) {
                if (preg_match('/^\s*\/\//', $line)) {
                    $modified = true;
                    $countRemoved++;
                    continue; // drop
                }
                $newLines[] = $line;
            }
            if ($modified) {
                $report['files'][] = ['path' => $rel, 'removed' => $countRemoved];
                if ($apply) {
                    // create backup dir
                    $targetBackup = $root . DIRECTORY_SEPARATOR . $backupDir . DIRECTORY_SEPARATOR . dirname($rel);
                    if (!is_dir($targetBackup)) mkdir($targetBackup, 0777, true);
                    copy($filePath, $targetBackup . DIRECTORY_SEPARATOR . basename($rel));
                    // write new content
                    file_put_contents($filePath, implode(PHP_EOL, $newLines));
                }
            }
            break;
        }
    }
}

// Print summary
$files = count($report['files']);
$totalRemoved = array_reduce($report['files'], function($carry, $item) { return $carry + $item['removed'];}, 0);
printf("Summary: %d files would be modified / %d comment lines removed\n", $files, $totalRemoved);
if ($files > 0) {
    printf("Top 20 files (path -> removed):\n");
    $i = 0;
    foreach ($report['files'] as $item) {
        printf("%s -> %d\n", $item['path'], $item['removed']);
        $i++;
        if ($i >= 20) break;
    }
}
if ($apply) {
    printf("Applied changes. Backups saved to %s\n", $backupDir);
} else {
    printf("This was a dry-run. To apply changes, run with --apply (and ensure you have backups or commit first).\n");
}

// Save report file
$reportPath = $root . DIRECTORY_SEPARATOR . ($apply ? 'backups' . DIRECTORY_SEPARATOR . 'comments_removed_report.json' : 'backups' . DIRECTORY_SEPARATOR . 'comments_removed_dryrun_report.json');
if (!is_dir(dirname($reportPath))) mkdir(dirname($reportPath), 0777, true);
file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT));
printf("Report saved to %s\n", $reportPath);

return 0;
