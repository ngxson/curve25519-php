<?php

require_once('axlsign.php');

// calculate hash of a string and convert it to int array
function HashToUint8Array($str) {
  $hash = hash('sha256', $str);
  return StringToUint8Array($hash);
}

// convert from hex string to array
function StringToUint8Array($str) {
    $arr = array_fill(0, 32, 0);
    for ($i = 0 ; $i < 32 ; $i++) {
        $num = $str[$i*2].''.$str[$i*2 + 1];
        $arr[$i] = hexdec($num); // parse hex
    }
    return $arr;
}

// convert from array to hex string
function Uint8ArrayToString($arr) {
    $str = '';
    for ($i = 0 ; $i < sizeof($arr) ; $i++) {
        $str .= dechex($arr[$i]); // to hex
    }
    return $str;
}

function test() {
  $password = 'G2S45EAZ';
  $qrcode = '6499959813|ea9a7b3dc8edb63d';
  $axlsign = new axlsign;
  $seed = HashToUint8Array($password);
  $keypair = $axlsign->generateKeyPair($seed);
  //var_dump($keypair); echo '<br/>';

  // test verify QR code
  $inputQRCode = array(
      'uid' => explode('|', $qrcode)[0],
      'signature' => explode('|', $qrcode)[1],
  );
  $signatureTest = $axlsign->sign($keypair['private'], HashToUint8Array($inputQRCode['uid']));
  $signatureTest = array_slice($signatureTest, 0, 8);
  //echo Uint8ArrayToString($signatureTest); echo '<br/>';

  echo(
      Uint8ArrayToString($signatureTest) === $inputQRCode['signature']
      ? 'Your QR code is OK'
      : 'Your QR code is INVALID'
  );
}

test();

