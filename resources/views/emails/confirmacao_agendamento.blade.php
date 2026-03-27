<!DOCTYPE html>
<html>
<head>
    <title>Confirmação de Agendamento</title>
</head>
<body>
    <h1>Olá, {{ $params['nome'] }}</h1>
    <p>Seu agendamento para a sala <strong>{{ $params['sala'] }}</strong> foi realizado com sucesso.</p>
    <p><strong>Detalhes:</strong></p>
    <ul>
        <li>Data: {{ $params['data'] }}</li>
        <li>Horários: {{ isset($params['horarios']) ? (is_array($params['horarios']) ? implode(', ', $params['horarios']) : $params['horarios']) : 'N/A' }}</li>
    </ul>
    
    <p>Atenciosamente,<br>Equipe Medical Place</p>
</body>
</html>