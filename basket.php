<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "joueurs_championnats";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer les pays pour les listes de sélection
$paysQuery = "SELECT DISTINCT pays FROM joueurs UNION SELECT DISTINCT pays FROM championnats";
$paysResult = $conn->query($paysQuery);

$paysList = [];
if ($paysResult->num_rows > 0) {
    while($row = $paysResult->fetch_assoc()) {
        $paysList[] = $row['pays'];
    }
}

// Filtrer par pays sélectionné
$selectedPays = '';
if (isset($_POST['pays'])) {
    $selectedPays = $_POST['pays'];
}

// Récupérer les joueurs et leurs championnats associés
$query = "
    SELECT 
        j.id_joueurs, j.nom AS nom_joueur, j.prenom, j.surnom, j.post, j.taille, j.saison, j.photo, j.pays AS pays_joueur, 
        c.code_championnat, c.nom AS nom_championnat, c.organisateur, c.pays AS pays_championnat
    FROM 
        joueurs j
    LEFT JOIN 
        championnats c ON j.pays = c.pays
    ".($selectedPays ? "WHERE j.pays = '$selectedPays' OR c.pays = '$selectedPays'" : "")."
";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Joueurs et Championnats</title>
</head>
<body>
    <h1>Liste des Joueurs et Championnats</h1>
    
    <form method="post" action="basket.php">
        <label for="pays">Sélectionnez un pays :</label>
        <select name="pays" id="pays">
            <option value="">Tous</option>
            <?php foreach($paysList as $pays): ?>
                <option value="<?php echo htmlspecialchars($pays); ?>" <?php echo ($selectedPays == $pays) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($pays); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filtrer</button>
    </form>

    <table border="1">
        <tr>
            <th>ID Joueur</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Surnom</th>
            <th>Poste</th>
            <th>Taille</th>
            <th>Saison</th>
            <th>Photo</th>
            <th>Pays Joueur</th>
            <th>Code Championnat</th>
            <th>Nom Championnat</th>
            <th>Organisateur</th>
            <th>Pays Championnat</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_joueurs']; ?></td>
                    <td><?php echo $row['nom_joueur']; ?></td>
                    <td><?php echo $row['prenom']; ?></td>
                    <td><?php echo $row['surnom']; ?></td>
                    <td><?php echo $row['post']; ?></td>
                    <td><?php echo $row['taille']; ?></td>
                    <td><?php echo $row['saison']; ?></td>
                    <td><img src="<?php echo $row['photo']; ?>" alt="Photo de <?php echo $row['nom_joueur']; ?>" width="50"></td>
                    <td><?php echo $row['pays_joueur']; ?></td>
                    <td><?php echo $row['code_championnat']; ?></td>
                    <td><?php echo $row['nom_championnat']; ?></td>
                    <td><?php echo $row['organisateur']; ?></td>
                    <td><?php echo $row['pays_championnat']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="13">Aucun résultat trouvé</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>