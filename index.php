<?php
if(empty($_FILES['arquivo'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>IMPORT CSV DATA IN DATABASE</title>
    </head>
    <body>
    <h1><?= strtoupper("import csv file directly to the database via SQL query") ?> </h1>
    <form action="index.php" method="post" enctype="multipart/form-data">
        <input type="file" name="arquivo">
        <button type="submit">OK</button>
    </form>
    </body>
    </html>
    <?php
}else{
  require_once 'loadfile.php';
  if(is_file($_FILES['arquivo']['tmp_name'])) {
      try {
          $fp = file($_FILES['arquivo']['tmp_name']);
          $lf = new Loadfile();
          if ($lf->alterarCsv($_FILES['arquivo']['tmp_name'])) {
              if ($lf->loadCsvData()) {
                  echo "Arquivo importado com sucesso -  " . count($fp) . " linhas adicionadas";
              } else {
                  echo "Erro ao inserir dados no DB";
              }
          } else {
              echo "Não foi possível alterar o arquivo!";
          }
      }catch (Exception $e){
          die($e);
      }
  }


}
?>