<?php include __DIR__ . '/../layout/header.php'; ?>

<style>
    :root {
        --primary-color: #721519;
        --secondary-color: #F05A24;
        --bg-light: #F8F9FA;
    }

    body, html {
        height: 100%;
        margin: 0;
        font-family: 'Lexend', sans-serif;
        background-color: var(--bg-light);
    }

    .login-container {
        min-height: 100vh;
    }

    /* Section Gauche (2/3) */
    .left-section {
        background-color: #FFFFFF;
        background-image: radial-gradient(#e0e0e0 0.75px, transparent 0.75px);
        background-size: 24px 24px;
    }

    .login-box {
        width: 100%;
        max-width: 420px;
        padding: 40px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.04);
        transition: transform 0.3s ease;
    }

    .brand-logo {
        max-height: 90px;
        object-fit: contain;
        filter: drop-shadow(0px 4px 8px rgba(0,0,0,0.06));
    }

    /* Input styling */
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
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 4px rgba(240, 90, 36, 0.12);
        outline: none;
    }

    .form-control-custom:focus + i.input-icon,
    .input-icon-wrapper:focus-within i.input-icon {
        color: var(--secondary-color);
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

    /* Custom Checkbox & Links */
    .form-check-input:checked {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
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

    /* Main Button */
    .btn-custom-submit {
        height: 52px;
        background: linear-gradient(135deg, var(--secondary-color) 0%, #d84b18 100%);
        color: #ffffff;
        font-weight: 600;
        font-size: 1rem;
        border-radius: 12px;
        border: none;
        letter-spacing: 0.5px;
        box-shadow: 0 8px 20px rgba(240, 90, 36, 0.25);
        transition: all 0.3s ease;
    }

    .btn-custom-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(240, 90, 36, 0.35);
        color: #ffffff;
    }

    /* Section Droite (1/3) */
    .right-section {
        background: linear-gradient(145deg, #721519 0%, #420b0d 100%);
        position: relative;
        overflow: hidden;
    }

    /* Decorative glowing elements */
    .glow-circle-1 {
        position: absolute;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(240, 90, 36, 0.35) 0%, rgba(240, 90, 36, 0) 70%);
        top: -60px;
        right: -60px;
    }

    .glow-circle-2 {
        position: absolute;
        width: 400px;
        height: 400px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(240, 90, 36, 0.2) 0%, rgba(240, 90, 36, 0) 70%);
        bottom: -100px;
        left: -100px;
    }

    .hero-text-wrapper {
        position: relative;
        z-index: 2;
        max-width: 340px;
    }

    .hero-title {
        color: #FFFFFF;
        font-size: 2.1rem;
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

    .badge-pill {
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

<div class="container-fluid p-0">
    <div class="row g-0 login-container">
        
        <!-- Section Formulaire (2/3 de la largeur : col-lg-8) -->
        <div class="col-lg-8 d-flex align-items-center justify-content-center p-4 left-section">
            <div class="login-box">
                
                <!-- Emplacement du Logo -->
                <div class="text-center mb-4">
                    <img src="assets/images/logo.png" alt="Logo BDG" class="brand-logo mb-3">
                    <h5 class="fw-bold text-dark mb-1">Espace de Connexion</h5>
                    <p class="text-muted small">Veuillez entrer vos identifiants pour continuer</p>
                </div>

                <!-- Notification Flash -->
                <?php if (isset($_SESSION['flash'])): ?>
                    <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show mb-4 small" role="alert">
                        <?= $_SESSION['flash']['message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['flash']); ?>
                <?php endif; ?>

                <form action="index.php?action=login" method="POST">
                    
                    <!-- Champ Identifiant / E-mail -->
                    <div class="form-group-custom">
                        <label class="form-label text-secondary small fw-semibold">Adresse e-mail ou identifiant</label>
                        <div class="input-icon-wrapper">
                            <input type="email" name="email" class="form-control form-control-custom" placeholder="nom@exemple.com" required autocomplete="username">
                            <i class="fa-regular fa-envelope input-icon"></i>
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

                    <!-- Options complémentaires -->
                    <div class="d-flex justify-content-between align-items-center mb-4 small">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe">
                            <label class="form-check-label text-secondary" for="rememberMe">Se souvenir de moi</label>
                        </div>
                        <a href="#" class="link-custom small">Mot de passe oublié ?</a>
                    </div>

                    <!-- Bouton Valider -->
                    <button type="submit" class="btn btn-custom-submit w-100 text-uppercase">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> Se connecter
                    </button>
                </form>

                <div class="text-center mt-4 text-muted small">
                    Pas encore de compte ? <a href="#" class="link-custom fw-bold">Créer un compte</a>
                </div>

            </div>
        </div>

        <!-- Section Latérale Interactive (1/3 de la largeur : col-lg-4) -->
        <div class="col-lg-4 d-none d-lg-flex right-section align-items-center justify-content-center p-5">
            <div class="glow-circle-1"></div>
            <div class="glow-circle-2"></div>

            <div class="hero-text-wrapper text-start">
                <div class="badge-pill">
                    <i class="fa-solid fa-shield-halved text-warning"></i> Plateforme Sécurisée
                </div>
                <h2 class="hero-title">Accédez à votre espace professionnel.</h2>
                <p class="hero-subtitle">Gérez efficacement l'ensemble du personnel et des opérations logistiques sur une seule interface centralisée.</p>
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

<?php include __DIR__ . '/../layout/footer.php'; ?>