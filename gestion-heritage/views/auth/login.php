<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion Patrimoine</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts : Lexend -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #01452f;
            --secondary-color: #023625;
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Lexend', sans-serif;
            background-color: #f8f9fa;
        }

        .login-wrapper {
            min-height: 100vh;
        }

        /* Panneau de Gauche : Formulaire sur fond blanc avec motifs */
        .left-form-section {
            background-color: #FFFFFF;
            background-image: radial-gradient(#e0e0e0 0.75px, transparent 0.75px);
            background-size: 24px 24px;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            padding: 40px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        /* Conteneur de l'icône/logo */
        .brand-icon-box {
            width: 70px;
            height: 70px;
            background-color: rgba(1, 69, 47, 0.08);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem auto;
            color: var(--primary-color);
            font-size: 1.8rem;
        }

        /* Champs de saisie personnalisés avec icônes */
        .form-group-custom {
            position: relative;
            margin-bottom: 20px;
        }

        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper i.input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9E9E9E;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .form-control-custom {
            height: 52px;
            padding-left: 48px;
            padding-right: 48px;
            border-radius: 12px;
            border: 1.5px solid #E2E8F0;
            background-color: #F8FAFC;
            font-size: 0.95rem;
            color: #1E293B;
            transition: all 0.25s ease-in-out;
        }

        .form-control-custom:focus {
            background-color: #FFFFFF;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(1, 69, 47, 0.12);
            outline: none;
        }

        .form-control-custom:focus + i.input-icon,
        .input-icon-wrapper:focus-within i.input-icon {
            color: var(--primary-color);
        }

        .btn-toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #94A3B8;
            padding: 6px 10px;
            cursor: pointer;
            border-radius: 8px;
            transition: color 0.2s;
        }

        .btn-toggle-pw:hover {
            color: #334155;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .link-custom {
            color: #64748B;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .link-custom:hover {
            color: var(--primary-color);
        }

        /* Bouton de Connexion principal */
        .btn-custom-submit {
            height: 52px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #022d1f 100%);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 12px;
            border: none;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 20px rgba(1, 69, 47, 0.25);
            transition: all 0.3s ease;
        }

        .btn-custom-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(1, 69, 47, 0.35);
            color: #ffffff;
        }

        /* Panneau de Droite : Bloc Visuel de Couleur */
        .right-hero-section {
            background: linear-gradient(145deg, var(--primary-color) 0%, #022217 100%);
            position: relative;
            overflow: hidden;
        }

        .glow-circle-1 {
            position: absolute;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
            top: -60px;
            right: -60px;
        }

        .hero-text-wrapper {
            position: relative;
            z-index: 2;
            max-width: 380px;
        }

        .hero-title {
            color: #FFFFFF;
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1.35;
            letter-spacing: -0.5px;
        }

        .hero-subtitle {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.95rem;
            margin-top: 15px;
            line-height: 1.6;
        }

        .badge-pill-custom {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            padding: 6px 16px;
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #FFFFFF;
            font-size: 0.825rem;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0 login-wrapper">
        
        <!-- SECTION GAUCHE : Formulaire de Connexion Blanc -->
        <div class="col-lg-7 d-flex align-items-center justify-content-center p-4 left-form-section">
            <div class="login-card">
                
                <!-- Logo & En-tête -->
                <div class="text-center mb-4">
                    <div class="brand-icon-box">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">Espace de Connexion</h4>
                    <p class="text-muted small">Veuillez entrer vos identifiants pour continuer</p>
                </div>

                <!-- Gestion des erreurs -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger bg-danger text-white border-0 rounded-3 py-2 px-3 small mb-4 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?action=authenticate" method="POST">
                    
                    <!-- Champ Identifiant -->
                    <div class="form-group-custom">
                        <label class="form-label text-secondary small fw-semibold">Identifiant ou Nom d'utilisateur</label>
                        <div class="input-icon-wrapper">
                            <input type="text" name="username" id="username" class="form-control form-control-custom" placeholder="admin" required autocomplete="username">
                            <i class="fa-regular fa-user input-icon"></i>
                        </div>
                    </div>

                    <!-- Champ Mot de passe -->
                    <div class="form-group-custom mb-3">
                        <label class="form-label text-secondary small fw-semibold">Mot de passe</label>
                        <div class="input-icon-wrapper">
                            <input type="password" id="passwordInput" name="password" class="form-control form-control-custom" placeholder="••••••••" required autocomplete="current-password">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <button type="button" class="btn-toggle-pw" onclick="togglePasswordVisibility()">
                                <i class="fa-regular fa-eye-slash" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="d-flex justify-content-between align-items-center mb-4 small">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe">
                            <label class="form-check-label text-secondary" for="rememberMe">Se souvenir de moi</label>
                        </div>
                        <a href="#" class="link-custom small">Mot de passe oublié ?</a>
                    </div>

                    <!-- Bouton de Soumission -->
                    <button type="submit" class="btn btn-custom-submit w-100 text-uppercase">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> Se connecter
                    </button>
                </form>

            </div>
        </div>

        <!-- SECTION DROITE : Panneau Visuel Coloré -->
        <div class="col-lg-5 d-none d-lg-flex right-hero-section align-items-center justify-content-center p-5">
            <div class="glow-circle-1"></div>

            <div class="hero-text-wrapper text-start">
                <div class="badge-pill-custom">
                    <i class="fa-solid fa-shield-halved text-warning"></i> Plateforme de gestion &bull; Ousmane N'DIAYE
                </div>
                <h2 class="hero-title">Plateforme de Gestion de Patrimoine.</h2>
                <p class="hero-subtitle">Plateforme intelligente de suivi et de gestion de patrimoine. Gérez efficacement l'ensemble de vos actifs et rapports sur une seule interface centralisée.</p>
            </div>
        </div>

    </div>
</div>

<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('passwordInput');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    }
}
</script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>