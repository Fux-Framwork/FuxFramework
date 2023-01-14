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
<h1><?= $myViewParameter ?></h1>
<h2>Here will appear query string parameters</h2>
<?php print_r_pre($params) ?>
</body>
</html>
