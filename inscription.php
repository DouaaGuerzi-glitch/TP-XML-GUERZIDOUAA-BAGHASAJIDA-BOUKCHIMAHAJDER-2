<?php
$basex_url = "http://localhost:8984/rest/club";
$username = "admin";
$password = "admin";
$message = "";
$error = "";

function executeQuery($query, $basex_url, $username, $password) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $basex_url . "?query=" . urlencode($query));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['response' => $response, 'http_code' => $http_code];
}

function executeUpdate($query, $basex_url, $username, $password) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $basex_url . "?command=" . urlencode("XQUERY " . $query));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['response' => $response, 'http_code' => $http_code];
}

$query_concours = '
<concoursList>
{
  for $c in db:get("club")//concours
  return
    <concours id="{$c/@id}">
      <titre>{$c/titre/text()}</titre>
    </concours>
}
</concoursList>
';
$result_concours = executeQuery($query_concours, $basex_url, $username, $password);

$query_membres = '
<membresList>
{
  for $m in db:get("club")//membre
  return
    <membre id="{$m/@id}">
      <nom>{$m/nom/text()}</nom>
      <prenom>{$m/prenom/text()}</prenom>
      <categorieRef>{data($m/@categorieRef)}</categorieRef>
    </membre>
}
</membresList>
';
$result_membres = executeQuery($query_membres, $basex_url, $username, $password);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $membreRef    = htmlspecialchars($_POST['membre']);
    $concoursId   = htmlspecialchars($_POST['concours']);
    $complexite   = intval($_POST['complexite']);
    $tempsExecution = intval($_POST['temps']);

    $check_query = '
    let $exists := exists(db:get("club")//concours[@id="' . $concoursId . '"]/participants/participant[@membreRef="' . $membreRef . '"])
    return <result>{$exists}</result>
    ';
    $check_result = executeQuery($check_query, $basex_url, $username, $password);

    if (strpos($check_result['response'], 'true') !== false) {
        $error = "❌ Ce membre est déjà inscrit à ce concours !";
    } else {
        $insert_query = '
        insert node
            <participant membreRef="' . $membreRef . '">
                <complexite>' . $complexite . '</complexite>
                <tempsExecution>' . $tempsExecution . '</tempsExecution>
            </participant>
        into db:get("club")//concours[@id="' . $concoursId . '"]/participants
        ';
        $insert_result = executeUpdate($insert_query, $basex_url, $username, $password);

        if ($insert_result['http_code'] == 200) {
            $message = "✅ Inscription réussie pour le concours !";
        } else {
            $error = "❌ Erreur lors de l'inscription (HTTP " . $insert_result['http_code'] . ")";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription à un concours</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>📝 Inscription à un concours</h1>

        <?php if ($message): ?>
            <div class="success"><?= $message ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>📅 Choisir un concours :</label>
            <select name="concours" required>
                <option value="">-- Sélectionner --</option>
                <?php
                $xml = simplexml_load_string($result_concours['response']);
                if ($xml && isset($xml->concours)) {
                    foreach ($xml->concours as $concours) {
                        echo '<option value="' . htmlspecialchars($concours['id']) . '">'
                             . htmlspecialchars($concours->titre) . '</option>';
                    }
                } else {
                    echo '<option value="">Aucun concours disponible</option>';
                }
                ?>
            </select>

            <label>👤 Choisir un membre :</label>
            <select name="membre" required>
                <option value="">-- Sélectionner --</option>
                <?php
                $xml = simplexml_load_string($result_membres['response']);
                if ($xml && isset($xml->membre)) {
                    foreach ($xml->membre as $membre) {
                        echo '<option value="' . htmlspecialchars($membre['id']) . '">'
                             . htmlspecialchars($membre->prenom . " " . $membre->nom)
                             . ' (Cat: ' . htmlspecialchars($membre->categorieRef) . ')</option>';
                    }
                } else {
                    echo '<option value="">Aucun membre disponible</option>';
                }
                ?>
            </select>

            <label>⚙️ Complexité (0-100) :</label>
            <input type="number" name="complexite" min="0" max="100" required>

            <label>⏱️ Temps d'exécution (ms) :</label>
            <input type="number" name="temps" min="1" max="1000" required>

            <button type="submit">✅ S'inscrire</button>
        </form>

        <br>
        <a href="index.php">← Retour à l'accueil</a>
    </div>
</body>
</html>