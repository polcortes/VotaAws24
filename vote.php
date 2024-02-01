<?php
// session_start();

if (isset($_SESSION["usuario"]) /*|| $_GET['invitation_token']*/) {  // hay que poner el get de los tokens para los invitados.
  // if (isset($_GET["survey_id"])) {
    $dsn = "mysql:host=localhost;dbname=votadb";        
    $username = "root";
    $password = "p@raMor3"; // AWS24VotaPRRojo_
    $pdo = new PDO($dsn, $username, $password);

    $query = $pdo -> prepare("SELECT survey_id, survey_title, imag FROM Survey WHERE survey_id = :survey_id;");

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
  <title>Votar en una encuesta</title>
  <link rel="stylesheet" href="styles.css">
  <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
  <script src="vote.js"></script>
  <script src="componentes/notificationHandler.js"></script>
</head>
<body>
  <main id="vote">
    <h1><?php echo $surveyTitle; ?></h1>
    <?php if (isset($surveyImg)): ?>
      <img src=<?php echo "'". $surveyImg ."'" ?> alt="Imagen de la encuesta.">
    <?php endif ?>
    
    <form id="vote-form" method="POST">
    <?php
      $querydos = $pdo -> prepare("SELECT * FROM SurveyOption WHERE survey_id = :survey_id;");
      $querydos -> bindParam(":survey_id", $surveyID);
      $querydos -> execute();

      $rows = $querydos -> fetchAll();

      foreach ($rows as $row) {
    ?>
      <article>
        <!--<img src="https://t3.ftcdn.net/jpg/02/48/42/64/360_F_248426448_NVKLywWqArG2ADUxDq6QprtIzsF82dMF.jpg" alt="">-->
        <?php if (isset($row["imag"])): ?>
          <img src=<?php echo "'". $row["imag"] ."'" ?> alt="Imagen de la opción.">
        <?php endif ?>
        <label><input type="radio" name="answer[]" value=<?php echo "'". $row["option_text"] ."'"; ?>><?php echo $row["option_text"]; ?></label>
      </article>
    <?php
      }
    ?>
    <input type="submit" value="Enviar voto" id="submit-vote">
    </form>
    

    <ul id="notification__list">
        <!-- todas las notificaciones -->
    </ul>
  </main>
</body>
</html>