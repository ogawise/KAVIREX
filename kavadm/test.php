<?php
// test_environment.php
header('Content-Type: text/plain');
echo "PHP Version: ".phpversion()."\n\n";

// Test with known good hash
$knownGoodHash = '$2y$10$N9qo8uLOickgx2ZMRZoMy.Mrq6PHiG5rC9FgWUZ4T7W7LbQ3Yq9Xe';
$test1 = password_verify('eyasharon', $knownGoodHash);
echo "Test with known hash: ".($test1 ? '✅ SUCCESS' : '❌ FAILED')."\n";

// Test with newly generated hash
$newHash = password_hash('eyasharon', PASSWORD_BCRYPT);
$test2 = password_verify('eyasharon', $newHash);
echo "Test with new hash: ".($test2 ? '✅ SUCCESS' : '❌ FAILED')."\n";
echo "New hash for DB: $newHash\n";

// Test with database retrieval simulation
$mockFromDB = '$2y$10$N9qo8uLOickgx2ZMRZoMy.Mrq6PHiG5rC9FgWUZ4T7W7LbQ3Yq9Xe';
$test3 = password_verify('eyasharon', trim($mockFromDB));
echo "Test with trimmed hash: ".($test3 ? '✅ SUCCESS' : '❌ FAILED')."\n";
?><?php
// test_environment.php
header('Content-Type: text/plain');
echo "PHP Version: ".phpversion()."\n\n";

// Test with known good hash
$knownGoodHash = '$2y$10$N9qo8uLOickgx2ZMRZoMy.Mrq6PHiG5rC9FgWUZ4T7W7LbQ3Yq9Xe';
$test1 = password_verify('eyasharon', $knownGoodHash);
echo "Test with known hash: ".($test1 ? '✅ SUCCESS' : '❌ FAILED')."\n";

// Test with newly generated hash
$newHash = password_hash('eyasharon', PASSWORD_BCRYPT);
$test2 = password_verify('eyasharon', $newHash);
echo "Test with new hash: ".($test2 ? '✅ SUCCESS' : '❌ FAILED')."\n";
echo "New hash for DB: $newHash\n";

// Test with database retrieval simulation
$mockFromDB = '$2y$10$N9qo8uLOickgx2ZMRZoMy.Mrq6PHiG5rC9FgWUZ4T7W7LbQ3Yq9Xe';
$test3 = password_verify('eyasharon', trim($mockFromDB));
echo "Test with trimmed hash: ".($test3 ? '✅ SUCCESS' : '❌ FAILED')."\n";
?>