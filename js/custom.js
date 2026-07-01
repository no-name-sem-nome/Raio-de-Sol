
  (function ($) {
  
  "use strict";

    // NAVBAR
    $('.navbar-nav .nav-link').click(function(){
        $(".navbar-collapse").collapse('hide');
    });

    // REVIEWS CAROUSEL
    $('.reviews-carousel').owlCarousel({
        center: true,
        loop: true,
        nav: true,
        dots: false,
        autoplay: true,
        autoplaySpeed: 300,
        smartSpeed: 500,
        responsive:{
          0:{
            items:1,
          },
          768:{
            items:2,
            margin: 100,
          },
          1280:{
            items:2,
            margin: 100,
          }
        }
    });

    // Banner Carousel
    var myCarousel = document.querySelector('#myCarousel')
    var carousel = new bootstrap.Carousel(myCarousel, {
      interval: 1500,
    })

    // REVIEWS NAVIGATION
    function ReviewsNavResize(){
      $(".navbar").scrollspy({ offset: -94 });

      var ReviewsOwlItem = $('.reviews-carousel .owl-item').width();

      $('.reviews-carousel .owl-nav').css({'width' : (ReviewsOwlItem) + 'px'});
    }

    $(window).on("resize", ReviewsNavResize);
    $(document).on("ready", ReviewsNavResize);

    // BOOKING FORM
    $('#bookingForm').on('submit', function (e) {
      e.preventDefault();

      var $form     = $(this);
      var $btn      = $('#submit-button');
      var $feedback = $('#form-feedback');

      $btn.prop('disabled', true).text('Enviando...');
      $feedback.hide();

      $.ajax({
        url: 'send-email.php',
        method: 'POST',
        data: $form.serialize(),
        dataType: 'json',
        success: function (res) {
          $feedback
            .removeClass('text-danger text-success')
            .addClass(res.success ? 'text-success' : 'text-danger')
            .text(res.message)
            .show();
          if (res.success) {
            $form[0].reset();
          }
        },
        error: function () {
          $feedback
            .removeClass('text-success')
            .addClass('text-danger')
            .text('Erro de conexão. Por favor, tente novamente.')
            .show();
        },
        complete: function () {
          $btn.prop('disabled', false).text('Agendar');
        }
      });
    });

    // HREF LINKS
    $('a[href*="#"]').click(function (event) {
      if (
        location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
        if (target.length) {
          event.preventDefault();
          $('html, body').animate({
            scrollTop: target.offset().top - 74
          }, 1000);
        }
      }
    });
    
  })(window.jQuery);
