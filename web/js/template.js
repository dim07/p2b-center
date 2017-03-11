


/* Theme Name: Worthy - Free Powerful Theme by HtmlCoder
 * Author:HtmlCoder
 * Author URI:http://www.htmlcoder.me
 * Version:1.0.0
 * Created:November 2014
 * License: Creative Commons Attribution 3.0 License (https://creativecommons.org/licenses/by/3.0/)
 * File Description: Initializations of plugins 
 */

(function($){
    $(document).ready(function(){
	
        hljs.initHighlightingOnLoad();

        // Datetime picker initialization.
        // See http://eonasdan.github.io/bootstrap-datetimepicker/
        $('[data-toggle="datetimepicker"]').datetimepicker({
            icons: {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-check-circle-o',
                clear: 'fa fa-trash',
                close: 'fa fa-remove'
            },
            locale: 'ru'
            //inline: true
        });
        
        var isLegalEntity = $("#stage_order_isLegalEntity");
        if(isLegalEntity.length>0) {
            var contractor = $( "#stage_order_Contractor" );
            var isp = $( "#stage_order_UserIsp" );
            if(isLegalEntity.is(':checked') ) {
                contractor.prop( "disabled", false );
                isp.prop( "disabled", true );
                contractor.closest(".form-group").show();
                isp.closest(".form-group").hide();

            } else {
                contractor.prop( "disabled", true );
                isp.prop( "disabled", false );
                contractor.closest(".form-group").hide();
                isp.closest(".form-group").show();
            }
        }    
		//$(".banner-image").backstretch('images/banner.jpg');
		
		  
		
		// Fixed header
		//-----------------------------------------------
		$(window).scroll(function() {
			if (($(".header.fixed").length > 0)) { 
				if(($(this).scrollTop() > 0) 
                                //  && ($(window).width() > 767)
                           ) 

                        {
					$("body").addClass("fixed-header-on");
				} else {
					$("body").removeClass("fixed-header-on");
				}
			};
			if ($(this).scrollTop() > 100) {
				$('.scroll-up').fadeIn();
			} else {
				$('.scroll-up').fadeOut();
			}
		});
		
		//$('a[href*=#]').bind("click", function(e){
        //   
		//	var anchor = $(this);
		//	$('html, body').stop().animate({
		//		scrollTop: $(anchor.attr('href')).offset().top
		//	}, 1000);
		//	e.preventDefault();
		//});

		
		$(window).load(function() {
			// will first fade out the loading animation
		jQuery(".status").fadeOut();
        // will fade out the whole DIV that covers the website.
		jQuery(".preloader").delay(1000).fadeOut("slow");
			if (($(".header.fixed").length > 0)) { 
				if(($(this).scrollTop() > 0) 
                                    //&& ($(window).width() > 767)
                                  ) 
                                {
					$("body").addClass("fixed-header-on");
				} else {
					$("body").removeClass("fixed-header-on");
				}
			};
                    var $ai = $('#scrollspy-r > ul.side-pills > li.active > a > h5 > i');
                    $ai.removeClass("fa-chevron-left");
                    $ai.addClass("fa-circle");    
		});

		//Scroll Spy
		//-----------------------------------------------
		if($(".scrollspy").length>0) {
			$("body").addClass("scroll-spy");
			$('body').scrollspy({ 
				target: '.scrollspy',
				offset: 152
			});
		}
                
                $('#scrollspy-r').on('activate.bs.scrollspy', function () {
                    var $ai = $('#scrollspy-r > ul.side-pills > li.active > a > h5 > i');
                    $ai.removeClass("fa-chevron-left");
                    $ai.addClass("fa-circle");
                    var $nai = $('#scrollspy-r > ul.side-pills > li:not(.active) > a > h5 > i.fa-circle');
                    $nai.removeClass("fa-circle");
                    $nai.addClass("fa-chevron-left");
                    
                });
                
                $('[data-spy="scroll"]').each(function () {
                    var $spy = $(this).scrollspy('refresh');
                });
		//Smooth Scroll
		//-----------------------------------------------
//		if ($(".smooth-scroll").length>0) {
//			$('.smooth-scroll a[href*=#]:not([href=#]), a[href*=#]:not([href=#]).smooth-scroll').click(function() {
//				if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
//					var target = $(this.hash);
//					target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
//					if (target.length) {
//						$('html,body').animate({
//							scrollTop: target.offset().top-151
//						}, 1000);
//						return false;
//					}
//				}
//			});
//		}

		// Animations
		//-----------------------------------------------
		if (($("[data-animation-effect]").length>0) && !Modernizr.touch) {
			$("[data-animation-effect]").each(function() {
				var $this = $(this),
				animationEffect = $this.attr("data-animation-effect");
				if(Modernizr.mq('only all and (min-width: 768px)') && Modernizr.csstransitions) {
					$this.appear(function() {
						setTimeout(function() {
							$this.addClass('animated object-visible ' + animationEffect);
						}, 400);
					}, {accX: 0, accY: -130});
				} else {
					$this.addClass('object-visible');
				}
			});
		};

		// Isotope filters
		//-----------------------------------------------
		if ($('.isotope-container').length>0) {
			$(window).load(function() {
				$('.isotope-container').fadeIn();
				var $container = $('.isotope-container').isotope({
					itemSelector: '.isotope-item',
					layoutMode: 'masonry',
					transitionDuration: '0.6s',
					filter: "*"
				});
				// filter items on button click
				$('.filters').on( 'click', 'ul.nav li a', function() {
					var filterValue = $(this).attr('data-filter');
					$(".filters").find("li.active").removeClass("active");
					$(this).parent().addClass("active");
					$container.isotope({ filter: filterValue });
					return false;
				});
			});
		};

		//Modal
		//-----------------------------------------------
		if($(".modal").length>0) {
			$(".modal").each(function() {
				$(".modal").prependTo( "body" );
			});
		}
	}); // End document ready
        
    // Handling the modal confirmation message.
    $(document).on('submit', 'form[data-confirmation]', function (event) {
        var $form = $(this),
            $confirm = $('#confirmationModal');

        if ($confirm.data('result') !== 'yes') {
            //cancel submit event
            event.preventDefault();

            $confirm
                .off('click', '#btnYes')
                .on('click', '#btnYes', function () {
                    $confirm.data('result', 'yes');
                    $form.find('input[type="submit"]').attr('disabled', 'disabled');
                    $form.submit();
                })
                .modal('show');
        }
    });    

})(this.jQuery);

// bootstrap alert activate
$().alert();

//Navigation Menu Slider
    $('#nav-expander').on('click',function(e){
            e.preventDefault();
            $('body').toggleClass('nav-expanded');
            if ($('#nav-expander > i').hasClass('fa-chevron-left')) {
                $(this).html('<i class="fa fa-bars fa-lg white"></i><i class="fa fa-chevron-right fa-lg white"></i>');
            } else {
                $(this).html('<i class="fa fa-chevron-left fa-lg white"></i><i class="fa fa-bars fa-lg white"></i>');
            }
    });
    $('#nav-close').on('click',function(e){
            e.preventDefault();
            $('body').removeClass('nav-expanded');
            $('#nav-expander').html('<i class="fa fa-chevron-left fa-lg white"></i><i class="fa fa-bars fa-lg white"></i>');
    });

/**
 * Author: Heather Corey
 * jQuery Simple Parallax Plugin
 *
 */
 
(function($) {
 
    $.fn.parallax = function(options) {
 
        var windowHeight = $(window).height();
 
        // Establish default settings
        var settings = $.extend({
            speed        : 0.15
        }, options);
 
        // Iterate over each object in collection
        return this.each( function() {
 
        	// Save a reference to the element
        	var $this = $(this);
 
        	// Set up Scroll Handler
        	$(document).scroll(function(){
 
    		        var scrollTop = $(window).scrollTop();
            	        var offset = $this.offset().top;
            	        var height = $this.outerHeight();
 
    		// Check if above or below viewport
			if (offset + height <= scrollTop || offset >= scrollTop + windowHeight) {
				return;
			}
 
			var yBgPosition = Math.round((offset - scrollTop) * settings.speed);
 
                 // Apply the Y Background Position to Set the Parallax Effect
    			$this.css('background-position', 'center ' + yBgPosition + 'px');
                
        	});
        });
    }
}(jQuery));


(function($) {
    var cls = $("#close-affix");
    var other = $(".affix-panel li .btn-social-icon");
    var affx = $(".affix-panel");
    cls.on('click',function(e){
            e.preventDefault();
            other.animate({ opacity: "toggle" },"slow");
            if ( $("#close-affix > i").hasClass("fa-navicon")) {
                cls.html('<i class="fa fa-close"></i>');
            } else {
                cls.html('<i class="fa fa-navicon"></i>');
            }    
//            affx.animate({ width: "toggle" });
//            $('body').removeClass('nav-expanded');
//            $('#nav-expander').html('<i class="fa fa-chevron-left fa-lg white"></i><i class="fa fa-bars fa-lg white"></i>');
//            if (other.is(":visible")) {
//                other.animate({ width: "toggle" });
//            } 
        //other.hide("slow");
    });
    
}(jQuery));

Share = {
    vkontakte: function(purl, ptitle, pimg, text) {
        url  = 'http://vkontakte.ru/share.php?';
        url += 'url='          + encodeURIComponent(purl);
        url += '&title='       + encodeURIComponent(ptitle);
        url += '&description=' + encodeURIComponent(text);
        url += '&image='       + encodeURIComponent(pimg);
        url += '&noparse=true';
        Share.popup(url);
    },
    odnoklassniki: function(purl, text) {
        url  = 'http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1';
        url += '&st.comments=' + encodeURIComponent(text);
        url += '&st._surl='    + encodeURIComponent(purl);
        Share.popup(url);
    },
    facebook: function(purl, ptitle, pimg, text) {
        url  = 'http://www.facebook.com/sharer.php?s=100';
        url += '&p[title]='     + encodeURIComponent(ptitle);
        url += '&p[summary]='   + encodeURIComponent(text);
        url += '&p[url]='       + encodeURIComponent(purl);
        url += '&p[images][0]=' + encodeURIComponent(pimg);
        Share.popup(url);
    },
    twitter: function(purl, ptitle) {
        url  = 'http://twitter.com/share?';
        url += 'text='      + encodeURIComponent(ptitle);
        url += '&url='      + encodeURIComponent(purl);
        url += '&counturl=' + encodeURIComponent(purl);
        Share.popup(url);
    },
    mailru: function(purl, ptitle, pimg, text) {
        url  = 'http://connect.mail.ru/share?';
        url += 'url='          + encodeURIComponent(purl);
        url += '&title='       + encodeURIComponent(ptitle);
        url += '&description=' + encodeURIComponent(text);
        url += '&imageurl='    + encodeURIComponent(pimg);
        Share.popup(url);
    },
    googleplus: function(purl) {
        url  = 'https://plus.google.com/share?';
        url += 'url='    + encodeURIComponent(purl);
        Share.popup(url);
    },
    linkedin: function(purl, ptitle) {
        url  = 'http://www.linkedin.com/shareArticle?';
        url += 'url='    + encodeURIComponent(purl);
        url += '&title=' + encodeURIComponent(ptitle);
        Share.popup(url);
    },
    pinterest: function(purl, pimg, text) {
        url  = 'https://pinterest.com/pin/create/bookmarklet/?';
        url += 'media='      + encodeURIComponent(pimg);
        url += '&url='       + encodeURIComponent(purl);
        url += '&description=' + encodeURIComponent(text);
        Share.popup(url);
    },
    reddit: function(purl, ptitle) {
        url  = 'http://reddit.com/submit?';
        url += 'url='    + encodeURIComponent(purl);
        url += '&title=' + encodeURIComponent(ptitle);
        Share.popup(url);
    },
    popup: function(url) {
        window.open(url,'','toolbar=0,status=0,width=626,height=436');
    }
};

$('#filters').on('shown.bs.collapse', function () {
    $("#eye").removeClass("fa-eye").addClass("fa-eye-slash");
});
$('#filters').on('hidden.bs.collapse', function () {
    $("#eye").removeClass("fa-eye-slash").addClass("fa-eye");
});
/* ------------------------------------------------------------ *\
|* ------------------------------------------------------------ *|
|* Some JS to help with our search
|* ------------------------------------------------------------ *|
\* ------------------------------------------------------------ */
//(function(window){
//    
//    var cls = document.querySelector("#close-affix");
//    var other = document.querySelector(".affix-panel .btn-social-icon");
//    cls.addEventListener("click",function(){
//        if (other.is(":visible")) {
//         other.animate({ width: "toggle" });
//        } 
//        //other.hide("slow");
//    });
//    
//
//	// get vars
//	var searchEl = document.querySelector("#input");
//	var labelEl = document.querySelector("#label");
//
//	// register clicks and toggle classes
//	labelEl.addEventListener("click",function(){
//		if (classie.has(searchEl,"focus")) {
//			classie.remove(searchEl,"focus");
//			classie.remove(labelEl,"active");
//		} else {
//			classie.add(searchEl,"focus");
//			classie.add(labelEl,"active");
//		}
//	});
//
//	// register clicks outisde search box, and toggle correct classes
//	document.addEventListener("click",function(e){
//		var clickedID = e.target.id;
//		if (clickedID != "search-terms" && clickedID != "search-label") {
//			if (classie.has(searchEl,"focus")) {
//				classie.remove(searchEl,"focus");
//				classie.remove(labelEl,"active");
//			}
//		}
//	});
//}(window));