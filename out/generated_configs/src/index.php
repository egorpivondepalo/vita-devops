<?php
if (isset($_POST['add_record'])) {
    try {
        $pdo = new PDO(
            'pgsql:host=postgres;dbname=dev_db', 
            'dev_user', 
            ''
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->execute([$name, $email]);
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
        
    } catch (PDOException $e) {
        $error_message = "Error in query: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Тестовая страница PHP + PostgreSQL</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        form { margin: 10px 0; }
        input[type="text"] { padding: 8px; margin: 5px; }
        button { padding: 8px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>PHP + PostgreSQL Test</h1>

    <?php if (isset($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="section">
        <h2>Server Info</h2>
        <?php
        echo "<p><strong>PHP version:</strong> " . phpversion() . "</p>";
        echo "<p><strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
        echo "<p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
        ?>
    </div>

    <div class="section">
        <h2>Add string</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="email" placeholder="Email" required>
            <button type="submit" name="add_record">Add</button>
        </form>
    </div>

    <div class="section">
        <h2>DB table</h2>
        <?php
        try {
            $pdo = new PDO(
                'pgsql:host=postgres;dbname=dev_db', 
                'dev_user', 
                ''
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id SERIAL PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");

            $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($records) > 0) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Имя</th><th>Email</th><th>Дата</th></tr>";
                foreach ($records as $row) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . $row['created_at'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No data in table</p>";
            }

        } catch (PDOException $e) {
            echo "<p class='error'>Connection to postgresl error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>PostgreSQL Info</h2>
        <?php
        try {
            if (isset($pdo)) {
                $version = $pdo->query("SELECT version()")->fetchColumn();
                echo "<p><strong>PostgreSQL version:</strong> " . $version . "</p>";
                
                $dbSize = $pdo->query("SELECT pg_size_pretty(pg_database_size('dev_db'))")->fetchColumn();
                echo "<p><strong>DB size:</strong> " . $dbSize . "</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>Error load info PostgreSQL</p>";
        }
        ?>
    </div>
</body>
</html>