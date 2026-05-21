<?php
$basex_url = "http://localhost:8984/rest/club";
$username = "admin";
$password = "admin";
$resultat = "";
$selectedConcours = "";

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

$query_concours = '
<concoursList>
{
  for $c in db:get("club")//concours
  return
    <concours id="{$c/@id}">
      <titre>{$c/titre/text()}</titre>
      <date>{data($c/@date)}</date>
    </concours>
}
</concoursList>
';
$result_concours = executeQuery($query_concours, $basex_url, $username, $password);

if (isset($_GET['concours']) && !empty($_GET['concours'])) {
    $selectedConcours = htmlspecialchars($_GET['concours']);

    $query_resultats = '
    let $c := db:get("club")//concours[@id="' . $selectedConcours . '"]
    let $coeff := number($c/@coefficient)
    let $maxScore := max(
        for $p in $c/participants/participant
        return ($p/complexite + $p/tempsExecution) * $coeff
    )
    return
        <resultats>
        {
            for $p in $c/participants/participant
            let $membre := db:get("club")//membre[@id=$p/@membreRef]
            let $nom := concat($membre/prenom, " ", $membre/nom)
            let $score := ($p/complexite + $p/tempsExecution) * $coeff
            return
                <participant>
                    <nom>{$nom}</nom>
                    <complexite>{$p/complexite/text()}</complexite>
                    <tempsExecution>{$p/tempsExecution/text()}</tempsExecution>
                    <score>{format-number($score, "0.00")}</score>
                    <vainqueur>{if ($score = $maxScore) then "🏆 Oui" else "Non"}</vainqueur>
                </participant>
        }
        </resultats>
    ';
    $resultat = executeQuery($query_resultats, $basex_url, $username, $password);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats des concours</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>📊 Résultats des concours</h1>

        <form method="GET">
            <label>🎯 Choisir un concours :</label>
            <select name="concours" onchange="this.form.submit()">
                <option value="">-- Sélectionner --</option>
                <?php
                $xml = simplexml_load_string($result_concours['response']);
                if ($xml && isset($xml->concours)) {
                    foreach ($xml->concours as $concours) {
                        $selected = (isset($_GET['concours']) && $_GET['concours'] == $concours['id']) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($concours['id']) . '" ' . $selected . '>'
                             . htmlspecialchars($concours->titre) . ' (' . htmlspecialchars($concours->date) . ')</option>';
                    }
                } else {
                    echo '<option value="">Aucun concours disponible</option>';
                }
                ?>
            </select>
        </form>

        <?php if ($selectedConcours && $resultat && $resultat['http_code'] == 200): ?>
            <?php
            $xml = simplexml_load_string($resultat['response']);
            if ($xml && isset($xml->participant) && count($xml->participant) > 0):
            ?>
            <h2>🏅 Résultats détaillés</h2>
            <table>
                <thead>
                    <tr>
                        <th>Participant</th>
                        <th>Complexité</th>
                        <th>Temps (ms)</th>
                        <th>Score</th>
                        <th>Vainqueur</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($xml->participant as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p->nom) ?></td>
                        <td><?= htmlspecialchars($p->complexite) ?></td>
                        <td><?= htmlspecialchars($p->tempsExecution) ?></td>
                        <td><strong><?= htmlspecialchars($p->score) ?></strong></td>
                        <td><?= htmlspecialchars($p->vainqueur) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="error">Aucun participant trouvé pour ce concours.</div>
            <?php endif; ?>
        <?php elseif ($selectedConcours): ?>
            <div class="error">Erreur lors du chargement des résultats (Code: <?= $resultat['http_code'] ?? 'N/A' ?>)</div>
        <?php endif; ?>

        <br>
        <a href="index.php">← Retour à l'accueil</a>
    </div>
</body>
</html>