<?php
/**
 * Generate correct bcrypt hashes for test accounts
 */

// Generate hashes for each password
$hashes = [
    'admin123' => password_hash('admin123', PASSWORD_BCRYPT),
    'staff123' => password_hash('staff123', PASSWORD_BCRYPT),
    'customer123' => password_hash('customer123', PASSWORD_BCRYPT),
];

echo "<h2>✅ Correct Bcrypt Hashes</h2>";
echo "<p>Use these SQL statements to fix the database:</p>";
echo "<pre>";
echo "UPDATE users SET password_hash = '" . $hashes['admin123'] . "' WHERE email = 'admin@pcparts.local';\n";
echo "UPDATE users SET password_hash = '" . $hashes['staff123'] . "' WHERE email = 'staff@pcparts.local';\n";
echo "UPDATE users SET password_hash = '" . $hashes['customer123'] . "' WHERE email = 'john@example.com';\n";
echo "UPDATE users SET password_hash = '" . $hashes['customer123'] . "' WHERE email = 'jane@example.com';\n";
echo "</pre>";

// Verify they work
echo "<h3>Verification:</h3>";
echo "<p>password_verify('admin123', '" . $hashes['admin123'] . "'): ";
echo (password_verify('admin123', $hashes['admin123']) ? "✅ TRUE" : "❌ FALSE") . "</p>";

echo "<p>password_verify('staff123', '" . $hashes['staff123'] . "'): ";
echo (password_verify('staff123', $hashes['staff123']) ? "✅ TRUE" : "❌ FALSE") . "</p>";

echo "<p>password_verify('customer123', '" . $hashes['customer123'] . "'): ";
echo (password_verify('customer123', $hashes['customer123']) ? "✅ TRUE" : "❌ FALSE") . "</p>";

?>
