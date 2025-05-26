<?php
$tests = [
    'test_add_donor.php',
    'test_delete_donor.php',
];

$passed = 0;
$failed = 0;

foreach ($tests as $test) {
    echo "Running $test...\n";
    $output = shell_exec("php testsin/" . escapeshellarg($test));
    echo $output;

    if (strpos($output, 'PASSED') !== false) {
        $passed++;
    } else {
        $failed++;
    }
    echo "---------------------------\n";
}

echo "Summary: $passed passed, $failed failed.\n";
