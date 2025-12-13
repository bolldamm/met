<?php
/**
 * Verifacti Log Viewer
 * Displays the Verifacti API interaction log for admin review
 *
 * Loaded via main_app.php?section=invoice&action=verifacti_log
 */

// Note: Authentication already handled by main_app.php

// Get requested month (default to current)
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

// Validate month format
if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
    $month = date('Y-m');
}

// Build log file path
$logDir = dirname(__FILE__) . "/../../../logs";
$logFile = $logDir . "/verifacti_" . $month . ".log";

// Get available log files for dropdown
$availableLogs = [];
if (is_dir($logDir)) {
    $files = glob($logDir . "/verifacti_*.log");
    foreach ($files as $file) {
        if (preg_match('/verifacti_(\d{4}-\d{2})\.log$/', $file, $matches)) {
            $availableLogs[] = $matches[1];
        }
    }
    rsort($availableLogs); // Most recent first
}

// Read log content
$logContent = "";
$lineCount = 0;
if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES);
    $lineCount = count($lines);
    // Reverse to show newest first
    $lines = array_reverse($lines);
    $logContent = implode("\n", $lines);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Verifacti Log - <?php echo htmlspecialchars($month); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .controls {
            margin-bottom: 15px;
            padding: 10px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .controls select, .controls button {
            padding: 8px 12px;
            font-size: 14px;
        }
        .stats {
            color: #666;
            margin-bottom: 15px;
        }
        .log-container {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            max-height: 70vh;
            overflow-y: auto;
        }
        .log-content {
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 12px;
            line-height: 1.5;
            white-space: pre-wrap;
            word-wrap: break-word;
            margin: 0;
        }
        .empty-message {
            color: #888;
            font-style: italic;
            padding: 20px;
            text-align: center;
        }
        .refresh-btn {
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .refresh-btn:hover {
            background: #45a049;
        }
        .back-link {
            margin-bottom: 15px;
        }
        .back-link a {
            color: #337ab7;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="back-link">
        <a href="main_app.php?section=invoice&action=view">&larr; Back to Invoice List</a>
    </div>

    <h1>Verifacti Log</h1>

    <div class="controls">
        <form method="get" style="display: inline;">
            <input type="hidden" name="section" value="invoice">
            <input type="hidden" name="action" value="verifacti_log">
            <label for="month">Month: </label>
            <select name="month" id="month" onchange="this.form.submit()">
                <?php if (empty($availableLogs)): ?>
                    <option value="<?php echo date('Y-m'); ?>"><?php echo date('Y-m'); ?> (no logs yet)</option>
                <?php else: ?>
                    <?php foreach ($availableLogs as $logMonth): ?>
                        <option value="<?php echo $logMonth; ?>" <?php echo $logMonth === $month ? 'selected' : ''; ?>>
                            <?php echo $logMonth; ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </form>
        <button class="refresh-btn" onclick="location.reload();">Refresh</button>
    </div>

    <div class="stats">
        <?php if (file_exists($logFile)): ?>
            <?php echo $lineCount; ?> log entries | Showing newest first |
            File: verifacti_<?php echo htmlspecialchars($month); ?>.log
        <?php else: ?>
            No log file for <?php echo htmlspecialchars($month); ?>
        <?php endif; ?>
    </div>

    <div class="log-container">
        <?php if (!empty($logContent)): ?>
            <pre class="log-content"><?php echo htmlspecialchars($logContent); ?></pre>
        <?php else: ?>
            <div class="empty-message">No log entries for this month.</div>
        <?php endif; ?>
    </div>
</body>
</html>
