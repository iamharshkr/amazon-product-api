<?php
include('amazon_api.php');
/* Copyright 2018 Amazon.com, Inc. or its affiliates. All Rights Reserved. */
/* Licensed under the Apache License, Version 2.0. */

$serviceName="ProductAdvertisingAPI";
$region="eu-west-1";
$keywords = 'Bluetooth earphone'; //Enter your keywords.
$accessKey = "Enter your access key"; // Enter your access key
$secretKey = "Enter your secret key"; //Enter your secret key
$AssociateTag = "Enter your associate tag"; //Enter your associate tag
$payload="{"
        ." \"Keywords\": \"$keywords\","
        ." \"Resources\": ["
        ."  \"Images.Primary.Large\","
        . "  \"ItemInfo.Features\","
        ."  \"ItemInfo.Title\","
        ."  \"Offers.Listings.Price\","
        ."  \"Offers.Listings.Promotions\","
        ."  \"Offers.Listings.SavingBasis\""
        ." ],"
        ." \"PartnerTag\": \"$AssociateTag\","
        ." \"PartnerType\": \"Associates\","
        ." \"Marketplace\": \"www.amazon.in\""
        ."}";
$host="webservices.amazon.in";
$uriPath="/paapi5/searchitems";
$awsv4 = new AwsV4 ($accessKey, $secretKey);
$awsv4->setRegionName($region);
$awsv4->setServiceName($serviceName);
$awsv4->setPath ($uriPath);
$awsv4->setPayload ($payload);
$awsv4->setRequestMethod ("POST");
$awsv4->addHeader ('content-encoding', 'amz-1.0');
$awsv4->addHeader ('content-type', 'application/json; charset=utf-8');
$awsv4->addHeader ('host', $host);
$awsv4->addHeader ('x-amz-target', 'com.amazon.paapi5.v1.ProductAdvertisingAPIv1.SearchItems');
$headers = $awsv4->getHeaders ();
$headerString = "";
foreach ( $headers as $key => $value ) {
    $headerString .= $key . ': ' . $value . "\r\n";
}
$params = array (
        'http' => array (
            'header' => $headerString,
            'method' => 'POST',
            'content' => $payload
        )
    );
$stream = stream_context_create ( $params );

$fp = @fopen ( 'https://'.$host.$uriPath, 'rb', false, $stream );

if (! $fp) {
    throw new Exception ( "Exception Occured" );
}
$response = @stream_get_contents ( $fp );
if ($response === false) {
    throw new Exception ( "Exception Occured" );
}
//echo $response;
$details = json_decode($response, true);
$products = $details['SearchResult']['Items'];

foreach($products as $product){
if (isset($product)) {
    $asin = $product['ASIN'];
    $productUrl = $product['DetailPageURL'];
}
if (isset($product['ItemInfo'])) {
    $title = $product['ItemInfo']['Title']['DisplayValue'];
    $features = $product['ItemInfo']['Features']['DisplayValues'];
    $description = '';
    foreach ($features as $feature) {
        $description .= "<li>". $feature ."</li>";
    }
}
if (isset($product['Images'])) {
    $image = $product['Images']['Primary']['Large']['URL'];
}
if (isset($product['Offers'])) {
    $Sellprice = $product['Offers']['Listings']['0']['Price']['Amount'];
    if(!$Sellprice){
        echo "Sell Price is not present.";
    }
}else{
    $Sellprice = 0;
}
if(isset($product['Offers']['Listings']['0']['SavingBasis'])){
    $Maxprice = $MaxPrice =$product['Offers']['Listings']['0']['SavingBasis']['Amount'];
}else{
    $MaxPrice = 0;
}
$Seller = 'Amazon';
echo $title ."<img align='right' src=$image height='120' width='80' /><br>". "Description: " .$description ."<br>Product Link: ". $productUrl ."<br> Deal Price: ". $Sellprice ."<br> Real Price: ". $MaxPrice ."<hr />"; 
}
echo "<hr />" .$details['SearchResult']['SearchURL'];

?>
