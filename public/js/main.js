



// sala detalhe datepicker
//$(function() {
//    $( "#datepicker" ).datepicker();
//});

// sala detalhe galeria
// var galleryThumbs = new Swiper('.gallery-thumbs', {
//     spaceBetween: 10,
//     slidesPerView: 4,
//     loop: true,
//     freeMode: true,
//     loopedSlides: 5, //looped slides should be the same
//     watchSlidesVisibility: true,
//     watchSlidesProgress: true,
// });
// var galleryTop = new Swiper('.gallery-top', {
//     spaceBetween: 10,
//     loop: true,
//     loopedSlides: 5, //looped slides should be the same
//     navigation: {
//       nextEl: '.swiper-button-next',
//       prevEl: '.swiper-button-prev',
//     },
//     thumbs: {
//       swiper: galleryThumbs,
//     },
// });


// botões horário ativos / inativos
var elements = document.getElementsByClassName('btn-select-horario')

function activeHandler() {

    event.preventDefault()

    let hasClass = this.classList.contains('btn-default')

    if (hasClass) {
        this.classList.remove('btn-default')
        this.classList.add('btn-default-outline')
    } else {
        this.classList.add('btn-default')
        this.classList.remove('btn-default-outline')
    }

}

for (var i = 0; i < elements.length; i++) {
    elements[i].addEventListener("click", activeHandler, false)
}
// botões horário ativos / inativos
