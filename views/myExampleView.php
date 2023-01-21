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
    <input type="hidden" name="_token" value="<?= csrf_token() ?>"/>
    <button>Submit</button>
</form>

<script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
<?= assetOnce('lib/FuxFramework/FuxHTTP.js', 'script') ?>
<script>
    FuxHTTP.post('<?= routeFullUrl('/send-form') ?>', {
        source: 'FuxHTTP',
        param: 'demo parameter'
    }, FuxHTTP.RESOLVE_RESPONSE, FuxHTTP.REJECT_RESPONSE)
        .then(r => console.log(r)).catch(r => console.log(r))
</script>
</body>
</html>
