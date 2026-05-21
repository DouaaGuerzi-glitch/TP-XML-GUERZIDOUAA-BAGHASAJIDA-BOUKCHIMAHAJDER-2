(: تعريف المتغيرات في البداية لضمان رؤيتها في كل مكان :)
let $targetCategorie := "Intelligence Artificielle"
let $targetCatId := //categorie[libelle=$targetCategorie]/@id

return
<result>
  (: Q1: Liste des membres :)
  <membres>
  {
    for $m in //membre
    let $cat := //categorie[@id=$m/@categorieRef]/libelle
    return
      <membre id="{$m/@id}">
        <nomComplet>{concat($m/prenom, " ", $m/nom)}</nomComplet>
        <email>{$m/email/text()}</email>
        <categorie>{$cat/text()}</categorie>
      </membre>
  }
  </membres>,

  (: Q2: Liste des concours ordonnés par date :)
  <concoursList>
  {
    for $c in //concours
    let $cat := //categorie[@id=$c/@categorieRef]/libelle
    order by $c/@date
    return
      <concours>
        <titre>{$c/titre/text()}</titre>
        <date>{data($c/@date)}</date>
        <coefficient>{data($c/@coefficient)}</coefficient>
        <categorie>{$cat/text()}</categorie>
      </concours>
  }
  </concoursList>,

  (: Q3: Calcul des scores par concours :)
  <scoresParConcours>
  {
    for $c in //concours
    let $coeff := number($c/@coefficient)
    return
      <concours titre="{$c/titre}">
      {
        for $p in $c/participants/participant
        let $membre := //membre[@id=$p/@membreRef]
        let $nom := concat($membre/prenom, " ", $membre/nom)
        let $score := ($p/complexite + $p/tempsExecution) * $coeff
        return 
          <participant score="{$score}">{$nom}</participant>
      }
      </concours>
  }
  </scoresParConcours>,

  (: Q4: Trouver le(s) vainqueur(s) de chaque concours :)
  <vainqueurs>
  {
    for $c in //concours
    let $coeff := number($c/@coefficient)
    let $maxScore := max(
      for $p in $c/participants/participant
      return ($p/complexite + $p/tempsExecution) * $coeff
    )
    return
      <concours titre="{$c/titre}">
      {
        for $p in $c/participants/participant
        let $membre := //membre[@id=$p/@membreRef]
        let $score := ($p/complexite + $p/tempsExecution) * $coeff
        where $score = $maxScore
        return
          <vainqueur>
            <nom>{concat($membre/prenom, " ", $membre/nom)}</nom>
            <score>{format-number($score, "0.00")}</score>
          </vainqueur>
      }
      </concours>
  }
  </vainqueurs>,

  (: Q5: Membres de la catégorie 'Intelligence Artificielle' :)
  <membresCategorie categorie="{$targetCategorie}">
  {
    for $m in //membre[@categorieRef=$targetCatId]
    order by $m/nom, $m/prenom
    return
      <membre>
        <nom>{$m/nom/text()}</nom>
        <prenom>{$m/prenom/text()}</prenom>
        <email>{$m/email/text()}</email>
      </membre>
  }
  </membresCategorie>
</result>