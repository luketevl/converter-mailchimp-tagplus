<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Converter Mailchimp to TagPlus format</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
</head>
<?php 
if(!empty($_FILES)){
  $_FILES['file'];
  createFile($_FILES['file']['tmp_name']);
}

function createFile($file){
  $headers = [];
  $file = fopen($file,"r");
  $line = 0;

  $dom = createStructureXml();
  $clientsXml = createClientsElementXml($dom);
  while(($data = fgetcsv($file) ) !== FALSE ){
    if($line == 0){
      $headers = array_flip($data);
    }else{
      $clientsXml = createClientElementsXml($dom, $clientsXml, $data);
    }
    $line ++;
  }
  $exportXml = $dom->createElement('export');
  $exportXml->appendChild( $dom->createElement('produtos') );
  $exportXml->appendChild( $dom->createElement('fornecedores') );
  $exportXml->appendChild( $dom->createElement('transportadoras') );
  $exportXml->appendChild($clientsXml);
  $dom->appendChild($exportXml);
  saveXml($dom);

  fclose($file);
}

function saveXml($dom){
  $dom->formatOutput = true;
  $xmlString = $dom->saveXML();
  download($dom->save('clientes_'.date('d-m-y').'.xml')); 
}
function createStructureXml(){
  $dom = new DomDocument('1.0');
  return $dom;
}

function createClientsElementXml($dom){
  return $dom->createElement('clientes');
}

function createClientElementsXml($dom, $clientsXml, $data){
  // var_dump($data);
  $clientXml = $dom->createElement('cliente');
  $contactsXml = $dom->createElement('contatos');
  $contactXml = $dom->createElement('contato');
  $clientXml->appendChild( $dom->createElement('razao_social', $data[1]) );

  if(!empty($data[0])){
    $contactXml->appendChild( $dom->createElement('descricao', $data[0]) );
    $contactXml->appendChild( $dom->createElement('principal', 1) );
    $contactXml->appendChild( $dom->createElement('tipo_cadastro') );
    $contactXml->appendChild( $dom->createElement('tipo_contato', 'E'));
  }
  $contactsXml->appendChild($contactXml);
  $clientXml->appendChild($contactsXml);
  $clientsXml->appendChild($clientXml);
  return $clientsXml;
}

function download($fileName){
  $filepath = $fileName;
  header("Cache-control: private");
  header("Content-type: application/force-download");
  header("Content-transfer-encoding: binary\n");
  header("Content-disposition: attachment; filename=\"$fileName\"");
  header("Content-Length: ".filesize($filepath));
  readfile($filepath);
  exit;
}

function getData($line, $clientsXml){

}

?>
<body>
  <form action="index.php" method="POST" enctype='multipart/form-data'>
    <input type="file" name="file" />
    <input type="submit" value="enviar" />
  </form>
</body>





</html>