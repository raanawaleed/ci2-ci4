<html>
<body>
    <div style="text-align: center;">
        <h2>Réinitialisation du mot de passe</h2>
        <h4>Bonjour <?= ucwords(strtolower($user->firstname . ' ' . $user->lastname)) ?></h4>
        <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le lien ci-dessous :</p>
        <p>
            <a href="<?= esc($url) ?>" style="padding: 10px 20px; background-color: #007bff; color: white; border-radius: 5px; text-decoration: none;">Réinitialiser</a>
        </p>
        <p>Veuillez noter que ce lien n'est valable que pour 15 minutes !</p>
    </div>
</body>
</html>
