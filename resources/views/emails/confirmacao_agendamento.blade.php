<!DOCTYPE html>
<html>
<head>
    <title>Confirmação de Agendamento</title>
</head>
<body>
    <h1>Olá, {{ $params['medico'] }}</h1>
    <p>Seu agendamento para a sala <strong>{{ $params['sala'] }}</strong> foi realizado com sucesso.</p>
    <p><strong>Detalhes:</strong></p>
    <ul>
        <li>Data: {{ $params['data'] }}</li>
        <li>Horários: {{ implode(', ', $params['horarios']) }}</li>
    </ul>
    
    @if(isset($params['valor_total']) && $params['valor_total'] > 0)
        <p>Valor total: <strong>R$ {{ number_format($params['valor_total'], 2, ',', '.') }}</strong></p>
    @endif

    @if(isset($params['credito_selecionado']) && $params['credito_selecionado'] > 0)
        <p>Créditos utilizados: <strong>R$ {{ number_format($params['credito_selecionado'], 2, ',', '.') }}</strong></p>
    @endif

    <p>Atenciosamente,<br>Equipe Medical Place</p>
</body>
</html>