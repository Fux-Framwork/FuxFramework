<?php
/**
 * My View description here
 *
 * @var string $myViewParameter This variable includes the page heading
 * @var array $params
 */
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <title>Example</title>
</head>
<body>
<h1>Test</h1>
<form method="post" action="<?= routeFullUrl('/send-form') ?>">
    <input type="text" name="param1" value="value1">
    <input type="text" name="param2" value="value2">
    <input type="text" name="param3" value="value3">
    <input type="text" name="_token" value="<?= csrf_token() ?>"/>
    <button>Submit</button>
</form>
</body>
</html>
