<?php
/**
 * Debug Script - Test Login Issues
 */
require_once 'config/db.php';

echo "<h2>🔍 Database Debug Test</h2>";
echo "<hr>";

// Test 1: Check connection
echo "<h4>1. Database Connection</h4>";
if ($conn->connect_error) {
    echo "<p style='color:red;'>❌ Connection failed: " . $conn->connect_error . "</p>";
} else {
    echo "<p style='color:green;'>✅ Connected to: " . DB_NAME . "</p>";
}

// Test 2: Check if users table exists and has data
echo "<h4>2. Users in Database</h4>";
$query = "SELECT user_id, full_name, email, role FROM users";
$result = $conn->query($query);

if (!$result) {
    echo "<p style='color:red;'>❌ Error: " . $conn->error . "</p>";
} else {
    if ($result->num_rows > 0) {
        echo "<p style='color:green;'>✅ Found " . $result->num_rows . " users</p>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['user_id'] . "</td>";
            echo "<td>" . $row['full_name'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['role'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:orange;'>⚠️ No users found in database. You need to import setup.sql</p>";
    }
}

// Test 3: Test password verification
echo "<h4>3. Password Hash Test</h4>";
$test_email = 'admin@pcparts.local';
$test_password = 'admin123';

$query = "SELECT password_hash FROM users WHERE email = '$test_email' LIMIT 1";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hash = $row['password_hash'];
    
    echo "<p>Testing: <strong>$test_email</strong> with password <strong>$test_password</strong></p>";
    echo "<p>Hash in DB: <code>$hash</code></p>";
    
    if (password_verify($test_password, $hash)) {
        echo "<p style='color:green;'>✅ Password verification SUCCESS</p>";
    } else {
        echo "<p style='color:red;'>❌ Password verification FAILED</p>";
        echo "<p><strong>This is the problem!</strong> The hash or password is wrong.</p>";
    }
} else {
    echo "<p style='color:red;'>❌ Admin account not found</p>";
}

// Test 4: Generate correct bcrypt hashes
echo "<h4>4. Generate Fresh Bcrypt Hashes</h4>";
$passwords = [
    'admin@pcparts.local' => 'admin123',
    'staff@pcparts.local' => 'staff123',
    'john@example.com' => 'customer123',
    'jane@example.com' => 'customer123'
];

echo "<p>Here are valid bcrypt hashes:</p>";
echo "<pre>";
foreach ($passwords as $email => $password) {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    echo "$email: $hash\n";
}
echo "</pre>";

?>
<hr>
<h4>Next Steps</h4>
<ol>
<li>If users table is empty → Import setup.sql via phpMyAdmin</li>
<li>If password hash test failed → Run the SQL below to update hashes</li>
</ol>

<h4>Fix: Update Password Hashes</h4>
<p>Copy and run this SQL in phpMyAdmin:</p>
<pre>
UPDATE users SET password_hash = '$2y$10$n9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P6kFDm' WHERE email = 'admin@pcparts.local';
UPDATE users SET password_hash = '$2y$10$PIxl3lQK5QrNqXVJqLT5Ce2T2aDSr1g9W9JjlS2qTVQJJYL3/lIIC' WHERE email = 'staff@pcparts.local';
UPDATE users SET password_hash = '$2y$10$LjGF.CqHvC7x7vXVDPPd0Oa1TjA6nPrRFvGvp8YSGR1S6hXXCJ5tG' WHERE email = 'john@example.com';
UPDATE users SET password_hash = '$2y$10$LjGF.CqHvC7x7vXVDPPd0Oa1TjA6nPrRFvGvp8YSGR1S6hXXCJ5tG' WHERE email = 'jane@example.com';
</pre>
