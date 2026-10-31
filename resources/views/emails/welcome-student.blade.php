<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4fbff; color: #0b1f3a; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e3edf3; }
        .header { background-color: #0b1f3a; color: #ffffff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .header span { color: #22d3ee; }
        .content { padding: 30px; }
        .credentials { background-color: #f4fbff; border: 1px dashed #22d3ee; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
        .credentials p { margin: 8px 0; font-size: 16px; }
        .credentials strong { color: #0b1f3a; font-size: 18px; }
        .btn { display: inline-block; background-color: #22d3ee; color: #0b1f3a; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 20px; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #5f6f84; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SHARK<span>LINK</span></h1>
        </div>
        <div class="content">
            <h2>Bonjour {{ $user->first_name }},</h2>
            <p>Félicitations ! Votre candidature a été acceptée et votre compte étudiant a été créé avec succès.</p>
            <p>Voici vos identifiants temporaires pour accéder à votre espace personnel :</p>
            
            <div class="credentials">
                <p>📧 <strong>Email :</strong> {{ $user->email }}</p>
                <p>🆔 <strong>Matricule :</strong> {{ $matricule }}</p>
                <p>🔑 <strong>Mot de passe temporaire :</strong> <span style="color: #ef4444; font-family: monospace; font-size: 20px;">{{ $password }}</span></p>
            </div>

            <p style="color: #ef4444; font-size: 14px;">⚠️ <strong>Important :</strong> Pour des raisons de sécurité, vous serez obligé de changer ce mot de passe lors de votre première connexion.</p>
            
            <div style="text-align: center;">
                <a href="{{ url('/login') }}" class="btn">Accéder à mon espace étudiant</a>
            </div>
        </div>
        <div class="footer">
            © {{ date('Y') }} SHARKLINK. Tous droits réservés.<br>
            En cas de problème, contactez le support technique de votre établissement.
        </div>
    </div>
</body>
</html>