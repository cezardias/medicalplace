<!DOCTYPE html>
<html>
<head>
    <title>Confirmação de Agendamento</title>
</head>
<body>
    <p>Olá, <strong>{{ $params['nome'] ?? 'Doutor(a)' }}</strong></p>
    <p>Seu agendamento para a sala <strong>{{ $params['sala'] ?? 'N/A' }}</strong> foi realizado com sucesso.</p>
    
    <p><strong>Detalhes do Agendamento:</strong></p>
    <ul>
        <li>Data: {{ $params['data'] ?? 'N/A' }}</li>
        <li>Horário: {{ $params['horario'] ?? 'N/A' }}</li>
    </ul>
    
    <p>Atenciosamente,<br>Equipe Medical Place</p>
</body>
</html>