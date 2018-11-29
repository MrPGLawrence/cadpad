<?php
$file = "";
if(isset($_POST['file']))
{
    $file=$_POST['file'];
}
else
{
    return;
}

require dirname(__FILE__) . '/vendor/autoload.php';

use Hubs3d\Api;

// Create consumer at: https://www.3dhubs.com/my-dashboard/api/oauth/consumer/add
$settings = array(
    'consumer_key' => 'key',
    'consumer_secret' => 'key',
);

$hubs3d = new Api($settings);

////////////////////////////////////////////////////////////////////////////////
// Send the models to the API.
////////////////////////////////////////////////////////////////////////////////
$model = $hubs3d->createModel($file);

////////////////////////////////////////////////////////////////////////////////
// Create the cart with the uploaded models.
////////////////////////////////////////////////////////////////////////////////
$items = array(
    'items' => array(
        array(
            'modelId' => $model['modelId'],
            'quantity' => 1,
        ),
    ),
  );

$result = $hubs3d->createCart($items);
$resultURL = $result["url"];
echo $resultURL;
?>