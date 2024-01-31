<?php
// session_start();

if (true) {
// if (isset($_SESSION["usuario"]) /*|| $_GET['invitation_token']*/) {  // hay que poner el get de los tokens para los invitados.
  // if (isset($_GET["survey_id"])) {
    $dsn = "mysql:host=localhost;dbname=votadb";        
    $username = "root";
    $password = "p@raMor3"; // AWS24VotaPRRojo_
    $pdo = new PDO($dsn, $username, $password);

    $query = $pdo -> prepare("SELECT survey_id, survey_title, survey_img FROM Survey WHERE survey_id = :survey_id;");

    // $query = $pdo -> prepare(
    //   "SELECT s.survey_title, s.imag, so.option_text, so.imag 
    //   FROM survey AS s 
    //   INNER JOIN surveyoption AS so ON so.survey_id = s.survey_id 
    //   WHERE s.survey_id = :survey_id;"
    // );

    $query -> bindParam(":survey_id", $_GET['survey_id']);
    $query -> execute();

    $row = $query -> fetch();

    $surveyID = "";
    $surveyTitle = "";
    $surveyImg = "";

    if (!$query -> rowCount() == 0) {
      $surveyID = $row["survey_id"];
      $surveyTitle = $row["survey_title"];
      $surveyImg = $row["survey_img"];
    } else {

    }
  // }
} else {
  header('Location: index.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
</head>
<body>
  <main>
    <h1><?php echo $surveyTitle; ?></h1>
    <img src=<?php echo "'". $surveyImg ."'" ?> alt="Imagen de la encuesta.">

    
  </main>
</body>
</html>