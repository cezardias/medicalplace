<!DOCTYPE html>
<html>
<head>
    <title>Bem-vindo à Medical Place</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #007bff;">Olá, {{ $params['nome'] }}!</h2>
        <p>Seja muito bem-vindo à <strong>Medical Place</strong>.</p>
        <p>Seu cadastro foi realizado com sucesso em nossa plataforma. Agora você pode acessar sua conta para realizar agendamentos e gerenciar suas locações.</p>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0;"><strong>Seu e-mail de acesso:</strong> {{ $params['email'] }}</p>
        </div>

        <p>Para começar, acesse o link abaixo:</p>
        <p style="text-align: center;">
            <a href="{{ url('/login') }}" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">Acessar Minha Conta</a>
        </p>

        <p>Se precisar de ajuda, entre em contato conosco.</p>
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #777;">Equipe Medical Place</p>
    </div>
</body>
</html>
