@extends('layouts.admin')

@section('style')
<style>
    .api-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        transition: transform 0.3s;
    }
    .api-card:hover { rotate title: 0; }
    .nav-pills .nav-link.active {
        background-color: #007bff;
        border-radius: 10px;
    }
    .nav-pills .nav-link {
        color: #6c757d;
        font-weight: 600;
        margin-right: 10px;
    }
    .token-display {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        font-family: monospace;
        border: 1px dashed #dee2e6;
    }
    .doc-section {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border-left: 5px solid #007bff;
    }
    code {
        color: #d63384;
    }
    pre {
        background: #272822;
        color: #f8f8f2;
        padding: 15px;
        border-radius: 8px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="admin-title mb-2">Configurações de API & Integrações</h1>
            <p class="text-muted">Gerencie suas chaves de acesso e conexões externas com CRMs.</p>
        </div>
    </div>

    <ul class="nav nav-pills mb-4" id="apiTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tokens-tab" data-toggle="pill" href="#tokens" role="tab">Tokens de API</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="webhooks-tab" data-toggle="pill" href="#webhooks" role="tab">Webhooks</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="doc-tab" data-toggle="pill" href="#doc" role="tab">Documentação</a>
        </li>
    </ul>

    <div class="tab-content" id="apiTabContent">
        <!-- Tokens Tab -->
        <div class="tab-pane fade show active" id="tokens" role="tabpanel">
            <div class="card api-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Seus Tokens de Acesso</h5>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalNewToken">
                        <i class="fa fa-plus"></i> Novo Token
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover dtable">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Último Uso</th>
                                <th>Criado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tokens as $token)
                            <tr>
                                <td><strong>{{ $token->name }}</strong></td>
                                <td>{{ $token->last_used_at ? $token->last_used_at->format('d/m/Y H:i') : 'Nunca usado' }}</td>
                                <td>{{ $token->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <form action="{{ route('admin.token.revoke', $token->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Tem certeza que deseja revogar este token?')">
                                            <i class="fa fa-trash"></i> Revogar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Webhooks Tab -->
        <div class="tab-pane fade" id="webhooks" role="tabpanel">
            <div class="card api-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Webhooks Ativos</h5>
                    <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalNewWebhook">
                        <i class="fa fa-plug"></i> Configurar Webhook
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>URL de Destino</th>
                                <th>Evento</th>
                                <th>Segredo</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($webhooks as $webhook)
                            <tr>
                                <td><code>{{ $webhook->url }}</code></td>
                                <td><span class="badge badge-info">{{ $webhook->event }}</span></td>
                                <td><small>{{ Str::limit($webhook->secret, 10) }}...</small></td>
                                <td>
                                    <span class="badge badge-{{ $webhook->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ $webhook->status }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.webhook.test', $webhook->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-link text-info p-0 mr-2" title="Testar Envio">
                                            <i class="fa fa-paper-plane"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.webhook.delete', $webhook->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-link text-danger p-0" onclick="return confirm('Excluir este webhook?')" title="Excluir">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Documentation Tab -->
        <div class="tab-pane fade" id="doc" role="tabpanel">
            <div class="card api-card p-4">
                <h3>Guia de Integração da API</h3>
                <hr>
                
                <div class="doc-section">
                    <h5>1. Autenticação</h5>
                    <p>Todas as requisições devem incluir o token no cabeçalho HTTP <code>Authorization</code>:</p>
                    <pre>Authorization: Bearer SEU_TOKEN_AQUI</pre>
                </div>

                <div class="doc-section">
                    <h5>2. Endpoints Disponíveis</h5>
                    
                    <div class="mb-4">
                        <h6 class="text-primary font-weight-bold">Módulo 1: Especialidades e Serviços</h6>
                        <div class="ml-3">
                            <p><span class="badge badge-primary">GET</span> <code>/api/v1/especialidades</code> - Lista especialidades ativas.</p>
                            <p><span class="badge badge-primary">GET</span> <code>/api/v1/servicos/{esp_id}</code> - Lista procedimentos e preços por especialidade.</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-primary font-weight-bold">Módulo 2: Qualificação e Leads</h6>
                        <div class="ml-3">
                            <p><span class="badge badge-success">POST</span> <code>/api/v1/leads/paciente</code> - Registra interesse de paciente (Nome, Telefone, Convênio).</p>
                            <p><span class="badge badge-success">POST</span> <code>/api/v1/leads/medico</code> - Registra interesse de médico (Nome, CRM, Especialidade, Turno).</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-primary font-weight-bold">Módulo 3: Agenda e Disponibilidade (n8n/Google Agenda)</h6>
                        <div class="ml-3">
                            <p><span class="badge badge-primary">GET</span> <code>/api/v1/disponibilidade</code> - Consulta horários livres (Filtros: data, termo).</p>
                            <p><span class="badge badge-success">POST</span> <code>/api/v1/agendamento/reservar</code> - Bloqueia horário temporariamente (v1).</p>
                            <p><span class="badge badge-warning">PATCH</span> <code>/api/v1/agendamento/confirmar</code> - Confirma reserva após Sync com Google Agenda.</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-primary font-weight-bold">Módulo 4: Base de Conhecimento</h6>
                        <div class="ml-3">
                            <p><span class="badge badge-primary">GET</span> <code>/api/v1/regras/locacao</code> - Retorna termos e condições vigentes.</p>
                            <p><span class="badge badge-primary">GET</span> <code>/api/v1/unidades</code> - Lista endereços e horários de funcionamento.</p>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <h6><span class="badge badge-primary">GET</span> <code>/api/v1/salas</code></h6>
                        <p>Retorna a lista de todas as salas cadastradas (Legado).</p>
                    </div>

                    <div class="mb-3">
                        <h6><span class="badge badge-primary">GET</span> <code>/api/v1/agenda</code></h6>
                        <p>Retorna os agendamentos do sistema (Legado).</p>
                    </div>
                </div>

                <div class="doc-section">
                    <h5>3. Webhooks (Saída)</h5>
                    <p>O sistema enviará um JSON via POST para a URL configurada sempre que ocorrerem os eventos selecionados.</p>
                    <p><strong>Payload Exemplo:</strong></p>
                    <pre>
{
    "event": "appointment.created",
    "data": {
        "id": 123,
        "medico": "Dr. House",
        "sala": "Consultório 01",
        "data": "2026-04-01 14:00"
    }
}
                    </pre>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal New Token -->
<div class="modal fade" id="modalNewToken" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.token.generate') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Token de API</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Identificador do Token (Ex: CRM Integração)</label>
                        <input type="text" name="name" class="form-control" required placeholder="Ex: Zaplandia CRM">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Gerar Token</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal New Webhook -->
<div class="modal fade" id="modalNewWebhook" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.webhook.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Configurar Webhook</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>URL de Destino (Endpoint do CRM)</label>
                        <input type="url" name="url" class="form-control" required placeholder="https://seu-crm.com/api/webhook">
                    </div>
                    <div class="form-group">
                        <label>Evento de Gatilho</label>
                        <select name="event" class="form-control">
                            <option value="appointment.created">Novo Agendamento</option>
                            <option value="appointment.canceled">Agendamento Cancelado</option>
                            <option value="user.registered">Novo Médico Cadastrado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar Configuração</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        // Inicialização de tooltips ou dataTables se necessário
    });
</script>
@endsection
