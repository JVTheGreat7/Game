<?php
/**
 * COMPREHENSIVE LOGIN DEBUG TEST
 * This will show exactly what's wrong
 */
require_once 'config/db.php';
require_once 'includes/auth.php';

echo "<h1>🔍 LOGIN DEBUG TEST</h1>";
echo "<hr>";

// Test the exact login flow
$test_email = 'admin@pcparts.local';
$test_password = 'admin123';

echo "<h2>Test: Logging in as admin@pcparts.local / admin123</h2>";
echo "<hr>";

// Step 1: Sanitize
echo "<h3>Step 1: Email Sanitization</h3>";
$sanitized_email = sanitize($test_email, $conn);
echo "<p>Original: <code>$test_email</code></p>";
echo "<p>Sanitized: <code>$sanitized_email</code></p>";
echo "<p>Match: " . ($test_email === $sanitized_email ? "✅ YES" : "❌ NO") . "</p>";

// Step 2: Query user
echo "<h3>Step 2: Query User from Database</h3>";
$query = "SELECT user_id, full_name, email, password_hash, role FROM users WHERE email = '$sanitized_email'";
echo "<p>Query: <code>$query</code></p>";
$result = $conn->query($query);

if (!$result) {
    echo "<p style='color:red;'>❌ Query Error: " . $conn->error . "</p>";
    exit;
}

if ($result->num_rows === 0) {
    echo "<p style='color:red;'>❌ User not found!</p>";
    echo "<p>Trying to find any user with 'admin':</p>";
    $debug_query = "SELECT user_id, email, role FROM users WHERE email LIKE '%admin%'";
    $debug_result = $conn->query($debug_query);
    if ($debug_result && $debug_result->num_rows > 0) {
        while ($row = $debug_result->fetch_assoc()) {
            echo "<p>Found: " . $row['email'] . " (Role: " . $row['role'] . ")</p>";
        }
    } else {
        echo "<p>No users with 'admin' in email found</p>";
    }
    exit;
}

$user = $result->fetch_assoc();
echo "<p style='color:green;'>✅ User found:</p>";
echo "<pre>";
print_r($user);
echo "</pre>";

// Step 3: Test password verification
echo "<h3>Step 3: Password Verification</h3>";
$hash_from_db = $user['password_hash'];
echo "<p>Hash from DB: <code>$hash_from_db</code></p>";
echo "<p>Testing password: <code>$test_password</code></p>";

$verify_result = password_verify($test_password, $hash_from_db);
echo "<p>password_verify() result: " . ($verify_result ? "✅ TRUE" : "❌ FALSE") . "</p>";

if (!$verify_result) {
    echo "<h4>🚨 HASH MISMATCH - This is the problem!</h4>";
    echo "<p>The password hash in the database does NOT match the password.</p>";
    echo "<p>You need to generate a new hash for this password:</p>";
    $new_hash = password_hash($test_password, PASSWORD_BCRYPT);
    echo "<p>New hash: <code>$new_hash</code></p>";
    echo "<p><strong>Run this SQL in phpMyAdmin:</strong></p>";
    echo "<pre>UPDATE users SET password_hash = '$new_hash' WHERE email = '$test_email';</pre>";
} else {
    echo "<h4>✅ PASSWORD VERIFICATION SUCCESSFUL</h4>";
}

// Step 4: Test actual login function
echo "<h3>Step 4: Test loginUser() Function</h3>";
$login_result = loginUser($conn, $test_email, $test_password);
echo "<p>loginUser() returned: " . ($login_result ? "✅ TRUE" : "❌ FALSE") . "</p>";

if ($login_result) {
    echo "<h4>✅ LOGIN SUCCESSFUL</h4>";
    echo "<p>Session variables set:</p>";
    echo "<pre>";
    echo "user_id: " . $_SESSION['user_id'] . "\n";
    echo "full_name: " . $_SESSION['full_name'] . "\n";
    echo "email: " . $_SESSION['email'] . "\n";
    echo "role: " . $_SESSION['role'] . "\n";
    echo "</pre>";
} else {
    echo "<h4>❌ LOGIN FAILED</h4>";
    echo "<p>This could be because:</p>";
    echo "<ol>";
    echo "<li>Password hash doesn't match</li>";
    echo "<li>Email sanitization is changing the email</li>";
    echo "<li>Database query returned no results</li>";
    echo "</ol>";
}

// Bonus: Show all users
echo "<hr>";
echo "<h3>All Users in Database</h3>";
$all_users = $conn->query("SELECT user_id, full_name, email, role FROM users");
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
while ($u = $all_users->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $u['user_id'] . "</td>";
    echo "<td>" . $u['full_name'] . "</td>";
    echo "<td>" . $u['email'] . "</td>";
    echo "<td>" . $u['role'] . "</td>";
    echo "</tr>";
}
echo "</table>";

?>
<hr>
<h2>📋 Next Steps</h2>
<p>Check the results above to find the exact issue. Then follow one of these fixes:</p>
<h3>Fix 1: If hash doesn't match password</h3>
<p>Run this in phpMyAdmin SQL tab:</p>
<pre>
UPDATE users SET password_hash = '$2y$10$2O3AYT26lXtKTAXg4o/tF.4hHADWaWW7SsQB3GxZmzT4QCc3Dy4RW' WHERE email = 'admin@pcparts.local';
UPDATE users SET password_hash = '$2y$10$QKEKuIx6h9I1w5kJh2LDKehYKyXN5Z/p3R8nKJ5iNa7vNK6M4pN2m' WHERE email = 'staff@pcparts.local';
UPDATE users SET password_hash = '$2y$10$LjGF.CqHvC7x7vXVDPPd0Oa1TjA6nPrRFvGvp8YSGR1S6hXXCJ5tG' WHERE email = 'john@example.com';
UPDATE users SET password_hash = '$2y$10$LjGF.CqHvC7x7vXVDPPd0Oa1TjA6nPrRFvGvp8YSGR1S6hXXCJ5tG' WHERE email = 'jane@example.com';
</pre>

<h3>Fix 2: Generate fresh hashes</h3>
<p>Open: <a href="http://localhost/PC/generate-hashes.php" target="_blank">http://localhost/PC/generate-hashes.php</a></p>
<p>Copy the hashes and run the UPDATE commands.</p>
