<?php
require_once __DIR__.'/Config.php';
/**
 * Lớp kết nối Database hỗ trợ đa môi trường (Cross-platform Database Helper)
 * - APP_ENV = 'local': Sử dụng PowerShell làm cầu nối đến SQL Server (không cần pdo_sqlsrv)
 * - APP_ENV = 'production': Sử dụng PDO kết nối đến MySQL (dành cho hosting InfinityFree / Linux)
 */

class Database {
    /** @var string */
    private $connType; // 'mysql' hoặc 'mssql_ps'
    
    // MySQL (PDO) properties
    /** @var PDO|null */
    private $dbh;
    /** @var PDOStatement|null */
    private $stmt;
    
    // MS SQL Server (PowerShell) properties
    /** @var string|null */
    private $sql;
    /** @var array */
    private $params = [];
    /** @var array|null */
    private $resultData = null;
    /** @var int|string|null */
    private $lastInsertedId = null;
    /** @var int */
    private $affectedRows = 0;
    
    public function __construct() {
        $this->connType = defined('DB_TYPE') ? DB_TYPE : (PHP_OS_FAMILY === 'Windows' ? 'mssql_ps' : 'mysql');
        
        if ($this->connType === 'mysql') {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $options = [
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];
            try {
                $this->dbh = new PDO($dsn, DB_USER, DB_PASS, $options);
                $this->dbh->exec("SET time_zone = '+07:00'");
                $this->runAutoMigration();
            } catch (PDOException $e) {
                echo "<div style='font-family: sans-serif; padding: 25px; margin: 20px auto; max-width: 800px; border: 2px solid #e74c3c; border-radius: 8px; background: #fff5f5; color: #333;'>";
                echo "<h2 style='color: #c0392b; margin-top: 0;'>❌ Lỗi kết nối cơ sở dữ liệu (Database Connection Error)</h2>";
                echo "<p style='font-size: 15px;'><b>Chi tiết:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<div style='background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-top: 15px;'>";
                echo "<p style='margin: 0 0 10px 0;'><b>Thông số hiện tại:</b></p>";
                echo "<ul>";
                echo "<li><b>DB_HOST:</b> " . htmlspecialchars(DB_HOST) . "</li>";
                echo "<li><b>DB_NAME:</b> " . htmlspecialchars(DB_NAME) . "</li>";
                echo "<li><b>DB_USER:</b> " . htmlspecialchars(DB_USER) . "</li>";
                echo "</ul>";
                echo "<p style='color: #666; font-size: 13px; margin-bottom: 0;'>👉 Hãy kiểm tra lại thông số kết nối MySQL (Hostname, Username, Password, Database Name) trong file <code>.env</code> hoặc <code>core/Config.php</code>.</p>";
                echo "</div>";
                echo "</div>";
                exit;
            }
        }
    }
    
    /**
     * Tự động chạy di cư CSDL nếu thiếu bảng/cột trên MySQL
     */
    private function runAutoMigration() {
        if ($this->connType !== 'mysql') return;
        
        try {
            // 1. Tạo bảng settings nếu chưa tồn tại
            $this->dbh->exec("CREATE TABLE IF NOT EXISTS settings (
                `key` VARCHAR(100) PRIMARY KEY,
                `value` LONGTEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
            
            // Chèn giá trị mặc định cho flash_sale_end nếu chưa có
            $stmt = $this->dbh->prepare("SELECT `key` FROM settings WHERE `key` = 'flash_sale_end'");
            $stmt->execute();
            if (!$stmt->fetch()) {
                $stmtIns = $this->dbh->prepare("INSERT INTO settings (`key`, `value`) VALUES ('flash_sale_end', :val)");
                $stmtIns->execute([':val' => date('Y-m-d 23:59:59')]);
            }
            
            // 2. Thêm cột is_best_selling và is_newest vào bảng products nếu chưa có
            $stmtCol = $this->dbh->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'is_best_selling'");
            $stmtCol->execute([':db' => DB_NAME]);
            if (!$stmtCol->fetch()) {
                $this->dbh->exec("ALTER TABLE products ADD COLUMN is_best_selling INT DEFAULT 0;");
            }
            
            $stmtCol2 = $this->dbh->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'is_newest'");
            $stmtCol2->execute([':db' => DB_NAME]);
            if (!$stmtCol2->fetch()) {
                $this->dbh->exec("ALTER TABLE products ADD COLUMN is_newest INT DEFAULT 0;");
            }
            
            // 3. Tạo bảng newsletter_subscribers nếu chưa tồn tại
            $this->dbh->exec("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(100) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
            
            // 4. Tạo bảng notifications nếu chưa tồn tại
            $this->dbh->exec("CREATE TABLE IF NOT EXISTS notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                type VARCHAR(50) DEFAULT 'system',
                is_read INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
            
            // 5. Thêm cột avatar vào bảng users nếu chưa có
            $stmtColAvatar = $this->dbh->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = 'users' AND COLUMN_NAME = 'avatar'");
            $stmtColAvatar->execute([':db' => DB_NAME]);
            if (!$stmtColAvatar->fetch()) {
                $this->dbh->exec("ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT NULL;");
            }

            // 6. Cập nhật email admin thành vuhuan123890@gmail.com
            $this->dbh->exec("UPDATE users SET email = 'vuhuan123890@gmail.com' WHERE (id = 1 OR username = 'admin') AND email != 'vuhuan123890@gmail.com';");
            
            // 7. Khởi tạo cấu hình mặc định nếu chưa có
            $defaultSettings = [
                'shop_hotline' => '0336.120.405',
                'shop_email' => 'vuhuan123890@gmail.com',
                'shop_address' => '100+ Cửa hàng toàn quốc',
                'shop_facebook' => 'https://facebook.com',
                'shipping_fee_standard' => '30000',
                'shipping_fee_free_amount' => '500000',
            ];
            foreach ($defaultSettings as $k => $v) {
                $stmtCheck = $this->dbh->prepare("SELECT `key` FROM settings WHERE `key` = :k");
                $stmtCheck->execute([':k' => $k]);
                if (!$stmtCheck->fetch()) {
                    $stmtIns = $this->dbh->prepare("INSERT INTO settings (`key`, `value`) VALUES (:k, :v)");
                    $stmtIns->execute([':k' => $k, ':v' => $v]);
                }
            }
            
        } catch (PDOException $e) {
            error_log("Database Auto-Migration Error: " . $e->getMessage());
        }
    }
    
    /**
     * Nhận lệnh SQL
     * @param string $sql
     * @return $this
     */
    public function query($sql) {
        if ($this->connType === 'mysql') {
            $this->stmt = $this->dbh->prepare($sql);
        } else {
            $this->sql = $this->translateQuery($sql);
            $this->params = [];
            $this->resultData = null;
        }
        return $this;
    }
    
    /**
     * Bind parameter an toàn
     * @param string $param
     * @param mixed $value
     * @param int|null $type
     * @return $this
     */
    public function bind($param, $value, $type = null) {
        if ($this->connType === 'mysql') {
            if (is_null($type)) {
                switch (true) {
                    case is_int($value):
                        $type = PDO::PARAM_INT;
                        break;
                    case is_bool($value):
                        $type = PDO::PARAM_BOOL;
                        break;
                    case is_null($value):
                        $type = PDO::PARAM_NULL;
                        break;
                    default:
                        $type = PDO::PARAM_STR;
                }
            }
            $this->stmt->bindValue($param, $value, $type);
        } else {
            if (is_numeric($value) && strval(intval($value)) === strval($value)) {
                $value = intval($value);
            }
            $this->params[$param] = [
                'value' => $value,
                'type' => $type
            ];
        }
        return $this;
    }
    
    /**
     * Thực thi statement
     */
    public function execute() {
        if ($this->connType === 'mysql') {
            try {
                return $this->stmt->execute();
            } catch (PDOException $e) {
                error_log("MySQL Query Error: " . $e->getMessage());
                return false;
            }
        } else {
            $finalSql = $this->buildFinalQuery();
            $finalSqlWithMeta = $this->prepareQueryForMetadata($finalSql);
            
            $jsonResult = $this->runPowerShell($finalSqlWithMeta);
            
            if ($jsonResult === null) {
                return false;
            }
            
            // Trích xuất phần chuỗi JSON sạch (loại bỏ cảnh báo hoặc module loading ở đầu nếu có)
            $startPos = strpos($jsonResult, '[');
            $endPos = strrpos($jsonResult, ']');
            if ($startPos !== false && $endPos !== false && $endPos > $startPos) {
                $jsonResult = substr($jsonResult, $startPos, $endPos - $startPos + 1);
            } else {
                $startPos = strpos($jsonResult, '{');
                $endPos = strrpos($jsonResult, '}');
                if ($startPos !== false && $endPos !== false && $endPos > $startPos) {
                    $jsonResult = '[' . substr($jsonResult, $startPos, $endPos - $startPos + 1) . ']';
                } else {
                    $jsonResult = '[]';
                }
            }
            
            $data = json_decode($jsonResult, true);
            if (!is_array($data)) {
                $data = [];
            }
            
            $this->resultData = $data;
            
            // Phân tích kết quả metadata nếu có
            if (count($data) > 0 && isset($data[0]['affected_rows'])) {
                $this->affectedRows = intval($data[0]['affected_rows']);
                if (isset($data[0]['last_id']) && $data[0]['last_id'] !== null) {
                    $this->lastInsertedId = intval($data[0]['last_id']);
                }
            } else {
                $this->affectedRows = count($data);
            }
            
            return true;
        }
    }
    
    /**
     * Lấy một hàng
     */
    public function single() {
        if ($this->connType === 'mysql') {
            $this->execute();
            return $this->stmt->fetch();
        } else {
            if ($this->resultData === null) {
                $this->execute();
            }
            return (is_array($this->resultData) && count($this->resultData) > 0) ? $this->resultData[0] : false;
        }
    }
    
    /**
     * Lấy tất cả hàng
     */
    public function resultSet() {
        if ($this->connType === 'mysql') {
            $this->execute();
            return $this->stmt->fetchAll();
        } else {
            if ($this->resultData === null) {
                $this->execute();
            }
            return is_array($this->resultData) ? $this->resultData : [];
        }
    }
    
    /**
     * Đếm số hàng bị ảnh hưởng
     */
    public function rowCount() {
        if ($this->connType === 'mysql') {
            return $this->stmt->rowCount();
        } else {
            return $this->affectedRows;
        }
    }
    
    /**
     * Lấy ID vừa insert
     */
    public function lastInsertId() {
        if ($this->connType === 'mysql') {
            return $this->dbh->lastInsertId();
        } else {
            return $this->lastInsertedId;
        }
    }
    
    /**
     * Transactions
     */
    public function beginTransaction() {
        if ($this->connType === 'mysql') {
            return $this->dbh->beginTransaction();
        }
        return true;
    }
    
    public function commit() {
        if ($this->connType === 'mysql') {
            return $this->dbh->commit();
        }
        return true;
    }
    
    public function rollBack() {
        if ($this->connType === 'mysql') {
            return $this->dbh->rollBack();
        }
        return true;
    }
    
    /**
     * Tự động biên dịch cú pháp MySQL sang SQL Server (T-SQL)
     * @param string $sql
     * @return string
     */
    private function translateQuery($sql) {
        // 1. Chuyển đổi NOW() thành CURRENT_TIMESTAMP
        $sql = str_replace('NOW()', 'CURRENT_TIMESTAMP', $sql);
        
        // 2. Chuyển đổi RAND() thành NEWID() để order ngẫu nhiên
        $sql = str_replace('RAND()', 'NEWID()', $sql);
        
        // 3. Chuyển đổi LIMIT & OFFSET sang SQL Server OFFSET/FETCH
        if (preg_match('/LIMIT\s+(:\w+|\d+)\s+OFFSET\s+(:\w+|\d+)/i', $sql, $matches)) {
            $limit = $matches[1];
            $offset = $matches[2];
            $sql = preg_replace('/LIMIT\s+(:\w+|\d+)\s+OFFSET\s+(:\w+|\d+)/i', "OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY", $sql);
        }
        elseif (preg_match('/LIMIT\s+(:\w+|\d+)/i', $sql, $matches)) {
            $limit = $matches[1];
            $sql = preg_replace('/LIMIT\s+(:\w+|\d+)/i', "OFFSET 0 ROWS FETCH NEXT $limit ROWS ONLY", $sql);
        }
        
        return $sql;
    }
    
    /**
     * Xây dựng câu truy vấn hoàn chỉnh sau khi bind parameter an toàn (chống SQL Injection)
     * @return string
     */
    private function buildFinalQuery() {
        $sql = $this->sql;
        
        if (empty($this->params)) {
            return $sql;
        }
        
        uksort($this->params, function($a, $b) {
            return strlen(strval($b)) - strlen(strval($a));
        });
        
        foreach ($this->params as $param => $data) {
            $value = $data['value'];
            
            $escaped = 'NULL';
            if ($value !== null) {
                if (is_int($value) || is_float($value)) {
                    $escaped = $value;
                } elseif (is_bool($value)) {
                    $escaped = $value ? 1 : 0;
                } else {
                    $escaped = "N'" . str_replace("'", "''", strval($value)) . "'";
                }
            }
            
            $sql = str_replace($param, $escaped, $sql);
        }
        
        return $sql;
    }
    
    /**
     * Thêm lệnh lấy ID vừa chèn hoặc số dòng bị ảnh hưởng cho các lệnh INSERT/UPDATE/DELETE
     * @param string $sql
     * @return string
     */
    private function prepareQueryForMetadata($sql) {
        $trimmed = trim($sql);
        if (stripos($trimmed, 'INSERT INTO') === 0) {
            return $sql . "; SELECT SCOPE_IDENTITY() AS last_id, @@ROWCOUNT AS affected_rows;";
        } elseif (stripos($trimmed, 'UPDATE') === 0 || stripos($trimmed, 'DELETE') === 0) {
            return $sql . "; SELECT @@ROWCOUNT AS affected_rows;";
        }
        return $sql;
    }
    
    /**
     * Chạy PowerShell ngầm để truy vấn SQL Server
     * @param string $query
     * @return string|null
     */
    private function runPowerShell($query) {
        $sqlBase64 = base64_encode($query);
        
        $psScript = "[Console]::OutputEncoding = [System.Text.Encoding]::UTF8;
\$OutputEncoding = [System.Text.Encoding]::UTF8;
\$ProgressPreference = 'SilentlyContinue';
[System.Reflection.Assembly]::LoadWithPartialName('System.Data') | Out-Null;
\$conn = New-Object System.Data.SqlClient.SqlConnection;
\$conn.ConnectionString = 'Server=" . DB_HOST . ";Database=" . DB_NAME . ";Integrated Security=True;TrustServerCertificate=True';
try {
    \$conn.Open();
    \$cmd = \$conn.CreateCommand();
    \$cmd.CommandText = [System.Text.Encoding]::UTF8.GetString([System.Convert]::FromBase64String('" . $sqlBase64 . "'));
    \$adapter = New-Object System.Data.SqlClient.SqlDataAdapter(\$cmd);
    \$dt = New-Object System.Data.DataTable;
    \$adapter.Fill(\$dt) | Out-Null;
    \$conn.Close();
    if (\$dt.Columns.Count -gt 0) {
        \$results = foreach (\$r in \$dt.Rows) {
            \$obj = [ordered]@{}
            foreach (\$col in \$dt.Columns) {
                \$val = \$r[\$col.ColumnName]
                if (\$val -is [System.DBNull]) {
                    \$obj[\$col.ColumnName] = \$null
                } elseif (\$val -is [System.DateTime]) {
                    \$obj[\$col.ColumnName] = \$val.ToString('yyyy-MM-dd HH:mm:ss')
                } else {
                    \$obj[\$col.ColumnName] = \$val
                }
            }
            [PSCustomObject]\$obj
        }
        if (\$results -eq \$null) {
            '[]'
        } else {
            \$json = @(\$results) | ConvertTo-Json -Compress
            if (\$results.Count -eq 1 -or -not (\$results -is [System.Array])) {
                \$json = \"[\$json]\"
            }
            \$json
        }
    } else {
        '[]'
    }
} catch {
    Write-Output \"ERROR: \$(\$_.Exception.Message)\";
    if (\$conn.State -eq 'Open') { \$conn.Close(); }
    exit 1;
}";

        $utf16Script = iconv('UTF-8', 'UTF-16LE', $psScript);
        $encodedScript = base64_encode($utf16Script);
        
        $cmd = "powershell -NoProfile -NonInteractive -EncodedCommand " . $encodedScript . " 2>&1";
        
        $descriptorspec = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"]
        ];
        
        $process = proc_open($cmd, $descriptorspec, $pipes);
        
        if (is_resource($process)) {
            fclose($pipes[0]);
            
            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            
            $returnValue = proc_close($process);
            
            if ($returnValue !== 0) {
                error_log("SQL Server PowerShell Error: " . $stdout);
                return null;
            }
            
            return $stdout;
        }
        
        return null;
    }
}
?>
