<!DOCTYPE html>
<html>
<head>
    <title>Boas-vindas à Medical Place</title>
</head>
<body>
    <h1>Olá, {{ $params['nome'] }}</h1>
    <p>Seja bem-vindo à Medical Place! Seu cadastro foi realizado com sucesso.</p>
    <p>Agora você pode acessar nosso sistema para realizar agendamentos e gerenciar suas consultas.</p>
    <p><strong>Dados de acesso:</strong></p>
    <ul>
        <li>Login: {{ $params['email'] }}</li>
    </ul>
    <p>Caso tenha alguma dúvida, nossa equipe está à disposição.</p>
    <p>Atenciosamente,<br>Equipe Medical Place</p>
</body>
</html>
