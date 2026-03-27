<!DOCTYPE html>
<html>
<head>
    <title>Boas-vindas à Medical Place</title>
</head>
<body>
    <h1>Olá, {{ $params['nome'] }}</h1>
    <p>Olá, <strong>{{ $params['nome'] ?? 'Doutor(a)' }}</strong></p>
    <p>Seu cadastro na plataforma <strong>Medical Place</strong> foi confirmado com sucesso!</p>
    
    <p><strong>Seus dados de acesso:</strong></p>
    <ul>
        <li>Login: {{ $params['email'] ?? 'N/A' }}</li>
        <li>Senha: (A que você escolheu no cadastro)</li>
    </ul>
    <p>Caso tenha alguma dúvida, nossa equipe está à disposição.</p>
    <p>Atenciosamente,<br>Equipe Medical Place</p>
</body>
</html>
