<!DOCTYPE html>
<html>
<head>
    <title>Bem-vindo à Medical Place</title>
</head>
<body>
    <h1>Olá, {{ $params['nome'] }}</h1>
    <p>Seja muito bem-vindo à Medical Place.</p>
    <p>Seu cadastro foi realizado com sucesso.</p>
    <p><strong>E-mail de acesso:</strong> {{ $params['email'] }}</p>
    <p>Atenciosamente,<br>Equipe Medical Place</p>
</body>
</html>
