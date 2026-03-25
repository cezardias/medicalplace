Olá {{ $medico }},<br><br>
Seu agendamento na sala <b>{{ $sala }}</b> no dia <b>{{ $data }}</b> está confirmado.<br><br>

@if ($valor_total > 0)
Valor cobrado: <b>R$ {{ number_format($valor_total,2,',','.') }}</b><br>
@endif
@if ($credito_selecionado > 0)
Créditos utilizados: <b>R$ {{ number_format($credito_selecionado,2,',','.') }}</b><br>
@endif
<br>
Horário selecionado:<br>
<ul>
@foreach ($horarios as $h)
    <li>Entrada às <b>{{ $h }}</b> e saída às <b>{{ \Carbon\Carbon::createFromFormat('H:i',$h)->addHour()->format('H:i') }}</b></li>
@endforeach
</ul>