<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MJS - Accès sécurisé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Utilisation de l'image en arrière-plan */
            background: url('public/images/image_accueil2.svg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Overlay pour assombrir l'image et rendre le texte lisible */
        .overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4);
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 90%;
            max-width: 1000px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
        }

        /* Formulaire avec effet verre noir */
        .glass-card {
    /* Fond semi-transparent plus riche */
    background: rgba(255, 255, 255, 0.05); 
    /* Backdrop blur plus fort pour un effet givré */
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    
    /* Bordure lumineuse subtile */
    border: 1px solid rgba(255, 255, 255, 0.15);
    
    /* Coins arrondis plus harmonieux */
    border-radius: 32px;
    padding: 3rem;
    width: 100%;
    max-width: 420px;
    color: white;
    
    /* Ombre portée complexe pour donner du relief */
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 
                inset 0 0 0 1px rgba(255, 255, 255, 0.1);
    
    /* Transition douce */
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.glass-card:hover {
    transform: translateY(-5px);
}

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 10px;
        }

        .form-control::placeholder { color: rgba(255, 255, 255, 0.6); }

        .btn-flashy {
            background: linear-gradient(45deg, #008751, #00c853);
            border: none;
            color: white;
            font-weight: bold;
            padding: 0.8rem;
            border-radius: 10px;
            transition: transform 0.2s;
        }
        
        .btn-flashy:hover { transform: scale(1.02); color: white; }

        .brand-text {
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
		
		@import url('https://fonts.cdnfonts.com/css/gilroy');
    </style>
</head>
<body>

<div class="overlay"></div>

<div class="login-wrapper">
    
    <div class="text-white text-center mb-4">
        <h1 class="fw-bold" style="font-family: 'Gilroy', sans-serif; font-size: 2.5rem; letter-spacing: 1px;">
            MINISTÈRE DE LA JEUNESSE ET DES SPORTS
        </h1>
        <p class="lead opacity-75" style="font-family: 'Gilroy', sans-serif; font-size: 1.2rem;">
            Système Intégré de Gestion du Personnel (SIGP)
        </p>
    </div>

    <div class="glass-card">
        <div class="text-center mb-4">
            <h4 class="fw-bold">Connexion</h4>
        </div>

        <form action="modules/auth/connexion.php" method="POST">
            <div class="mb-3">
                <label class="form-label small">Identifiant</label>
                <input type="text" name="username" class="form-control" placeholder="Matricule ou email" required>
            </div>
            <div class="mb-4">
                <label class="form-label small">Mot de passe</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-flashy">ACCÉDER À LA SESSION</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>