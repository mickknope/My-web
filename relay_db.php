<?php
require_once __DIR__ . '/config.php';

function relayEnsureTable($conn) {
    $createSql = "CREATE TABLE IF NOT EXISTS relay_status (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sw1 TINYINT(1) NOT NULL DEFAULT 0,
        sw2 TINYINT(1) NOT NULL DEFAULT 0,
        ldr INT NOT NULL DEFAULT 0,
        ry1 TINYINT(1) NOT NULL DEFAULT 0,
        ry2 TINYINT(1) NOT NULL DEFAULT 0,
        ry3 TINYINT(1) NOT NULL DEFAULT 0,
        ry4 TINYINT(1) NOT NULL DEFAULT 0,
        ry5 TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if (!$conn->query($createSql)) {
        throw new Exception("Unable to create relay_status table: " . $conn->error);
    }

    $columns = ['sw1', 'sw2', 'ldr', 'ry1', 'ry2', 'ry3', 'ry4', 'ry5', 'created_at'];
    foreach ($columns as $column) {
        $check = $conn->query("SHOW COLUMNS FROM relay_status LIKE '$column'");
        if ($check && $check->num_rows === 0) {
            $alterSql = '';
            switch ($column) {
                case 'sw1':
                case 'sw2':
                    $alterSql = "ALTER TABLE relay_status ADD COLUMN $column TINYINT(1) NOT NULL DEFAULT 0";
                    break;
                case 'ldr':
                    $alterSql = "ALTER TABLE relay_status ADD COLUMN $column INT NOT NULL DEFAULT 0";
                    break;
                case 'ry1':
                case 'ry2':
                case 'ry3':
                case 'ry4':
                case 'ry5':
                    $alterSql = "ALTER TABLE relay_status ADD COLUMN $column TINYINT(1) NOT NULL DEFAULT 0";
                    break;
                case 'created_at':
                    $alterSql = "ALTER TABLE relay_status ADD COLUMN $column TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
                    break;
            }

            if ($alterSql !== '' && !$conn->query($alterSql)) {
                throw new Exception("Unable to add column $column: " . $conn->error);
            }
        }
    }
}

try {
    relayEnsureTable($conn);
} catch (Exception $e) {
    die($e->getMessage());
}
?>
