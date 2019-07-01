import $ from 'jquery';
import 'owl.carousel';
$('.owl-carousel').owlCarousel({
    loop:true,
    navRewind:true,
    margin:10,
    nav:true,
    dots:false,
    navText: ['<i class="la la-angle-left"></i>','<i class="la la-angle-right"></i> '],
    responsive:{
        0:{
            items:1
        },
        600:{
            items:3
        },
        1000:{
            items:5
        }
    }
})