@extends('layouts.public')

@section('javascript')
<script>
    $(function () {
        $("#data").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            minDate: "{{ $data_inicial->format('d/m/Y') }}"
        });
        var selecionados = [];
        $(".select-horario").on('click',function() {
            var time = $(this).attr('data-time');
            var btn = $(this);
            
            if (btn.hasClass('btn-default-outline')) {
                // Selecionar
                btn.removeClass('btn-default-outline').addClass('btn-default');
                selecionados.push(time);
            } else {
                // Desmarcar
                btn.removeClass('btn-default').addClass('btn-default-outline');
                selecionados = selecionados.filter(function(t) { return t !== time; });
            }
            
            $('#horarios_selecionados').val(selecionados.join(','));
            console.log('Horários selecionados:', $('#horarios_selecionados').val());
        });
        $("#data").on('change', function() {
            $('#data_selecionada').val($(this).val());
            $("#seleciona_data").submit();
        });
    });

</script>

<!-- swiper -->
<script>
    $(document).ready(function(){

        var topSlide = $("#topSlide")
        var thumbSlide = $("#thumbSlide")
        var slidesPerPage = {{ count($sala->imagens) }}; //globaly define number of elements per page
        var syncedSecondary = true;

        topSlide.owlCarousel({
            autoplay: true,
            center: true,
            nav: true,
            items: 1,
            loop: true,
            responsiveRefreshRate: 200,
        }).on('changed.owl.carousel', syncPosition);

        thumbSlide.on('initialized.owl.carousel', function() {
            thumbSlide.find(".owl-item").eq(0).addClass("current");
        })
        .owlCarousel({
            items: slidesPerPage,
            margin: 10,
            dots: true,
            nav: true,
            smartSpeed: 200,
            slideSpeed: 500,
            slideBy: slidesPerPage,
            responsiveRefreshRate: 100
        }).on('changed.owl.carousel', syncPosition2);

    function syncPosition(el) {
        //if you set loop to false, you have to restore this next line
        //var current = el.item.index;

        //if you disable loop you have to comment this block
        var count = el.item.count - 1;
        var current = Math.round(el.item.index - (el.item.count / 2) - .5);

        if (current < 0) {
            current = count;
        }
        if (current > count) {
            current = 0;
        }

        //end block

        thumbSlide
            .find(".owl-item")
            .removeClass("current")
            .eq(current)
            .addClass("current");
        var onscreen = thumbSlide.find('.owl-item.active').length - 1;
        var start = thumbSlide.find('.owl-item.active').first().index();
        var end = thumbSlide.find('.owl-item.active').last().index();

        if (current > end) {
            thumbSlide.data('owl.carousel').to(current, 100, true);
        }
        if (current < start) {
            thumbSlide.data('owl.carousel').to(current - onscreen, 100, true);
        }
    }

    function syncPosition2(el) {
        if (syncedSecondary) {
            var number = el.item.index;
            topSlide.data('owl.carousel').to(number, 100, true);
        }
    }

    thumbSlide.on("click", ".owl-item", function(e) {
        e.preventDefault();
        var number = $(this).index();
        topSlide.data('owl.carousel').to(number, 300, true);
    });


    });
</script>
@endsection

@section('content')
<div class="container my-5" id="contentWrapper">
    <div class="row text-center">
        <div class="col-12">
        <h1 class="title">{{ $sala->nome }}</h1>
        <p class="subtitle">{!! $sala->descricao !!}</p>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="row">
        <div class="col">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb pt-5">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Salas</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $sala->nome }}</li>
            </ol>
        </nav>
        </div>
    </div>
    <!--Breadcrumb -->
    <div class="row text-left">
        <!-- Imagens Sala -->


        <div class="col-12 col-md-6">
            <div class="row">
                <div id="topSlide" class="owl-carousel" style="height: 450px;">
                    @forelse ($sala->imagens as $i)
                        <div class="topSlide">
                            <img src="{{ $i->caminho }}" alt="">
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>

            <div class="row">
                @if ( count($sala->imagens) > 1  )
                <div id="thumbSlide" class="owl-carousel">
                    @forelse ($sala->imagens as $i)
                        <div class="thumbSlide">
                            <img src="{{ $i->caminho }}" alt="">
                        </div>
                    @empty
                    @endforelse
                </div>
                @endif
            </div>
        </div>



        <!-- Imagens Sala -->
        <!-- Destalhes Sala -->
        <div class="col-12 col-md-6">

            <form class="form" action="{{ route('ver_sala',( $sala->slug )) }}" id="seleciona_data" method="post">
            @csrf
                <input type="hidden" name="data_selecionada" id="data_selecionada" value="{{ $data_inicial->format('Y-m-d') }}">
            </form>

            <form class="form" action="{{ route('checkout_agendamento') }}" method="post">
            @csrf
                <input type="hidden" name="sala" value="{{ $sala->id }}">
                <div class="form-group">
                    <label for="">Data para agendamento</label>
                    <div class="input-group mb-2">
                        <input type="text" id="data" name="data_agendamento" class="form-control" value="{{ $data->format('d/m/Y') }}" readonly>
                        <div class="input-group-append">
                            <div class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                <label for="">Selecione abaixo um dos horários disponíveis de entrada:</label>
                <div class="sala-agenda seletor-horario">
                    <input type="hidden" name="horarios_selecionados" id="horarios_selecionados" value="">
                    <div class="mb-3">
                        @forelse ($horarios as $k => $h)
                            @php 
                                $is_occupied = in_array($h, $ocorrencias);
                                $is_past = $agora >= \Carbon\Carbon::createfromformat('Y-m-d H:i',$data->format('Y-m-d').' '.$h);
                            @endphp
                            <button type="button" 
                                    data-id="{{ $k }}" 
                                    data-time="{{ $h }}"
                                    class="btn @if($is_occupied || $is_past) btn-default-outline @else btn-default-outline btn-select-horario select-horario @endif mx-2 mb-2" 
                                    @if ($is_occupied || $is_past) disabled title="Indisponível" @endif>
                                {{ $h }}
                            </button>
                        @empty
                        @endforelse
                    </div>
                </div>
                </div>
                <div class="form-group">
                <button type="submit" class="btn btn-default my-3">Solicitar agendamento</button>
                </div>
            </form>
        </div>
        <div class="alert alert-light" role="alert">
            <h6>
                * Período mínimo de 1 hora de agendamento.<br>
                ** 15 minutos de tolerância e os 15 minutos finais para limpeza da sala.
            </h6>
        </div>

        <!-- Destalhes Sala -->
    </div>
</div>
@endsection

<style>
    .topSlide img {
        height: 450px;
        object-fit: cover;
        padding-bottom: 10px;
    }
    .thumbSlide img {
        height: 100px;
        object-fit: cover;
    }
</style>
