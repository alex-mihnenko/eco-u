$(document).ready(function(){
    $(".n-p_list").click(function(e){
        var tag = $(this).data('tag');
        var modalElement = $('.remodal[data-remodal-id="modal5"]');
        modalElement.find('.l-m_title').html('');
        modalElement.find('.modal-content').html('');
        $.get('/?route=ajax/index/ajaxGetProductsByTag', {
            tag:tag
        }, function(msg){
            if(msg) {
                modalElement.find('.l-m_title').html(tag);
                modalElement.find('.modal-content').html(msg);
                initDropDown();
                $('.m-product_submit').click(function(e){
                    e.preventDefault();
                    var pElement = $(this).parents('.modal-product');
                    var product_id = pElement.find('input.product_id').val();
                    var quantity = parseInt(pElement.find('.selectric .label').html());
                    $.post('/?route=checkout/cart/add', {
                        product_id: product_id,
                        quantity: quantity
                    }, function(msg){
                        pElement.find('.m-product_submit').html('Добавлено в корзину');
                        LoadCart();
                    }, "json");
                });
            }
        });
    });
    $(".sticker").sticky({
		topSpacing: 0
	});
	$(".f-c_top").sticky({
		topSpacing: 97,
		className: 'is-sticky2',
	});
	/*** tabs profile ***/
	$('ul.tabs__caption').on('click', 'li:not(.active)', function() {
		$(this)
		.addClass('active').siblings().removeClass('active')
		.closest('.modal-profile').find('.tabs__content').removeClass('active').eq($(this).index()).addClass('active');
	});
	/*** end ***/
	/* select2 */
	var initDropDown = function() {
		var $items = $('.tech:not(.dd-ready)');
		if(!$items.length) {
			return;
		}
		var i = 0;
		$items.each(function() {
			if(i == 10) {
				return false;
			}
			var $item = $(this);
			$item.addClass('dd-ready');
			$item.selectric();
                        var currencyStr = ' руб';
                        $item.parents('.p-o_select').find('.selectric-items').find('li').click(function(e){
                            var quantity = parseFloat($(this).html());
                            var price = parseFloat($(this).parents('.p-o_block').find('meta[itemprop="price"]').attr('content'));
                            console.log(quantity, price);
                            var totalPrice = parseInt(quantity * price);
                            if(totalPrice > 999) currencyStr = ' р';
                            $(this).parents('.p-o_block').find('.p-o_price').html(totalPrice + currencyStr);
                        });
                        $item.parents('.m-product_select').find('.selectric-items').find('li').click(function(e){
                            var quantity = parseFloat($(this).html());
                            var price = parseFloat($(this).parents('.size-0').find('input.product_price').val());
                            var totalPrice = parseInt(quantity * price);
                            if(totalPrice > 999) currencyStr = ' р';
                            $(this).parents('.size-0').find('.m-product_price').html(totalPrice + currencyStr);
                        });
                        $item.parents('.c-p_select').find('.selectric-items').find('li').click(function(e){
                            var quantity = parseFloat($(this).html());
                            var price = parseFloat($(this).parents('.size-0').find('meta[itemprop="price"]').attr('content'));
                            var totalPrice = parseInt(quantity * price);
                            if(totalPrice > 999) currencyStr = ' р';
                            $(this).parents('.size-0').find('.c-p_price').html(totalPrice + currencyStr);
                        });
			i++;
		});
		setTimeout(function() {
				initDropDown();
			}, 10);
	}
	initDropDown();
        
	/* END select2 */
	/*** slick profitable_offer ***/
	$('.slider-profitable_offer').slick({
		autoplay: true,
		autoplaySpeed: 2000000,
		slidesToShow: 5,
		slidesToScroll: 5,
		  responsive: [
		{
		  breakpoint: 374,
		  settings: {
			slidesToShow: 2,
			slidesToScroll: 2, 
		  }
		},
		{
		  breakpoint: 479,
		  settings: {
			slidesToShow: 2,
			slidesToScroll: 2, 
		  }
		},
		{
		  breakpoint: 979,
		  settings: {
			slidesToShow: 2,
			slidesToScroll: 2,
		  }
		},
		{
		  breakpoint: 1199,
		  settings: {
			slidesToShow: 3,
			slidesToScroll: 3,
		  }
		},
		{
		  breakpoint: 1280,
		  settings: {
			slidesToShow: 4,
			slidesToScroll: 4,
		  }
		},
		{
		  breakpoint: 1500,
		  settings: {
			slidesToShow: 4,
			slidesToScroll: 4,
		  }
		}]
	});
	/*** end profitable_offer ***/
	
	/*** tabs catalog ***/
	$('.tabs__block.active').animate({opacity:'1'});
	$('.tabs__block:not(.active)').animate({opacity:'0'});
	$('ul.tabs__catalog').on('click', 'li:not(.active)', function() {
                if($(this).hasClass('modal8')) {
                    $('.qwe2').fadeIn('slow', function() {
                        if(alphabeticScroller === null) {
                            alphabeticScroller = new AlphabeticScrollerProto();
                        } else {
                            alphabeticScroller.refresh();
                        }
                    });
                } else {
                    $('.qwe2').fadeOut('slow');
                }
                if($(this).hasClass('modal9')) {
                    $('.all-l_a2').fadeIn('slow', function() {
                        if(catalogScroller === null) {
                            catalogScroller = new CatalogScrollerProto();
                        } else {
                            catalogScroller.refresh();
                        }
                    });
                } else {
                    $('.all-l_a2').fadeOut('slow');
                }
		$(this)
		.addClass('active').siblings().removeClass('active')
		.closest('.fond-catalog').find('.tabs__block').animate({opacity:'0'}, 300).removeClass('active').eq($(this).index()).animate({opacity:'1'}, 300).addClass('active');
		var $window = $(window);
		var $columnizer = $('.auto-columnizer');
		var columnizerHTML = $columnizer.html();
		function checkWidth0() {
				$columnizer.find('.no-pictures').each(function() {
					var $this = $(this);
					$columnizer.append($this);
				});
				$columnizer.find('.column').remove();
				$columnizer.find('br').remove();
			var windowsize789 = $window.width();
			if (windowsize789 < 374) {
				var checkWidth1 = function() {
					if($columnizer.width()) {
						$columnizer.columnize({width: 106});
					}
				}
				checkWidth1();
			} else
			if (windowsize789 < 767) {
					var checkWidth2 = function() {
						if($columnizer.width()) {
							$columnizer.columnize({width: 125});
						}
					}
					checkWidth2();
			} else
			if (windowsize789 < 979) {
					var checkWidth4 = function() {
						if($columnizer.width()) {
							$columnizer.columnize({width: 125});
						}
					}
					checkWidth4();
			}
			else {
				var checkWidth3 = function() {
					if($columnizer.width()) {
						$columnizer.columnize({width: 280});
					}
				}
				checkWidth3();
			}
		}
		checkWidth0();
		$(window).resize(checkWidth0);

		$('.list-products').ddscrollSpy({
			highlightclass: "selected2",
			scrolltopoffset: -185,
		});
		$('.list-alphabetic').ddscrollSpy({
			highlightclass: "selected",
			scrolltopoffset: -200,
		}); 
		$('.list-tabs2').ddscrollSpy({
			highlightclass: "selected3",
			scrolltopoffset: -200,
		});
	});
	/*** ***/
	$('.tabs__catalog li.modal8').on( 'click', function(){
		
		if(!$(this).hasClass('active')) {
			setTimeout(function() {
				$('.list-alphabetic li:first-child a').trigger('click');
			}, 100);
			
		} else {
			e.preventDefault();
			return false;
		}
	});
	$('.tabs__catalog li.modal9:not(.active)').on( 'click', function(e){

		if(!$(this).hasClass('active')) {
			setTimeout(function() {
			$('.list-products li:first-child a').trigger('click');
			}, 100);
			
		} else {
			e.preventDefault();
			return false;
		}
	});
	
	
	
	
	$('.tabs__catalog li.modal-hide').on( 'click', function(){
		
		setTimeout(function() {
			var distanceTop = $('.fond-catalog').offset().top;
			$(window).scrollTop(distanceTop);
		}, 50);
	});

	
	$('.list-products').ddscrollSpy({
		highlightclass: "selected2",
		scrolltopoffset: -185,
	});
	$('.list-alphabetic').ddscrollSpy({
		highlightclass: "selected",
		scrolltopoffset: -200,
	}); 
	var $window2 = $(window);
    var checkWidthTabs = function() {
        var windowsize = $window2.width();
        if (windowsize < 767) {
			var checkWidth333 = function() {
				$('.list-tabs2').ddscrollSpy({
					highlightclass: "selected3",
					scrolltopoffset: -110,
				});
				$('.list-m_a').ddscrollSpy({
					highlightclass: "selected",
					scrolltopoffset: -150,
				});
				/*
				$('.tabs__catalog li.modal8').on( 'click', function(){
					$(this).attr("data-remodal-target", "modal8");
				});
				*/
				/*
				$('.tabs__catalog li.modal9').on( 'click', function(){
					$(this).attr("data-remodal-target", "modal9");
					$('.js-modal8').removeClass("js-modal8");
				});
				*/
				$('.tabs__catalog li.modal-hide').on( 'click', function(){
					$('.js-modal8').removeClass("js-modal8");
				});
				/**/
				/*
				$(window).scroll(function(){
					if ($('.fond-catalog').length) {	
						var distanceTop = $('.fond-catalog').offset().top ;
						$(".button-alphabetic").hide();
						if  ($(window).scrollTop() > distanceTop) {
							$(".button-alphabetic").show();
						}
					}
				});
				$(window).scroll(function(){
					if ($('.fond-catalog').length) {	
						var distanceTop = $('.fond-catalog').offset().top ;
						$(".button-tabs2").hide();
						if  ($(window).scrollTop() > distanceTop) {
							$(".button-tabs2").show();
						}
					}
				});
				*/
				$('.list-m_a li a').on( 'click', function(){
					var inst8 = $('[data-remodal-id=modal8]').remodal();
					inst8.close();
				});
				$('.modal-tabs2 li a').on( 'click', function(){
					var inst9 = $('[data-remodal-id=modal9]').remodal();
					inst9.close();
				});
				$('.qwe').removeClass('dragscroll');
				$('.qwe').removeClass('vertical');
			}
			checkWidth333();
        } else if (windowsize < 979) {
			var checkWidth222 = function() {
				$('.list-tabs2').ddscrollSpy({
					highlightclass: "selected3",
					scrolltopoffset: -200,
				});
				$('.list-m_a').ddscrollSpy({
					highlightclass: "selected",
					scrolltopoffset: -270,
				});
				$('.tabs__catalog li').click(function () {
					setTimeout(function() {
						$('[data-remodal-id=modal8], [data-remodal-id=modal9]').remodal();
						return false;
					}, 1);
				});
				/*
				$('.tabs__catalog li.modal8').on( 'click', function(){
					var inst812 = $('[data-remodal-id=modal8]').remodal();
					inst812.open();
				});
				
				
				$('.tabs__catalog li.modal9').on( 'click', function(){
					var inst912 = $('[data-remodal-id=modal9]').remodal();
					inst912.open();
				});
				*/
				////////
				$('.list-m_a li a').on( 'click', function(){
					var inst8 = $('[data-remodal-id=modal8]').remodal();
					inst8.close();
				});
				$('.modal-tabs2 li a').on( 'click', function(){
					var inst9 = $('[data-remodal-id=modal9]').remodal();
					inst9.close();
				});
				/**/
				/*
				$('.tabs__catalog li.modal9').on( 'click', function(){
					$(this).attr("data-remodal-target", "modal9");
					$('.js-modal8').removeClass("js-modal8");
				});
				*/
				/*
				$(window).scroll(function(){
					if ($('.fond-catalog').length) {	
						var distanceTop = $('.fond-catalog').offset().top ;
						$(".button-alphabetic").hide();
						if  ($(window).scrollTop() > distanceTop) {
							$(".button-alphabetic").show();
						}
					}
				});
				$(window).scroll(function(){
					if ($('.fond-catalog').length) {	
						var distanceTop = $('.fond-catalog').offset().top ;
						$(".button-tabs2").hide();
						if  ($(window).scrollTop() > distanceTop) {
							$(".button-tabs2").show();
						}
					}
				});
				*/
				/**/
				$('.qwe').removeClass('dragscroll');
				$('.qwe').removeClass('vertical');
			}
			checkWidth222();
			
        } else if (windowsize < 1279) {
			var checkWidth555 = function() {
				
				$('.list-tabs2').ddscrollSpy({
					highlightclass: "selected3",
					scrolltopoffset: -200,
				});
				$('.list-m_a').ddscrollSpy({
					highlightclass: "selected",
					scrolltopoffset: -270,
				});
				
				$('.tabs__catalog li').click(function () {
					setTimeout(function() {
						$('[data-remodal-id=modal8], [data-remodal-id=modal9]').remodal();
						return false;
					}, 1);
				});	
				$('.tabs__catalog li').click(function () {
					setTimeout(function() {
						$('[data-remodal-id=modal8], [data-remodal-id=modal9]').remodal();
						return false;
					}, 1);
				});
				
				$('.list-m_a li a').on( 'click', function(){
					var inst8 = $('[data-remodal-id=modal8]').remodal();
					inst8.close();
				});
				$('.modal-tabs2 li a').on( 'click', function(){
					var inst9 = $('[data-remodal-id=modal9]').remodal();
					inst9.close();
				});
				/**/
				/*
				$(window).scroll(function(){
					if ($('.fond-catalog').length) {	
						var distanceTop = $('.fond-catalog').offset().top ;
						$(".button-alphabetic").hide();
						if  ($(window).scrollTop() > distanceTop) {
							$(".button-alphabetic").show();
						}
					}
				});
				$(window).scroll(function(){
					if ($('.fond-catalog').length) {	
						var distanceTop = $('.fond-catalog').offset().top ;
						$(".button-tabs2").hide();
						if  ($(window).scrollTop() > distanceTop) {
							$(".button-tabs2").show();
						}
					}
				});
				*/
				/**/
				$('.qwe').removeClass('dragscroll');
				$('.qwe').removeClass('vertical');
			}
			checkWidth555();
			
        }
		else {
			var checkWidth111 = function() {
				$(".button-alphabetic").hide();
				$('.list-m_a').ddscrollSpy({
					highlightclass: "selected",
					scrolltopoffset: -201,
				});
				$('.list-tabs2').ddscrollSpy({
					highlightclass: "selected3",
					scrolltopoffset: -185,
				});
				$('.tabs__catalog li.modal8').on( 'click', function(){
					var inst812 = $('[data-remodal-id=modal8]').remodal();
					inst812.close();
				});
				$('.tabs__catalog li.modal9').on( 'click', function(){
					var inst912 = $('[data-remodal-id=modal9]').remodal();
					inst912.close();
				});
		
			}
			checkWidth111();
		}
    }
    checkWidthTabs();
    $(window).resize(checkWidthTabs);
	/*** END tabs catalog  ***/
	/** tooltip **/
	$( function()
	{
		var targets = $( '[rel~=tooltip]' ),
			target  = false,
			tooltip = false,
			title   = false;
		targets.bind( 'mouseenter', function()
		{
			target  = $( this );
			tip     = target.attr( 'title' );
			tooltip = $( '<div id="tooltip"></div>' );
			if( !tip || tip == '' )
				return false;
			target.removeAttr( 'title' );
			tooltip.css( 'opacity', 0 )
				   .html( tip )
				   .appendTo( 'body' );
			var init_tooltip = function()
			{
				if( $( window ).width() < tooltip.outerWidth() * 1.5 )
					tooltip.css( 'max-width', $( window ).width() / 2 );
				else
					tooltip.css( 'max-width', 340 );
	 
				var pos_left = target.offset().left + ( target.outerWidth() / 2 ) - ( tooltip.outerWidth() / 2 ),
					pos_top  = target.offset().top - tooltip.outerHeight() + 113;
	 
				if( pos_left < 0 )
				{
					pos_left = target.offset().left + target.outerWidth() / 2 - 20;
					tooltip.addClass( 'left' );
				}
				else
					tooltip.removeClass( 'left' );
	 
				if( pos_left + tooltip.outerWidth() > $( window ).width() )
				{
					pos_left = target.offset().left - tooltip.outerWidth() + target.outerWidth() / 2 + 20;
					tooltip.addClass( 'right' );
				}
				else
					tooltip.removeClass( 'right' );
	 
				if( pos_top < 0 )
				{
					var pos_top  = target.offset().top + target.outerHeight();
					tooltip.addClass( 'top' );
				}
				else
					tooltip.removeClass( 'top' );
	 
				tooltip.css( { left: pos_left, top: pos_top } )
					   .animate( { top: '+=10', opacity: 1 }, 50 );
			};
	 
			init_tooltip();
			$( window ).resize( init_tooltip );
	 
			var remove_tooltip = function()
			{
				tooltip.animate( { top: '-=10', opacity: 0 }, 50, function()
				{
					$( this ).remove();
				});
	 
				target.attr( 'title', tip );
			};
	 
			target.bind( 'mouseleave', remove_tooltip );
			tooltip.bind( 'click', remove_tooltip );
		});
	});
	/** END tooltip **/
	$(function(){
		var $magicLine2 = $(".magic-line2");
			$(".list-alphabetic li").find("a").click(function() {
				$el6 = $(this);
				leftPos2 = $el6.position().top;
				newWidth2 = $el6.parent().width();
				elem_click_z23 = true;
				$magicLine2
					.data("origTop", leftPos2)
					.data("origWidth", newWidth2);
				$magicLine2.stop().animate({
					top: leftPos2,
					width: newWidth2
				}, function() {
					elem_click_z23 = false;
				});
				if(alphabeticScroller === null) {
					alphabeticScroller = new AlphabeticScrollerProto();
				}
										
				alphabeticScroller.scrollToAlphabeticLetter($el6);
			});
	});
	$(function(){
		var $magicLine3 = $(".magic-line3");
			$(".list-products li").find("a").click(function() {
				elem_click_z24 = true;
				$el7 = $(this);
				leftPos3 = $el7.position().top;
				newWidth3 = $el7.parent().width();
				$magicLine3
					.data("origTop", leftPos3)
					.data("origWidth", newWidth3);
				$magicLine3.stop().animate({
					top: leftPos3,
					width: newWidth3
				}, function() {
					elem_click_z24 = false;
				});
				if(catalogScroller === null) {
					catalogScroller = new CatalogScrollerProto();
				}
				catalogScroller.scrollToCatalogItem($el7);
			});
	});
	/*** **/
	$('.m-product_submit').on( 'click', function(){
		var el = $(this);
            if(el.hasClass("m-product_submit2")) {
                el.removeClass('m-product_submit2');
				el.val("Добавить в корзину");
            } else {
			el.addClass('m-product_submit2');
			el.val("Добавлено в корзину");
        }
	});
	/*  */
	/** dynamic input **/
	$('.f-p_plus').click(function() {
		$('<input type="text" data-name="customer_address" data-target-id="0" class="f-p_input" name="dynamic[]" placeholder="Адрес Доставки" />').fadeIn('slow').appendTo('.f-p_box2');
	});
	/** END dynamic input **/
	/** **/
	$('.liTabs').liTabs({
		duration: 500,		//A string or number determining how long the animation will run
		effect:'hSlide' //clasic, fade, hSlide, vSlide
	});
	/** */
	/* tabs */
	$('.liTabs_2').liTabs({
		duration: 500,		//A string or number determining how long the animation will run
		effect:'hSlide' //clasic, fade, hSlide, vSlide
	});
        $('.liTabs_2').find('li:nth-child(2) a, li:nth-child(3) a').click(function(){
            $('.fond-f-p').remove();
        });
        $('.liTabs_2').find('li:nth-child(2) a, li:nth-child(3) a').css('pointer-events', 'none');
	/* end tabs */
	/* calendar */
	$.datepicker.setDefaults($.extend(
	  $.datepicker.regional["ru"])
	);
	$( "#datepicker" ).datepicker({
		minDate: "-30",			//Минимальная дата которую можно выбрать, т.е. -30 дней от "сейчас"
		maxDate: "+1m +20d",  //Максимальная дата которую можно выбрать, т.е. + 1 месяц, 1 неделя, и 3 дня от "сейчас"
	});
	/* END calendar */
	/* magic-line 5 */
	var isMobile = false;
	$(document).ready( function() {
		if ($('body').width() <= 979) {
			$(".sidebar_right").trigger("sticky_kit:detach");
		}
		if ($('body').width() <= 1280) {
			isMobile = true;
			
			$magicLine = $(".magic-line");
			$el5 = $('.tabs__catalog li.active span');
			if ($el5.length) {	
				leftPos = $el5.position().left;
				newWidth = $el5.parent().width();
				$magicLine
					.data("origLeft", leftPos)
					.data("origWidth", newWidth);
				$magicLine.stop().animate({
					left: leftPos,
					width: newWidth
				});
			}
			$magicLine4 = $(".magic-line4");
			$el8 = $('.t_wrap_1 li a.cur');
			if ($el8.length) {
				leftPos4 = $el8.position().left;
				newWidth4 = $el8.parent().width();
			
				$magicLine4
					.data("origLeft", leftPos4)
					.data("origWidth", newWidth4);
				$magicLine4.stop().animate({
					left: leftPos4,
					width: newWidth4
				});
			}
			$magicLine5 = $(".magic-line5");
			$el9 = $('.t_wrap_2 li a.cur');
			if ($el9.length) {	
				leftPos5 = $el9.position().left;
				newWidth5 = $el9.parent().width();
				$magicLine5
					.data("origLeft", leftPos5)
					.data("origWidth", newWidth5);
				
				$magicLine5.stop().animate({
					left: leftPos5,
					width: newWidth5
				});
			}
		} if (!isMobile) {
			$(function(){
				
			var $el5, leftPos, newWidth,
				$mainNav = $(".tabs__catalog");
				$mainNav.append("<li class='magic-line'></li>");
			var $magicLine = $(".magic-line");
			if ($magicLine.length) {
				$magicLine
					.width($(".active").width())
					.css("left", $(".active span").position().left)
					.data("origWidth", $magicLine.width());

				$(".tabs__catalog li").find("span").click(function() {
					$el5 = $(this);
					leftPos = $el5.position().left;
					newWidth = $el5.parent().width();
					$magicLine
						.data("origLeft", leftPos)
						.data("origWidth", newWidth);
					$magicLine.stop().animate({
						left: leftPos,
						width: newWidth
					});
				});
			}	
			var $el8, leftPos4, newWidth4,
				$mainNav4 = $(".t_wrap_1");
				$mainNav4.append("<li class='magic-line4'></li>");
			var $magicLine4 = $(".magic-line4");
			if ($magicLine4.length) {
				$magicLine4
					.width($("li .cur").width())
					.css("left", $("li a").position().left)
					.data("origLeft", $magicLine4.position().left)
					.data("origWidth", $magicLine4.width());
				$(".t_wrap_1 li").find("a").click(function() {
					$el8 = $(this);
					leftPos4 = $el8.position().left;
					newWidth4 = $el8.parent().width();
					$magicLine4
						.data("origLeft", leftPos4)
						.data("origWidth", newWidth4);
					$magicLine4.stop().animate({
						left: leftPos4,
						width: newWidth4
					});
				});
			}	
			var $el9, leftPos5, newWidth5,
				$mainNav5 = $(".t_wrap_2");
				$mainNav5.append("<li class='magic-line5'></li>");
				var $magicLine5 = $(".magic-line5");
				if ($magicLine5.length) {
					$magicLine5
						.width($("li .cur").width())
						.css("left", $("li a").position().left)
						.data("origLeft", $magicLine5.position().left)
						.data("origWidth", $magicLine5.width());
					$(".t_wrap_2 li").find("a").click(function() {
						$el9 = $(this);
						leftPos5 = $el9.position().left;
						newWidth5 = $el9.parent().width();
						$magicLine5
							.data("origLeft", leftPos5)
							.data("origWidth", newWidth5);
						$magicLine5.stop().animate({
							left: leftPos5,
							width: newWidth5
						});
					});
				}
			});
			
		}
	});
	$(".sidebar_right").stick_in_parent({
		offset_top: 20,
	});
	/* END magic-line 5 */
	/* discount */
	$('.b-d_coupon').click(function() {
		$(this).slideUp();
		$(this).next().slideDown();
	});
	/* END discount */
	/* active checked */
	function check_agreement(){
		var ischeck = $('.check_agreement').prop('checked');
		if (ischeck) {
			$('.o-i_submit').prop('disabled', false);
			$('.o-i_submit').removeClass('o-i_submit2');
		} else {
			$('.o-i_submit').prop('disabled', true);
			$('.o-i_submit').addClass('o-i_submit2');
		}
	}
	check_agreement();
	$('.check_agreement').change(function() {
		check_agreement();
	});
	/* END active checked */
	/* accordion */
	$(".b-accordion li").find(".b-accordion_text").hide().prev().on('click', function() {
		$(this).parents(".b-accordion").find(".b-accordion_text").not(this).slideUp().prev().removeClass("active");
		$(this).next().not(":visible").slideDown().prev().addClass("active");
	});
	$(".b-accordion li:first-child").find(".b-accordion_text").not(":visible").show().prev().addClass("active");
	/* END accordion */

	/** **/
	$('.h-menu').click( function() {
		$(this).siblings(".hidden-menu").slideToggle("slow");
		return false;
	});
	$(document).click( function(event){
		if( $(event.target).closest(".hidden-menu").length ) 
		return;
		$(".hidden-menu").slideUp("slow");
		event.stopPropagation();
	});
	$(".h-menu").on("click",function(){
		var el2 = $(this);
		if(el2.hasClass("js-background")) {
                el2.removeClass("js-background");
            } else {
			el2.addClass("js-background");
        }
	});
	$(document).click( function(event){
		$(this).find(".h-menu").removeClass("js-background");
	});
	/** **/	
	$("#date").mask("99-99",{placeholder:""});
	$("#date2").mask("99-99",{placeholder:""});
	$("#phone").mask("+7 (999) 999-99-99");
	$('#phone2').mask("+7 (999) 999-99-99");
	$("#phone3").mask("+7 (999) 999-99-99");
	$("#phone4").mask("+7 (999) 999-99-99");
	$("#phone5").mask("+7 (999) 999-99-99");
	var $pane = $('.box-a_d');
	$pane.on("click","a.anchor-details", function (event) {
		event.preventDefault();
		var id  = $(this).attr('href'),
		top = $(id).offset().top - 0;
		$('body,html').animate({scrollTop: top}, 1000);
	});
	$('.js-reg-1').on( 'click', function(){
		$(this).hide();
		$('.m-p_buy').hide();
		$('.show-registration').show();
	});
        // Регистрация
        function SetSMSTimeLeft(num) {
            if(num >= 0) {
                $('.sms-hint').find('span.sms-time-left').html(num);
                --num;
                setTimeout(SetSMSTimeLeft, 1000, num);
            } else {
                $('.sms-hint').html('<span style="cursor:pointer;text-decoration:underline;" class="send-new-confirmation">Отправить код еще раз</span>');
                $('.sms-hint').find('.send-new-confirmation').click(function(){
                    $('#smscode5').val('');
                    $('.sms-hint').html('');
                    $('.js-reg').click();
                });
            }
        }
        
	$('.js-reg').on( 'click', function(){
		var phone = $('#phone5').val();
                var smscode = $('#smscode5').val();
                var pass = $('#password5').val();
                var smshint = $('.sms-hint').html();
                if(phone != '' && smscode == '' && smshint == '') {
                    $.post('/?route=ajax/index/ajaxSendConfirmationSms', {
                        telephone: phone
                    }, function(msg){
                        if(msg.status == 'success') {
                            $('.t-c_code').show();
                            $(this).show();
                            $('.sms-hint').html('Вам отправлено SMS с кодом авторизации');
                            setTimeout(function(){
                                $('.sms-hint').fadeTo(250, 0, function(){
                                    $('.sms-hint').html('Отправить SMS повторно можно через <span class="sms-time-left"></span> секунд').fadeTo(250, 1);
                                    SetSMSTimeLeft(60);
                                });
                            }, 5000);
                        }
                    }, "json");
                }
                else if(phone != '' && pass != '' && smscode != '') {
                    $.post('/?route=ajax/index/ajaxValidateRegistration', {
                        telephone: phone,
                        smscode: smscode,
                        pass: pass
                    }, function(msg){
                        if(msg.status == 'success') {
                            window.location.href = '/';
                        }
                        else {
                            console.log('ошибка авторизации')
                        }
                    }, "json");
                }
	});
        //Авторизация
        $('.m-p_entrance').click(function(){
            var phone = $('#phone3').val();
            var pass = $('#password3').val();
            if(phone != '' && pass != '') {
                $.post('/?route=ajax/index/ajaxLoginByPhone',{
                    telephone: phone,
                    password: pass
                }, function(msg){
                    if(msg.status == 'success') {
                        window.location.href = '/';
                    }
                }, "json");
            }
        });
        
	$('.m-p_forgot').on( 'click', function(){
		$('.js-hide_1').closest(".t-c_box").find(".js-hide_1").hide();
		$(".show-forgot").show();
	});
	$(".m-p_registration.js-reg-2").on( 'click', function(){
		$(".password-sent").show();	
		$(".m-p_registration.js-reg-2").show();
                if($('#phone4').val() != '' && $('#smscode4').val() == '') {
                    $.get('/?route=ajax/index/ajaxForgotPassword', {
                        telephone: $('#phone4').val()
                    }, function(msg){
                        if(msg.status == 'success') {
                            $('.password-sent').html('Вам выслана SMS с новым паролем.');
                            $('.show-forgot').find('.t-c_code').show();
                        } else {
                            $('.password-sent').html('Пользователь с таким номером не зарегистрирован.');
                        }
                    }, "json");
                } else if($('#phone4').val() != '' && $('#smscode4').val() != '') {
                    $.get('/?route=ajax/index/ajaxValidateNewPassword', {
                        telephone: $('#phone4').val(),
                        password: $('#smscode4').val()
                    }, function(msg){
                        if(msg.status == 'success') {
                            window.location.href = '/';
                        } else {
                            $('.password-sent').html('Пароль из SMS введен некорретно');
                        }
                    }, "json");
                }
	});
        $('.h-box_left').find('.hidden-menu a').each(function(i, item, arr){
            if($(item).attr('href') == window.location.origin + window.location.pathname)
            {
                $('.h-box_left').find('.h-menu').html($(this).html());
                $(this).remove();
            }
        });
        $('.show-more').click(function(e){
            $(this).parent().find('.list-letter>li.hidden').removeClass('hidden');
            $(this).css('visibility', 'hidden');
        });
        $('.p-o_submit').click(function(e){
            e.preventDefault();
            var pElement = $(this).parents('.p-o_block');
            var product_id = pElement.find('input[name="product_id"]').val();
            var label = pElement.find('.selectric .label').html();
            var quantity = parseFloat(label);
            var weight_class = label.substr(label.indexOf(' ')+1);
            console.log(product_id, quantity);
            $.post('/?route=checkout/cart/add', {
                product_id: product_id,
                quantity: quantity
            }, function(msg){
                pElement.find('.clearfix').hide();
                pElement.append('<div class="not-available clearfix"><div class="n-a_text">'+quantity+' '+weight_class+' в корзине</div><input type="submit" value="" class="p-o_submit2"></div>');
                LoadCart();
            }, "json");
        });
        // Добавление в корзину на странице товара
        $('.c-p_submit').click(function(e){
            e.preventDefault();
            var pElement = $(this).parents('.c-p_right');
            var product_id = pElement.find('input[name="product_id"]').val();
            var quantity = parseInt(pElement.find('.selectric .label').html());
            $.post('/?route=checkout/cart/add', {
                product_id: product_id,
                quantity: quantity
            }, function(msg){
                LoadCart();
            }, "json");
        });
        function LoadCart() {
            $.get('/?route=ajax/index/ajaxGetCart', {}, function(msg){
                var totalPrice = 0;
                var totalPositions = 0;
                $('.cart-container').html('');
                msg.products.forEach(function(product, i, arr){
                    var productHTML = '<div class="basket-product clearfix">'+
                        '<div class="b-p_close" data-href="'+product.link_remove+'"></div>' +
                        '<a href="#" class="b-p_link">'+product.name+'</a>'+
                        '<div class="b-p_amount">'+product.quantity+'</div>'+
                        '<div class="b-p_quantity">x '+product.price+'</div>'+
                    '</div>';
                    $('.cart-container').append(productHTML);
                    totalPrice += product.total;
                    totalPositions++;
                });
                $('.cart-price-total').html(totalPrice);
                if(totalPrice == 0) {
                    $('.b-basket, .b-basket_mobile').find('.b-b_price').html('');
                    $('.b-basket, .b-basket_mobile').find('.b-b_quantity').hide();
                } else {
                    $('.b-basket, .b-basket_mobile').find('.b-b_price').html(totalPrice + ' <span>руб</span>');
                    $('.b-basket, .b-basket_mobile').find('.b-b_quantity').show();
                }
                
                
                $('.b-basket, .b-basket_mobile').find('.b-b_quantity').html(totalPositions);
                $('.order-information').find('.o-i_price').html(totalPrice + ' <span>руб</span>');
                
                $('.cart-container').find('.b-p_close').click(function(e){
                    var removeLink = $(this).data('href');
                    $.get(removeLink, {}, function(msg){
                        if(msg.success) {
                            LoadCart();
                        }
                    }, "json");
                });
            }, "json");
        }
        LoadCart();
        
        $('.table-basket').find('.table-b_close').click(function(){
            var cart_id = $(this).data('target');
            var removeLink = '/?route=ajax/index/ajaxRemoveCartProduct&cart_id='+cart_id;
            $.get(removeLink, {}, function(msg){
                if(msg.success) {
                    LoadCart();
                    window.location.reload();
                }
            }, "json");
        });
        $('.table-basket').find('.button_decrease').click(function(){
            var targetField = $('#'+$(this).data('target'));
            var value = parseInt(targetField.val());
            if(value > 1) {
                targetField.val(value-1);
                var productTotal = (value - 1) * parseInt($(this).parents('tr').find('.table-b_price2').html());
                $(this).parents('tr').find('.table-b_price').html(productTotal + ' руб.');
                if(window.quantityTimer) clearTimeout(quantityTimer);
                window.quantityTimer = setTimeout(ChangeCartQuantity, 500, targetField.data('cart-id'), targetField.val());
            }
        });
        $('.table-basket').find('.button_increase').click(function(){
            var targetField = $('#'+$(this).data('target'));
            var value = parseInt(targetField.val());
            targetField.val(value+1);
            var productTotal = (value + 1) * parseInt($(this).parents('tr').find('.table-b_price2').html());
            $(this).parents('tr').find('.table-b_price').html(productTotal + ' руб.');
            if(window.quantityTimer) clearTimeout(quantityTimer);
            window.quantityTimer = setTimeout(ChangeCartQuantity, 500, targetField.data('cart-id'), targetField.val());
        });
        $('.table-basket').find('.table-b_input').change(function(e){
            var quantity = $(this).val();
            var cart_id = $(this).data('cart-id');
            var productTotal = quantity * parseInt($(this).parents('tr').find('.table-b_price2').html());
            $(this).parents('tr').find('.table-b_price').html(productTotal + ' руб.');
            if(window.quantityTimer) clearTimeout(quantityTimer);
            window.quantityTimer = setTimeout(ChangeCartQuantity, 500, cart_id, quantity);
        });
        function ChangeCartQuantity(cart_id, quantity) {
            $.post('/?route=ajax/index/ajaxChangeCartQuantity', {
                cart_id: cart_id,
                quantity: quantity
            }, function(msg){
                if(msg.status == 'success') {
                    LoadCart();
                }
            }, "json");
        }
        
        $('.b-dis_submit').click(function(){
            $.post('/?route=ajax/index/ajaxApplyCoupon', {
                code: $('.b-dis_input').val()
            }, function(msg){
                if(msg.status == 'success') {
                    $('.o-i_price').html(msg.total + ' <span>руб</span>');
                }
            }, "json");
        });
        
        $('.c-m_submit').click(function(){
            var address = $('#delivery_address').val();
            var comment = $('#delivery_comment').val();
            var date = $('#delivery_date').val();
            var time = $('#delivery_time').val();
            var price = parseInt($('.o-i_price').html());
            var delivery_price = 0;
            var order_id = parseInt($('.field_order_id').html());
            var payment_method = '';
            if($('#d').prop('checked')) {
                payment_method = 'Оплата при получении';
            } else {
                payment_method = 'Оплата на сайте';
            }
            if($('.block-delivery-price').css('display') == 'none') {
                // Вывод стоимости доставки
                if(address != '') {
                    $.post('/?route=ajax/index/ajaxGetDeliveryPrice', {
                        address: address
                    }, function(msg){
                        if(msg.status == 'success') {
                            if(price >= 4000 && msg.mkad == 'IN_MKAD') {
                                delivery_price = 0;
                            } else if(price < 4000 && msg.mkad == 'IN_MKAD') {
                                delivery_price = 250;
                            } else if(msg.mkad != 'IN_MKAD') {
                                delivery_price = 600;
                            }
                            $('#delivery_address').val(msg.result.data[0][0].result);
                            $('.block-delivery-price .c-d_price').html(delivery_price+' руб');
                            $('.shipping-amount .sh-a_price').html((price+delivery_price)+' руб');
                            $('.c-m_submit').html('Далее');
                            $('.block-delivery-price, .shipping-amount').show();
                        }
                    }, "json");
                }
            } else {
                // Сохраняем информацию о доставке и переходим на следующий шаг
                if(address != '' && order_id != 0) {
                    $.post('/?route=ajax/index/ajaxSetDelivery', {
                        address: address,
                        comment: comment,
                        order_id: order_id,
                        date: date,
                        time: time,
                        payment_method: payment_method,
                        telephone: $('#phone2').val()
                    }, function(msg){
                        if(msg.status == 'success') {
                            $('.liTabs_2').find('li:nth-child(2) a').css('pointer-events', 'none');
                            $('.liTabs_2').find('li:nth-child(3) a').css('pointer-events', 'auto');
                            $('.liTabs_2').find('li:nth-child(3) a').click();
                        }
                    }, "json");
                }
            }
        })
        
        $('.f-p_submit').click(function(){
            var addresses = [];
            $('input[data-name="customer_address"].f-p_input').each(function(i, item, arr){
                var address_id = $(item).attr('data-target-id');
                console.log(address_id);
                if(parseInt(address_id) <= 0) {
                    address_id = 0;
                }
                addresses[addresses.length] = {
                    value: $(item).val(),
                    address_id: address_id
                }
                
            });
            var firstname = $('input[data-name="customer_firstname"].f-p_input').val();
            var telephone = $('input[data-name="customer_telephone"].f-p_input').val();
            var email = $('input[data-name="customer_email"].f-p_input').val();
            $.post('/?route=ajax/index/ajaxSetCustomerData', {
                addresses: addresses,
                firstname: firstname,
                telephone: telephone,
                email: email
            }, function(msg){
                if(msg.status == 'success') {
                    
                } else {
                    
                }
            }, "json");
        });
        
        $('#form1.order-information').find('.o-i_submit').click(function(){
            var policy = $(this).parents('#form1').find('#myId1').prop('checked');
            var firstname = $(this).parents('#form1').find('.field_firstname').val();
            var phone = $(this).parents('#form1').find('.field_phone').val();
            var total_price = parseInt($(this).parents('#form1').find('.o-i_price').html());
            if(policy && firstname != '' && phone != '' && total_price >= 1000) {
                $(this).find('.ajax-loader').css('display', 'inline-block');
                $(this).css('font-size', '0');
                $('#form1 .field_firstname').removeClass('input-error_3');
                $('#form1 .field_phone').removeClass('input-error_3');
                $.post('/?route=ajax/index/ajaxChangeCustomerInfo', {
                    firstname: $(this).parents('#form1').find('.field_firstname').val(),
                    telephone: $(this).parents('#form1').find('.field_phone').val()
                }, function(msg){
                    $(this).find('.ajax-loader').css('display', '');
                    $(this).css('font-size', '');
                    if(msg.status == 'success') {
                        $.post('/?route=ajax/index/ajaxAddOrder', {}, 
                            function(msg) {
                                if(msg.status == 'success') {
                                    $('.field_order_id').html(msg.orderId);
                                    $('table.table-basket').html('');
                                    LoadCart();
                                    $('.liTabs_2').find('li:nth-child(1) a').css('pointer-events', 'none');
                                    $('.liTabs_2').find('li:nth-child(2) a').css('pointer-events', 'auto');
                                    $('.liTabs_2').find('li:nth-child(2) a').click();
                                }
                            }
                        );
                    }
                }, "json");
            } else {
                console.log(total_price);
                if(firstname == '') {
                    $('#form1 .field_firstname').addClass('input-error_3');
                } else {
                    $('#form1 .field_firstname').removeClass('input-error_3');
                }
                if(phone == '') {
                    $('#form1 .field_phone').addClass('input-error_3');
                } else {
                    $('#form1 .field_phone').removeClass('input-error_3');
                }
                if(total_price < 1000) {
                    $('#form1 .total-price-error').show();
                } else {
                    $('#form1 .total-price-error').hide();
                }
            }
        });
        if($('.slider-favorite-products').length > 0) {
            $.get('/?route=ajax/index/ajaxGetProductsSpecialPrice', {}, function(msg){
                $('.slider-favorite-products').html(msg);
                /*** slider-favorite-products ***/
                $('.slider-favorite-products').slick({
                        autoplay: true,
                        autoplaySpeed: 2000000,
                        slidesToShow: 5,
                        slidesToScroll: 5,
                          responsive: [
                        {
                          breakpoint: 374,
                          settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2, 
                          }
                        },
                        {
                          breakpoint: 480,
                          settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2,
                          }
                        },
                        {
                          breakpoint: 979,
                          settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2,
                          }
                        },
                        {
                          breakpoint: 1199,
                          settings: {
                                slidesToShow: 3,
                                slidesToScroll: 3,
                          }
                        },
                        {
                          breakpoint: 1280,
                          settings: {
                                slidesToShow: 4,
                                slidesToScroll: 4,
                          }
                        },
                        {
                          breakpoint: 1500,
                          settings: {
                                slidesToShow: 4,
                                slidesToScroll: 4,
                          }
                        }]
                });
                $('.f-p_all').on( 'click', function(){
                        $(this).css("display","none");
                        $(this).closest(".fond-f-p").find(".slider-favorite-products").fadeOut(100).css("display","none");
                        $(this).closest(".fond-f-p").find(".list-favorite-products").fadeIn(2000).css("display","block");
                });
                initDropDown();
                $('.slider-favorite-products').find('.p-o_submit').click(function(e){
                    e.preventDefault();
                    var pElement = $(this).parents('.p-o_block');
                    var product_id = pElement.find('input[name="product_id"]').val();
                    var quantity = parseInt(pElement.find('.selectric .label').html());
                    var special_price = true;
                    console.log(product_id, quantity);
                    $.post('/?route=checkout/cart/add', {
                        product_id: product_id,
                        quantity: quantity,
                        special_price: true
                    }, function(msg){
                        if(location.pathname == '/cart') {
                            location.reload();
                        } else {
                            LoadCart();
                        }
                    }, "json");
                });
            });
        }
        
        function InitClamp(selector) {
            $(selector).each(function(i, item, arr){
                $clamp(item, {clamp: 2});
            });
        }
        InitClamp('.p-o_link a, .p-o_short-descr');
        
        window.searchTimer = false;
        $('.b-seach input[type="text"]').keyup(function(){
            clearTimeout(window.searchTimer);
            window.searchTimer = setTimeout(function(){
                var search = $('.b-seach input[type="text"]').val();
                if(search.length >= 2) {
                    $.get('/?route=ajax/index/ajaxSearchProducts', {
                        search: search
                    }, function(msg){
                        $('#contentcontainer3 div.container').html(msg);
                        InitClamp('.p-o_link a, .p-o_short-descr');
                        $('.tabs__catalog').find('li:nth-child(4)').click();
                    });
                }
            }, 500);
        });
        $('form.b-seach').submit(function(e){
            e.preventDefault();
        })
        if(location.pathname == '/my-account') {
            $('.b-profile').html('Выйти');
                $('.b-profile').click(function(e){
                        e.preventDefault();
                        $.get('/?route=ajax/index/ajaxLogout', {}, function(msg){
                           if(msg.status == 'success') {
                               location.reload();
                           } 
                        }, 'json');
                });
        }
});