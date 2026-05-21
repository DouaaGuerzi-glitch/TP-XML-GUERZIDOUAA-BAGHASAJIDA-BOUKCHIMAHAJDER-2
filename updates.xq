(
  (: 1. Insertion d'un nouveau membre :)
  insert node 
    <membre id="M009" categorieRef="C1">
      <nom>Bouzid</nom>
      <prenom>Nadia</prenom>
      <email>n.bouzid@club.dz</email>
    </membre>
  into doc("club")//membres,

  (: 2. Modification du coefficient du concours CO2 :)
  replace value of node 
    doc("club")//concours[@id="CO2"]/@coefficient 
  with "2.0",

  (: 3. Suppression d'un participant du concours CO1 :)
  delete node 
    doc("club")//concours[@id="CO1"]//participant[@membreRef="M003"]
)