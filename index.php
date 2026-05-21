<?php
$url = "http://localhost:8984/rest/club";
$username = "admin";
$password = "admin";

$query = '
<resultats>
{
  for $c in db:get("club")//concours
  let $cat := db:get("club")//categorie[@id=$c/@categorieRef]/libelle
  return
    <concours id="{$c/@id}" date="{$c/@date}" coefficient="{$c/@coefficient}" categorieRef="{$c/@categorieRef}">
      <titre>{$c/titre/text()}</titre>
      <categorie>{$cat/text()}</categorie>
    </concours>
}
</resultats>
';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url . "?query=" . urlencode($query));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Club Info_Tech - Concours</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🏆 Club Info_Tech</h1>
        <h2>Liste des concours</h2>

        <?php if ($http_code == 200): ?>
            <?php
            $xml = simplexml_load_string($response);
            if ($xml && isset($xml->concours) && count($xml->concours) > 0):
            ?>
            <table>
                <tr>
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Date</th>
                    <th>Coefficient</th>
                </tr>
                <?php foreach ($xml->concours as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c->titre) ?></td>
                    <td><?= htmlspecialchars($c->categorie) ?></td>
                    <td><?= htmlspecialchars($c['date']) ?></td>
                    <td><?= htmlspecialchars($c['coefficient']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php else: ?>
                <p>Aucun concours trouvé.</p>
            <?php endif; ?>
        <?php else: ?>
            <div class="error">
                <p>❌ Erreur de connexion à BaseX (HTTP <?= $http_code ?>)</p>
                <p>Vérifiez que :</p>
                <ul>
                    <li>BaseX HTTP Server est démarré : <code>basexhttp -h 8984</code></li>
                    <li>La base de données 'club' existe : <code>CREATE DB club club.xml</code></li>
                    <li>Les identifiants sont corrects (admin/admin)</li>
                </ul>
            </div>
        <?php endif; ?>

        <br>
        <a href="inscription.php">📝 S'inscrire à un concours</a>
        <a href="resultats.php">📊 Voir les résultats</a>
    </div>
</body>
</html>