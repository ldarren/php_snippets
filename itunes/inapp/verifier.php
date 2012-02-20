<?php
function getReceiptData($receipt, $sandbox = false){
  if ($sandbox){
    $endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
  }else{
    $endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
  }
  $postData = json_encode(array('receipt-data'=>$receipt));

  $ch = curl_init($endpoint);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

  $response = curl_exec($ch);
  $errno = curl_errno($ch);
  $errmsg = curl_error($ch);
  curl_close($ch);

  if ($errno != 0){
    echo "$errno: $errmsg";
    throw new Exception($errmsg, $errno);
  }

  $data = json_decode($response);
  if (!is_object($data)){
    echo 'Invalid response data';
    throw new Exception('Invalid response data');
  }

  if (!isset($data->status) || $data->status != 0){
    print_r($data);
    throw new Exception('Invalid receipt');
  }

  return array(
  'quantity'       =>  $data->receipt->quantity,
  'product_id'     =>  $data->receipt->product_id,
  'transaction_id' =>  $data->receipt->transaction_id,
  'purchase_date'  =>  $data->receipt->purchase_date,
  'app_item_id'    =>  $data->receipt->app_item_id,
  'bid'            =>  $data->receipt->bid,
  'bvrs'           =>  $data->receipt->bvrs
  );
}

$receipt = $_GET['receipt'];
$sandbox = (bool)$_GET['sandbox'];

try{
  $info = getReceiptData($receipt, $sandbox);
  echo 'ok';
}catch(Exception $ex){
  echo $ex->getMessage();
  error_log($ex->getMessage());
}

