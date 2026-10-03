$(document).ready(function(){

$(window).scroll(function(){
  if ($(window).scrollTop() >= 30) {
    $('.navigation').addClass('fixed');
   }
   else {
    $('.navigation').removeClass('fixed');
   }
});


$(document).on("submit",'.form-data',function(eform){
    eform.preventDefault();
    
    $.ajax({
        
        url:"modal/inquery-data.php",
        type:"POST",
        data:$(".form-data").serialize(),
        success:function(einuq){
           
            if(einuq = "succ"){
                alert("order conform");
            }
            else{
                alert("Error Massage");
            }
        }
        
    })
    
    
});


$(document).on("submit",'.contact_info_data',function(cform){
    cform.preventDefault();
    
    $.ajax({
        
        url:"modal/contact-inquery.php",
        type:"POST",
        data:$(".contact_info_data").serialize(),
        success:function(cinq){
            alert(cinq);
        }
        
    })
    
    
});




$('.testimonail_carousel_sec').owlCarousel({

    loop:false,

    margin:0,

    responsiveClass:true,

    nav:false,

    dots:false,

    responsive:{

        0:{

            items:1,
            dots:false,
            loop:true,
            autoplay:true,

        },

        600:{

            items:1,
            dots:false,
            loop:true,
            autoplay:true,

        },

        1000:{

            items:1,

            loop:true,

            autoplay:true,

        }

    }

});

$('.slider_carousel_sec').owlCarousel({

    loop:true,

    margin:0,

    responsiveClass:true,

    nav:false,

    dots:false,

    responsive:{

        0:{

            items:1,
            dots:false,
            loop:true,
            autoplay:true,

        },

        600:{

            items:1,
            dots:false,
            loop:true,
            autoplay:true,

        },

        1000:{

            items:1,

            loop:true,

            autoplay:true,

        }

    }

});


$('.custom_informative_carousel_sec').owlCarousel({

    loop:false,

    margin:0,

    responsiveClass:true,

    nav:false,

    dots:false,

    responsive:{

        0:{

            items:1,
            dots:false,
            loop:true,
            autoplay:true,

        },

        600:{

            items:1,
            dots:false,
            
            autoplay:true,

        },

        1000:{

            items:1,

            

            autoplay:true,

        }

    }

});

})