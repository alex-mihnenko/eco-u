$(document).ready(function(){
    
	/*** start basket ***/
	$(document).on('opening', '.modal-basket', function () {
		$('.scroll-pane').jScrollPane();
	    var initDropDown2 = function() {
	        var $items = $('.modal-basket .tech:not(.dd-ready)');
	        if(!$items.length) {
	            return;
	        }
	        var i = 0;
	        $items.each(function() {
	            if(i == 10) {
	                return false;
	            }
	            var $item = $(this);
                    i++;
                    if($item.hasClass('dd-ready')) return true;
	            $item.addClass('dd-ready');
	            $item.selectric();
	        });
	        setTimeout(function() {
	                initDropDown2();
	        }, 10);
	    }
	    initDropDown2();
	});
	$(document).on('opened', '.modal-basket', function () {
		$('.scroll-pane').jScrollPane();
			/* select */
	    var initDropDown3 = function() {
	        var $items = $('.modal-basket .tech:not(.dd-ready)');
	        if(!$items.length) {
	            return;
	        }
	        var i = 0;
	        $items.each(function() {
	            if(i == 10) {
	                return false;
	            }
	            var $item = $(this);
                    i++;
                    if($item.hasClass('dd-ready')) return true;
	            $item.addClass('dd-ready');
	            $item.selectric();
	        });
	        setTimeout(function() {
	                initDropDown3();
	        }, 10);
	    }
	    initDropDown3();
	});
	/*** end basket **/
    $('#b-close_advantage').click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var $parent = $this.parent();
        $parent.css({ minHeight: 0 });
        $parent.slideUp('slow');
        
        var d = new Date();
        var exdays = 30;
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = "hide_advantage=1;" + expires + ";path=/";
    
        return false;
    });
        
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
                    var label = pElement.find('.selectric .label').html();
                    var quantity = parseFloat(label);
                    var weight_class = label.substr(label.indexOf(' ')+1);
                    var weight_variant = 0;
                    pElement.find('.selectric-hide-select option').each(function(i, item) {
                        if($(item).html() == label) {
                            weight_variant = $(item).val();
                        }
                    });
                    console.log(product_id, quantity, weight_variant);
                    $.post('/?route=checkout/cart/add', {
                        product_id: product_id,
                        quantity: quantity,
                        weight_variant: weight_variant
                    }, function(msg){
                    	var bSubmit = pElement.find('.m-product_submit');
                        if(bSubmit.hasClass("m-product_submit2")) {
                        } else {
                            bSubmit.addClass('m-product_submit2');
                            bSubmit.text("Добавлено в корзину");
                            setTimeout(function () {
                                bSubmit.removeClass('m-product_submit2');
                                bSubmit.text("Добавить в корзину");
                            }, 2000, bSubmit); // 1000 м.сек
                        }
                        LoadCart();
                    }, "json");
                });
            }
        });
    });
    
    $('.f-p_address_remove').click(function(){
        var tElement = $(this).data('target');
        $.get('/?route=ajax/index/ajaxRemoveAddress', {address_id: tElement}, function(msg){
            if(msg.status == 'success') {
                $('.f-p_address_container[data-index="'+tElement+'"]').remove();
                $('.f-p_address_remove').eq($('.f-p_address_remove').length-1).addClass('last');
            }
        }, 'json');
    });
    
    $(".sticker").sticky({
		topSpacing: 0
	});
	$(".f-c_top").sticky({
		topSpacing: 67,
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
		var $items = $('.tech:not(.dd-ready):visible');
		if(!$items.length) {
			return;
		}
		var i = 0;
		$items.each(function() {
                        i++;
                        if(i == 10) {
                            return false;
                        }
			var $item = $(this);
                        if($item.parents('.modal-basket').length) return true;
			if($item.hasClass('dd-ready')) return true;
			$item.addClass('dd-ready');
			$item.selectric();
                        console.log($item.parents('div[itemprop="itemListElement"]').attr('id'));
                        var currencyStr = ' руб';
                        $item.parents('.p-o_select').find('select').on('change', function(e){
//                            console.log($(this));
//                            console.log($(this).parents('.p-o_block'));
//                            console.log($(this).parents('.p-o_block').find('meta[itemprop="price"]'));
                            if(!$(this).parents('.p-o_block').find('meta[itemprop="price"]').length) return;
                            var quantity = parseFloat($(this).parents('.p-o_block').find('.selectric .label').html());
                            var price = parseFloat($(this).parents('.p-o_block').find('meta[itemprop="price"]').attr('content'));
                            var compPrice = $(this).parents('.p-o_block').find('.composite_price').val();
                            var mtpl = 1;
                            if(typeof(compPrice) != 'undefined') {
                                var cpFormat = JSON.parse(compPrice);
                                if(cpFormat[quantity]) {
                                    mtpl = cpFormat[quantity];
                                }
                            }
                            var totalPrice = Math.floor(mtpl * quantity * price);
                            if(totalPrice > 999) currencyStr = ' р';
                            $(this).parents('.p-o_block').find('.p-o_price').html(totalPrice + currencyStr);
                        });
                        $item.parents('.p-o_select').find('select').trigger('change');;
                        
                        $item.parents('.m-product_select').find('select').on('change', function(e){
                            var quantity = parseFloat($(this).parents('.size-0').find('.selectric .label').html());
                            var price = parseFloat($(this).parents('.size-0').find('input.product_price').val());
                            var compPrice = $(this).parents('.size-0').find('.composite_price').val();
                            var mtpl = 1;
                            if(typeof(compPrice) != 'undefined') {
                                var cpFormat = JSON.parse(compPrice);
                                if(cpFormat[quantity]) {
                                    mtpl = cpFormat[quantity];
                                }
                            }
                            var totalPrice = Math.floor(mtpl * quantity * price);
                            if(totalPrice > 999) currencyStr = ' р';
                            $(this).parents('.size-0').find('.m-product_price_shadow').html(totalPrice + currencyStr);
                        });
                        $item.parents('.m-product_select').find('select').trigger('change');;
                        
                        $item.parents('.c-p_select').find('select').on('change', function(e){
                            var quantity = parseFloat($(this).parents('.c-p_right').find('.selectric .label').html());
                            var price = parseFloat($(this).parents('.c-p_right').find('meta[itemprop="price"]').attr('content'));
                            var compPrice = $(this).parents('.c-p_right').find('.composite_price').val();
                            
                            var mtpl = 1;
                            if(typeof(compPrice) != 'undefined') {
                                var cpFormat = JSON.parse(compPrice);
                                if(cpFormat[quantity]) {
                                    mtpl = cpFormat[quantity];
                                }
                            }
                            var totalPrice = Math.floor(mtpl * quantity * price);
                            $(this).parents('.c-p_right').find('.c-p_price_shadow').html(totalPrice + currencyStr);
                        });
                        $item.parents('.c-p_select').find('select').trigger('change');;
		});
		setTimeout(function() {
			initDropDown();
		}, 10);
	};
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

//		$('.list-products').ddscrollSpy({
//			highlightclass: "selected2",
//			scrolltopoffset: -160,
//		});
//		$('.list-alphabetic').ddscrollSpy({
//			highlightclass: "selected",
//			scrolltopoffset: -160,
//		}); 
//		$('.list-tabs2').ddscrollSpy({
//			highlightclass: "selected3",
//			scrolltopoffset: -160,
//		});
	});
	/*** ***/
	$('.tabs__catalog li.modal8').on( 'click', function(){
		
		if(!$(this).hasClass('active')) {
			setTimeout(function() {
                                var $element = $('.list-alphabetic li a.selected').length ? $('.list-alphabetic li a.selected') : $('.list-alphabetic li:first-child a');
				$element.trigger('click');
                                bLazyPluginInit();
//				$('.list-alphabetic li:first-child a').trigger('click');
			}, 100);
			
		} else {
			e.preventDefault();
			return false;
		}
	});
	$('.tabs__catalog li.modal9:not(.active)').on( 'click', function(e){

		if(!$(this).hasClass('active')) {
			setTimeout(function() {
                                var $element = $('.list-products li a.selected2').length ? $('.list-products li a.selected2') : $('.list-products li:first-child a');
				$element.trigger('click');
                                bLazyPluginInit();
//			$('.list-products li:first-child a').trigger('click');
			}, 100);
			
		} else {
			e.preventDefault();
			return false;
		}
	});
	
	
	
	
	$('.tabs__catalog li.modal-hide').on( 'click', function(){
		var distanceTop = $('.fond-catalog').offset().top;
		setTimeout(function() {
			$(window).scrollTop(distanceTop);
		}, 50, distanceTop);
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
    $('.c-p_submit').on( 'click', function(){
        var sTxt = $(this).html();
        var el = $(this);
        if(el.hasClass("c-p_submit_submit2")) {
        } else {
            el.addClass('c-p_submit_submit2');
            el.text("Добавлено в корзину");
            setTimeout(function () {
                el.removeClass('c-p_submit_submit2');
                el.text(sTxt);
            }, 2000, sTxt); // 1000 м.сек
        }
    });
    
    var slIcons = document.getElementsByClassName('category-icon-active');
    for(i=0;i<slIcons.length;i++) {
        slIcons[i].onload = function(){
            for(j=0;typeof(this.contentDocument.getElementsByTagName('path')[j]) != 'undefined';j++) {
                this.contentDocument.getElementsByTagName('path')[j].setAttribute('fill', '#ffffff');
            }
            for(j=0;typeof(this.contentDocument.getElementsByTagName('polygon')[j]) != 'undefined';j++) {
                this.contentDocument.getElementsByTagName('polygon')[j].setAttribute('fill', '#ffffff');
            }
        }
    }

	/*  */
	/** dynamic input **/
	$('.f-p_plus').click(function() {
                $('.f-p_address_remove').removeClass('last');
		$('<input type="text" data-name="customer_address" data-target-id="0" class="f-p_input" name="dynamic[]" placeholder="Адрес Доставки" />').fadeIn('slow').appendTo('.f-p_box2');
                InitSuggestions();
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
				if ($magicLine5.length && $magicLine5.is(':visible')) {
					$magicLine5
						.width($("li .cur").width())
						.css("left", $("li a.cur").position().left)
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
	$('.modal-basket').delegate('.check_agreement', 'change', function() {
		check_agreement();
	});
        
        $('.modal-basket').delegate('select#delivery_address_m', 'change', function(){
            if($(this).val() == 0) {
                $(this).parents('.selectric-wrapper').remove();
                $('.delivery-address-m').html('<input type="text" class="ca-i_input ta-center" id="delivery_address_m" value="" placeholder="Новый адрес доставки">');
                $('input#delivery_address_m[type="text"]').not('.suggestions-input').suggestions({
                    token: "a4ad0e938bf22c2ffbf205a4935ef651fc92ed52",
                    type: "ADDRESS",
                    count: 5,
                    /* Вызывается, когда пользователь выбирает одну из подсказок */
                    onSelect: function(suggestion) {
                        $('.block-delivery-price').hide();
                        $('.shipping-amount').hide();
                        $('.c-m_submit').html('Рассчитать стоимость доставки');
                        console.log(suggestion);
                    }
                });
                $('input#delivery_address_m').keydown(function(){
                    $('.block-delivery-price').hide();
                    $('.shipping-amount').hide();
                    $('.c-m_submit').html('Рассчитать стоимость доставки');
                });
            } else {
                $('.block-delivery-price').hide();
                $('.shipping-amount').hide();
                $('.c-m_submit').html('Рассчитать стоимость доставки');
            }
        });
        $('.modal-basket').delegate('.c-m_submit2', 'click', function(){
            var $this = $(this);
            var gtStep =$this.data('go-to');
            $('.t_item_p' + gtStep + ' a').click();
        });
        $('.modal-basket').delegate('.c-m_submit', 'click', function(){
            var hasError = false;
            var address = '';
            var address_new = 'false';
            if($('select#delivery_address_m').length > 0) {
                var addrIndex = $('select#delivery_address_m').val();
                address = $('select#delivery_address_m').find('option[value="'+addrIndex+'"]').html();
            } else if($('input#delivery_address_m').length > 0) {
                address = $('input#delivery_address_m').val();
                address_new = 'true';
            }
            var phone = $('#phone2_m').val().trim();
            $next = $('#phone2_m').next();
            if(!phone) {
                if(!$next.hasClass('customer-exists-error')) {
                    $('#phone2_m').after('<div class="customer-exists-error">Введите номер телефона</div>');
                }
                hasError = true;
            } else {
                if($next.hasClass('customer-exists-error')) {
                    $next.slideUp('fast', function() {
                        $(this).remove();
                    });
                }
            }
            $next = $('.delivery-address-m').next();
            if(!address.trim()) {
                if(!$next.hasClass('customer-exists-error')) {
                    $('.delivery-address-m').after('<div class="customer-exists-error">Введите адрес доставки</div>');
                }
                hasError = true;
            } else {
                if($next.hasClass('customer-exists-error')) {
                    $next.slideUp('fast', function() {
                        $(this).remove();
                    });
                }
            }
            $next = $('#customer-name').next();
            if(!$('#customer-name').val().trim()) {
                if(!$next.hasClass('customer-exists-error')) {
                    $('#customer-name').after('<div class="customer-exists-error">Введите имя</div>');
                }
                hasError = true;
            } else {
                if($next.hasClass('customer-exists-error')) {
                    $next.slideUp('fast', function() {
                        $(this).remove();
                    });
                }
            }
            
            if(hasError) {
                $('.modal-basket .customer-exists-error').slideDown('fast');
                return;
            }
            var price = parseInt($('#form-customer .o-i_price').html());
            var delivery_price = 0;
            if(!$('.modal-basket .block-delivery-price').is(':visible')) {
                // Вывод стоимости доставки
                if(address != '') {
                    $.post('/?route=ajax/index/ajaxGetDeliveryPrice', {
                        address: address,
                        address_new: address_new,
                        telephone: $('#phone2_m').val(),
                        firstname: $('#customer-name').val(),
                        order_id: $('#checkout-order-id').val()
                    }, function(msg){
                        if(msg.status == 'success') {
                            if((msg.order_id !== undefined) && (msg.order_id > 0)) {
                                $('#checkout-order-id').val(msg.order_id);
                            }
                            if(price >= 4000 && msg.mkad == 'IN_MKAD') {
                                delivery_price = 0;
                            } else if(price < 4000 && msg.mkad == 'IN_MKAD') {
                                delivery_price = 290;
                            } else if(msg.mkad != 'IN_MKAD') {
                                delivery_price = 490;
                            }
                            $('input#delivery_address_m').val(msg.result.data[0][0].result);
                            $('.block-delivery-price .c-d_price').html(delivery_price+' руб');
                            $('.shipping-amount .sh-a_price').html((price+delivery_price)+' руб');
                            $('.c-m_submit').html('Далее');
                            $('.block-delivery-price, .shipping-amount').show();
                        }
                    }, "json");
                }
            } else {
                // Сохраняем информацию о доставке и переходим на следующий шаг
                if(address != '') {
                    $('.t_item_p3 a').click();
                }
            }
        });
        $('.modal-basket').delegate('.payment-method', 'click', function(e) {
            e.preventDefault();
            
            var $this = $(this);
            $('#cart-loading').show();
            
            var address = '';
            var addressNew = 0;
            if($('select#delivery_address_m').length > 0) {
                var addrIndex = $('select#delivery_address_m').val();
                address = $('select#delivery_address_m').find('option[value="'+addrIndex+'"]').html();
            } else if($('input#delivery_address_m').length > 0) {
                address = $('input#delivery_address_m').val();
                addressNew = 1;
            }

            
            var data = {
                payment_method_code: $this.data('payment-method-code'),
                payment_method_title: $this.find('.pm-title').html(),
                customer: $('#customer-name').val(),
                telephone: $('#phone2_m').val(),
                comment: $('#delivery_comment_m').val(),
                date: $('#delivery_date_m').val(),
                time: $('#delivery_time_m').val(),
                address: address,
                address_new: addressNew,
                order_id: $('#checkout-order-id').val()
            };
            $.ajax({
                url: '/?route=ajax/index/ajaxConfirmOrder',
                type: 'post',
                data: data,
                dataType: 'json',
                success: function (data, textStatus, jqXHR) {
                    if(data.redirect !== undefined) {
                        document.location = data.redirect;
                    } else if(data.status == 'success') {
                        $('.t_item_p4 a').click();
                        $('#cart-order-id').html(data.order_id);
                    } else if(data.error !== undefined) {
                        alert(data.error);
                    } else {
                        alert('Ошибка оформления заказа. Повторите попытку');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Ошибка оформления заказа. Повторите попытку');
                },
                complete: function (jqXHR, textStatus) {
                    $('#cart-loading').hide();
                }
            });
            
            return false;
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
    $.mask.definitions['h'] = "[0-6,9]";
	$("#date").mask("99-99",{placeholder:""});
	$("#date2").mask("99-99",{placeholder:""});
	$("#phone").mask("+7 (h99) 999-99-99");
	$('#phone2').mask("+7 (h99) 999-99-99");
	$("#phone3").mask("+7 (h99) 999-99-99");
	$("#phone4").mask("+7 (h99) 999-99-99");
	$("#phone5").mask("+7 (h99) 999-99-99");
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
        var afterLogin = false;
        var reopenCart = function() {
            var inst = $('[data-remodal-id="modal-basket"]').remodal();
            inst.open();
            afterLogin = false;
            $(document).unbind('closed', reopenCart);
        };
        $('.modal-basket').delegate('#btn-cart-auth', 'click', function(e) {
            e.preventDefault();
            var inst = $('[data-remodal-id=modal]').remodal();
            inst.open();
            
            afterLogin = function() {
                document.location.hash = "modal-basket";
                document.location.reload();
                return true;
            };
            
            $(document).bind('closed', '.modal-profile', reopenCart);

            return false;
        });
        //Авторизация
        $('.m-p_entrance').click(function(e){
            e.preventDefault();
            var phone = $('#phone3').val();
            var pass = $('#password3').val();
            if(phone != '' && pass != '') {
                $('.js-hide_1').submit();
                $.post('/?route=ajax/index/ajaxLoginByPhone',{
                    telephone: phone,
                    password: pass
                }, function(msg){
                    if(msg.status == 'success') {
                        if(afterLogin && afterLogin()) {
                            return;
                        }
                        window.location.href = window.location.pathname;
                    } else {
                        $('.m-p_entrance').parent().find('.t-c_input').addClass('input-error_1').find('input').css('border-bottom', '3px solid #D00');
                        var $errEl = $('.m-p_entrance').parent().find('.login-wrong');
                        if(msg.locked) {
                            $errEl.text(msg.locked);
                        } else {
                            $errEl.text($errEl.data('wrong-text'));
                        }
                        $errEl.show();
                    }
                }, "json");
            }
        });
        
        $('.js-hide_1').find('input').keydown(function(){
                $('.login-wrong').hide();
        });
        
	$('.m-p_forgot').on( 'click', function(){
		$('.js-hide_1').closest(".t-c_box").find(".js-hide_1").hide();
                $('#smscode4').val('')
		$(".show-forgot").show();
                $(".m-p_registration.js-reg-2").text('Напомнить пароль');
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
                            $(".m-p_registration.js-reg-2").text('Войти');
                        } else {
                            $('.t-c_input').addClass('input-error_1');
                            $('.password-sent').html('Пользователь с таким номером не зарегистрирован.');
                            $('#phone4').focus();
                        }
                    }, "json");
                } else if($('#phone4').val() != '' && $('#smscode4').val() != '') {
                    $.get('/?route=ajax/index/ajaxValidateNewPassword', {
                        telephone: $('#phone4').val(),
                        password: $('#smscode4').val()
                    }, function(msg){
                        if(msg.status == 'success') {
                            window.location.href = window.location.pathname;
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
            //$(this).css('visibility', 'hidden');
            var $button = $(this);
            var dataMode = $(this).data('mode');
            var dataTarget = $(this).data('target');
            var $btns = $('.show-more[data-mode="' + dataMode + '"][data-target="' + dataTarget + '"]');
            
            var pElement = $(this).parent().find('.list-letter');
            if(pElement.hasClass('ll-opened')) {
                pElement.removeClass('ll-opened');
                $btns.each(function() {
                    var $btn = $(this);
                    $btn.html($btn.data('html'));
                });
                if(dataMode == 'asort') {
                    $('.list-alphabetic a.selected').trigger('click');
                } else if(dataMode == 'catsort') {
                    $('.list-products a.selected2').trigger('click');
                }
                return;
            } else {
                pElement.addClass('ll-opened');
                $btns.each(function() {
                    var $btn = $(this);
                    $btn.data('html', $btn.html());
                });
                $btns.html('скрыть');
            }
            if(pElement.hasClass('all-loaded')) {
                return;
            }
            var nInclude = [];
            pElement.find('li[data-product]').each(function(i, item, arr){
                nInclude[nInclude.length] = $(item).data('product');
            });
            $.post('/?route=ajax/index/ajaxShowMore', {
                mode: $(this).data('mode'),
                target: $(this).data('target'),
                not_include: nInclude
            }, function(products){
                if(!pElement.hasClass('all-loaded')) {
                    pElement.addClass('all-loaded');
                    pElement.append(products);
                    initDropDown();
                    BindAddToCartEvents(pElement.find('li[data-type="dynamic"]'));
                    InitClamp('.p-o_link a, .p-o_short-descr');
                }
                bLazyPluginInit();
            });
        });
        function BindAddToCartEvents(block) {
            block.find('.p-o_submit').click(function(e){
                e.preventDefault();
                var pElement = $(this).parents('.p-o_block');
                var product_id = pElement.find('input[name="product_id"]').val();
                var label = pElement.find('.selectric .label').html();
                var quantity = parseFloat(label);
                var weight_class = label.substr(label.indexOf(' ')+1);
                var weight_variant = 0;
                var special_price = false;
                if(location.pathname == '/cart') {
                    special_price = true;
                }
                pElement.find('.selectric-hide-select option').each(function(i, item) {
                    if($(item).html() == label) {
                        weight_variant = $(item).val();
                    }
                });
                $.post('/?route=checkout/cart/add', {
                    product_id: product_id,
                    quantity: quantity,
                    weight_variant: weight_variant,
                    special_price: special_price
                }, function(msg){
                    pElement.find('.p-o_select, .p-o_right').hide();
                    pElement.find('.clearfix').append('<div class="not-available clearfix basket-added"><div class="n-a_text">'+quantity+' '+weight_class+' в корзине</div><input type="submit" value="" class="p-o_submit2"></div>');
                    LoadCart();
                    setTimeout(function(){
                        pElement.find('.basket-added').remove();
                        pElement.find('.p-o_select, .p-o_right').show();
                    }, 3000, pElement);
                    if(location.pathname == '/cart') location.href = '/cart';
                }, "json");
            });
        }
        BindAddToCartEvents($('body'));
        // Добавление в корзину на странице товара
        $('.c-p_submit').click(function(e){
            e.preventDefault();
            if($(this).hasClass('sold')) return false;
                    var pElement = $(this).parents('.c-p_right');
                    var product_id = pElement.find('input[name="product_id"]').val();
                    var quantity = parseFloat(pElement.find('.selectric .label').html());
                    var label = pElement.find('.selectric .label').html();
                    var special_price = true;
                    var weight_variant = 0;
                    pElement.find('.selectric-hide-select option').each(function(i, item) {
                        if($(item).html() == label) {
                            weight_variant = $(item).val();
                        }
                    });
                    $.post('/?route=checkout/cart/add', {
                        product_id: product_id,
                        quantity: quantity,
                        weight_variant:weight_variant
                    }, function(msg){
                        if(location.pathname == '/cart') {
                            location.reload();
                        } else {
                            LoadCart();
                        }
                    }, "json");
        });
        function LoadCart() {
            $.get('/?route=ajax/index/ajaxGetCart', {}, function(msg){
                
                
                var totalPrice = msg.total;
                var totalPositions = msg.count;
                
                var $html = $(msg.html);
                $('.modal-basket').removeClass('modal-basket-380');
                $('.modal-basket .remodal-close').show();
                $('.modal-basket').html($html.html());
                $('.cart-price-total').html(totalPrice);
                if(totalPrice == 0) {
                    $('.b-basket, .b-basket_mobile').find('.b-b_price').html('');
                    $('.b-basket, .b-basket_mobile').find('.b-b_quantity').hide();
                } else {
                    $('.b-basket, .b-basket_mobile').find('.b-b_price').html(totalPrice + ' <span>руб</span>');
                    $('.b-basket, .b-basket_mobile').find('.b-b_quantity').show();
                }
                
                
                $('.b-basket, .b-basket_mobile').find('.b-b_quantity').html(totalPositions);
                $.get('/?route=ajax/index/ajaxGetOrderPrice', {}, function(msg){
                    if(msg.status == 'success') {
                        $('#form-customer').find('.o-i_price').html(msg.price + ' <span>руб</span>');
                        //$('.b-discount .c-d_amount').html(msg.discount);
                    } else {
                        console.log('Ошибка расчета цены');
                    }
                }, "json");
                
                $('.liTabs_cart').liTabs({
                    duration: 500,		//A string or number determining how long the animation will run
                    effect:'hSlide' //clasic, fade, hSlide, vSlide
                });

                if($('.remodal.modal-basket.remodal-is-opened').length) {
                    $('.modal-basket').trigger('opening');
                    $('.modal-basket').trigger('opened');
                }
                
                $('.modal-basket .m-checkout-step-2').click(function(e) {
                    e.preventDefault();
                    $('.t_item_p2 a').click();
                    $('.modal-basket .remodal-close').hide();
                    setTimeout(function() {
                        $('.modal-basket').addClass('modal-basket-380');
                    }, 500);
                    return false;
                });
                
                var $items = $('.modal-basket .tech:not(.dd-ready)');
	        if($items.length) {
                    $items.each(function() {
                        var $item = $(this);
                        if($item.hasClass('dd-ready')) return true;
                        $item.addClass('dd-ready');
                        $item.selectric();
                    });
	        }
                $('#phone2_m').mask("+7 (h99) 999-99-99");
                $('input#delivery_address_m').keydown(function(){
                    $('.block-delivery-price').hide();
                    $('.shipping-amount').hide();
                    $('.c-m_submit').html('Рассчитать стоимость доставки');
                });
                $('input#delivery_address_m[type="text"]').not('.suggestions-input').suggestions({
                    token: "a4ad0e938bf22c2ffbf205a4935ef651fc92ed52",
                    type: "ADDRESS",
                    count: 5,
                    /* Вызывается, когда пользователь выбирает одну из подсказок */
                    onSelect: function(suggestion) {
                        $('.block-delivery-price').hide();
                        $('.shipping-amount').hide();
                        $('.c-m_submit').html('Рассчитать стоимость доставки');
                        console.log(suggestion);
                    }
                });
//                
//                $('.cart-container').find('.b-p_close').click(function(e){
//                    var removeLink = $(this).data('href');
//                    $.get(removeLink, {}, function(msg){
//                        if(msg.success) {
//                            LoadCart();
//                        }
//                    }, "json");
//                });
            }, "json");
        }
        LoadCart();
        
        // Remove From Cart
        $('.modal-basket').delegate('.list-modal_close', 'click', function() {
            var removeLink = $(this).data('href');
            $.get(removeLink, {}, function(msg){
                if(msg.success) {
                    LoadCart();
                }
            }, "json");
        });

        // Change Quantity
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
        $('.modal-basket').delegate('select.change-m-cart-quantity', 'change', function() {
            var $this = $(this);
            var cartId = $this.data('cart-id');
            var variant = parseFloat($this.data('cart-variant'));
            var quantity = parseInt($this.val());
            var prevQuantity = parseInt($this.data('cart-quantity'));
            if(prevQuantity != quantity) {
                ChangeCartQuantity(cartId, quantity * variant);
            }
        });
        
        // Apply Coupon
        $('#m-basket-coupon').bind('ecomodal.onhide', function() {
            $('#m-basket-coupon input').val('');
        });
        $('#m-basket-coupon a').click(function() {
            $.post('/?route=ajax/index/ajaxApplyCoupon', {
                code: $('#m-basket-coupon input').val()
            }, function(msg){
                if(msg.status == 'success') {
                    LoadCart();
                    $('#m-basket-coupon').trigger('ecomodal.hide');
                }
            }, "json");
        });        
        
        /*
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
            var variantField = $('#'+$(this).data('variant'));
            var value = parseInt(targetField.val());
            var variant = parseFloat(variantField.html());
            if(value > 1) {
                targetField.val(value-1);
                var productTotal = Math.floor((value-1) * variant * parseInt($(this).parents('tr').find('.table-b_price2').html()));
                $(this).parents('tr').find('.table-b_price').html(productTotal + ' руб.');
                if(window.quantityTimer) clearTimeout(quantityTimer);
                window.quantityTimer = setTimeout(ChangeCartQuantity, 500, targetField.data('cart-id'), targetField.val() * variant);
            }
        });
        $('.table-basket').find('.button_increase').click(function(){
            var targetField = $('#'+$(this).data('target'));
            var variantField = $('#'+$(this).data('variant'));
            var value = parseInt(targetField.val());
            var variant = parseFloat(variantField.html());
            targetField.val(value+1);
            var productTotal = Math.floor((value+1) * variant * parseInt($(this).parents('tr').find('.table-b_price2').html()));
            $(this).parents('tr').find('.table-b_price').html(productTotal + ' руб.');
            if(window.quantityTimer) clearTimeout(quantityTimer);
            window.quantityTimer = setTimeout(ChangeCartQuantity, 500, targetField.data('cart-id'), targetField.val() * variant);
        });
        $('.table-basket').find('.table-b_input').change(function(e){
            var quantity = $(this).val();
            var variantField = $('#'+$(this).data('variant'));
            var variant = parseFloat(variantField.html());
            var cart_id = $(this).data('cart-id');
            var productTotal = Math.floor(quantity * variant * parseInt($(this).parents('tr').find('.table-b_price2').html()));
            $(this).parents('tr').find('.table-b_price').html(productTotal + ' руб.');
            if(window.quantityTimer) clearTimeout(quantityTimer);
            window.quantityTimer = setTimeout(ChangeCartQuantity, 500, cart_id, quantity * variant);
        });
        
        $('.b-dis_submit').click(function(){
            $.post('/?route=ajax/index/ajaxApplyCoupon', {
                code: $('.b-dis_input').val()
            }, function(msg){
                if(msg.status == 'success') {
                    $('.order-information').find('.o-i_price').html(msg.total + ' <span>руб</span>');
                    $('.b-discount .c-d_amount').html(msg.discountValue);
                    
                    var $html = $(msg.html);
                    var $parent = $('.b-discount');
                    $parent.find('.personal-coupon').html($html.find('.personal-coupon').html());
                    
                    $parent.find('.personal-discount').remove();
                    if($html.find('.personal-discount').length) {
                        $parent.find('.personal-coupon').before($html.find('.personal-discount').clone());
                    }
                    var $el = $html.find('.b-d_coupon_discount').length ? $html.find('.b-d_coupon_discount') : $html.find('.b-d_discount');
                    $parent.find('.b-d_coupon_discount,.b-d_coupon').html($el.html());
                    $parent.find('.b-d_coupon_discount,.b-d_coupon').attr('class', $el.attr('class'));
                    
                    if($('#customer_coupon').length > 0) {
                        if($('#customer_discount').length == 0 || (parseInt($('#customer_coupon').val()) > parseInt($('#customer_discount').val()))) {
                            $('.personal-coupon').slideDown();
                        } else {
                            $('.personal-discount').slideDown();
                        }
                    } else {
                        $('.personal-discount').slideDown();
                    }
        
                    $('.b-d_coupon_discount,.b-d_coupon').slideDown();
                    
                    $('.b-d_hidden').slideUp(function() {
                        $('.b-d_hidden input.b-dis_input').val('');
                    });

                }
            }, "json");
        });
        
        if($('#customer_coupon').length > 0) {
            if($('#customer_discount').length == 0 || (parseInt($('#customer_coupon').val()) > parseInt($('#customer_discount').val()))) {
                $('.personal-discount').hide();
                $('.personal-coupon').show();
            }
        }
        
        $('.c-m_submit').click(function(){
            var address = '';
            if($('select#delivery_address').length > 0) {
                var address_new = false;
                var addrIndex = $('select#delivery_address').val();
                address = $('select#delivery_address').find('option[value="'+addrIndex+'"]').html();
            } else if($('input#delivery_address').length > 0) {
                var address_new = true;
                address = $('input#delivery_address').val();
            }
            var comment = $('#delivery_comment').val();
            var date = $('#delivery_date').val();
            var time = $('#delivery_time').val();
            var price = parseInt($('.o-i_price').html());
            var delivery_price = 0;
            var order_id = parseInt($('.field_order_id').html());
            var payment_method = '';
            var payment_code = '';
            var payment_method_online = 0;
            if($('#d').prop('checked')) {
                payment_method = 'Оплата при получении';
                payment_code = '';
            } else {
                payment_method = 'Оплата на сайте';
                payment_code = 'bank-card';
                payment_method_online = 1;
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
                            $('input#delivery_address').val(msg.result.data[0][0].result);
                            $('.block-delivery-price .c-d_price').html(delivery_price+' руб');
                            $('.shipping-amount .sh-a_price').html((price+delivery_price)+' руб');
                            $('.can-toggle.demo-rebrand-1').show();
                            $('.c-m_submit').html('Далее');
                            $('.c-m_submit').prepend('<div class="ajax-loader"></div>');
                            $('.can-toggle.demo-rebrand-1, .payment-title').show();
                            $('.block-delivery-price, .shipping-amount').show();
                        }
                    }, "json");
                }
            } else {
                // Сохраняем информацию о доставке и переходим на следующий шаг
                if(address != '' && order_id != 0) {
                    var btn = $(this);
                    btn.find('.ajax-loader').show();
                    btn.css('font-size', '0');
                    console.log(address);
                    $.post('/?route=ajax/index/ajaxSetDelivery', {
                        address: address,
                        address_new: address_new,
                        comment: comment,
                        order_id: order_id,
                        date: date,
                        time: time,
                        payment_method: payment_method,
                        payment_method_online: payment_method_online,
                        payment_code: payment_code,
                        telephone: $('#phone2').val()
                    }, function(msg){
                        btn.find('.ajax-loader').hide();
                        btn.css('font-size', '');
                        if(msg.redirect !== undefined) {
                            document.location = msg.redirect;
                        } else if(msg.status == 'success') {
                            $('.liTabs_2').find('li:nth-child(2) a').css('pointer-events', 'none');
                            $('.liTabs_2').find('li:nth-child(3) a').css('pointer-events', 'auto');
                            $('.liTabs_2').find('li:nth-child(3) a').click();
                        } else if(msg.error !== undefined) {
                            alert(msg.error);
                        }
                    }, "json");
                }
            }
        })
        
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
                    $('#form1.order-information').find('.o-i_submit').find('.ajax-loader').css('display', '');
                    $('#form1.order-information').find('.o-i_submit').css('font-size', '');
                    if(msg.status == 'success') {
                        $.post('/?route=ajax/index/ajaxAddOrder', {}, 
                            function(msg) {
                                if(msg.status == 'success') {
                                    $('.field_order_id').html(msg.orderId);
                                    $('table.table-basket').html('');
                                    //LoadCart();
                                    $('.all-b_basket').hide();
                                    $('.can-toggle.demo-rebrand-1, .payment-title').hide();
                                    $('.liTabs_2').find('li:nth-child(1) a').css('pointer-events', 'none');
                                    $('.liTabs_2').find('li:nth-child(2) a').css('pointer-events', 'auto');
                                    $('.liTabs_2').find('li:nth-child(2) a').click();
                                }
                            }
                        );
                    } else {
                        $.post('/?route=ajax/index/ajaxAddNoAuthOrder', {
                            telephone: $('#phone2').val()
                        }, 
                            function(msg) {
                                if(msg.status == 'success') {
                                    $('.field_order_id').html(msg.orderId);
                                    $('table.table-basket').html('');
                                    //LoadCart();
                                    $('.all-b_basket').hide();
                                    $('.can-toggle.demo-rebrand-1, .payment-title').hide();
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
    */
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
            var email_hidden = $('input[data-name="customer_email_virtual"].f-p_input').val();
            if($('#myId1').prop('checked') && (!email.match(/^[0-9a-z-\.]+\@[0-9a-z-]{2,}\.[a-z]{2,}$/i)) || email.match(/^[0-9]+\@eco\-u\.ru/i)) {
                $('.f-p_input[data-name="customer_email"]').addClass('input-error_2');
                $('html, body').animate({
                    scrollTop: '0px'
                }, 'fast');
                return false;
            } 
            if(email_hidden && email == '') {
                email = email_hidden;
            }
            var newsletter = $('#myId1').prop('checked') ? 1 : 0;
            $.post('/?route=ajax/index/ajaxSetCustomerData', {
                addresses: addresses,
                firstname: firstname,
                telephone: telephone,
                email: email,
                newsletter: newsletter
            }, function(msg){
                if(msg.status == 'success') {
                    msg.dadata.forEach(function(item, i, arr){
                        $('.f-p_input[data-target-id="'+item.id+'"]').val(item.value);
                    });
                    $('.f-p_input').removeClass('input-error_2');
                    $('.f-p_submit').html('Изменения сохранены').addClass('changes-applied');
                    setTimeout(function(){
                        $('.f-p_submit').html('Сохранить изменения').removeClass('changes-applied');
                    }, 3000);
                } else {
                    
                }
            }, "json");
        });
        
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
        
        $('.slider-preferable-products').slick({
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
        
        function InitClamp(selector) {
            $(selector).each(function(i, item, arr){
                if($(this).text()) {
                    $clamp(item, {clamp: 2});
                }
            });
        }
        InitClamp('.p-o_link a, .p-o_short-descr');
        
        window.searchTimer = false;
        $('.b-seach input[type="text"]').keyup(function(){
            clearTimeout(window.searchTimer);
            if($(this).val().length > 0) {
                $('.b-seach .cancel-search').show();
            } else {
                $('.b-seach .cancel-search').hide();
            }
            window.searchTimer = setTimeout(function(){
                var search = $('.b-seach input[type="text"]').val();
                if(search.length >= 2) {
                    $.get('/?route=ajax/index/ajaxSearchProducts', {
                        search: search
                    }, function(msg){
                        $('html, body').stop().animate({
                            scrollTop: $('.fond-catalog').offset().top - $('header.sticker').height()
                        });
                       
                        $('#contentcontainer3 div.container').html(msg);
                        if(!$('.tabs__catalog').find('#search-content').hasClass('active')) window.saved_active_block = $('ul.tabs__catalog').find('li.active');
                        InitClamp('.p-o_link a, .p-o_short-descr');
                        $('.tabs__catalog').find('li:nth-child(4)').click();
                        initDropDown();
                        BindAddToCartEvents($('#search-content'));
                    });
                }
            }, 500);
        });
        
        $('.b-seach .cancel-search').on('click', function(e){
            $('.b-seach input[type="text"]').val('');
            $(this).hide();
            window.saved_active_block.click();
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
                               location.href = '/';
                           } 
                        }, 'json');
                });
        }
        
        if(window.location.hash.indexOf('prod') > 0) {
            var target = window.location.hash.replace('#', '');
            if($('#catsorted_'+target).length > 0) {
                target = '#catsorted_'+target;
                $('.modal9').click();
            } else {
                target = '#asorted_'+target;
            }
            setTimeout(function(){
                $('html, body').stop().animate({
                    scrollTop: $(target).offset().top - 200
                });
            }, 500, target);
        }
        function InitSuggestions() {
            $('.f-p_input[data-name="customer_address"],input#delivery_address[type="text"]').not('.suggestions-input').suggestions({
                token: "a4ad0e938bf22c2ffbf205a4935ef651fc92ed52",
                type: "ADDRESS",
                count: 5,
                /* Вызывается, когда пользователь выбирает одну из подсказок */
                onSelect: function(suggestion) {
                    console.log(suggestion);
                }
            });
        }
        InitSuggestions();
        
        if($.cookie('cYloc') == location.pathname && $.cookie('cYpos')) {
            var yPos = $.cookie('cYpos'),
                yBlk = $.cookie('cYblk'),
                yLnk = $.cookie('cYlnk');
            if(yPos && yLnk && yBlk) { 
                if(yLnk != 0) {
                    $('.tabs__catalog li').eq(yLnk).click();
                }
                
                $('.tabs__catalog li').removeClass('active');
                $('section.fond-catalog div.tabs__block').css('opacity', '0').removeClass('active');

                $('.tabs__catalog li').eq(yLnk).addClass('active');
                $('section.fond-catalog div.tabs__block').eq(yBlk).css('opacity', '1').addClass('active');

                $('.tabs__block.active').animate({opacity:'1'}, function(){
                    $(document).scrollTop(yPos);
                });
                $('.tabs__block:not(.active)').animate({opacity:'0'});
            }
        }
        
        setTimeout(function(){
            $(window).scroll(function() {
                $.cookie('cYpos', $(document).scrollTop(), { expires: 1 });
                $.cookie('cYloc', location.pathname, { expires: 1 });
                $.cookie('cYlnk', $('.tabs__catalog li.active').index(), { expires: 1 });
                $.cookie('cYblk', $('section.fond-catalog div.tabs__block.active').index('.tabs__block'), { expires: 1 });
            });
        }, 500);
        $('body').append('<iframe name="ph_iframe" src="/auth.php" style="display:none;"></iframe>');
        
        // Images lazy load
        function bLazyPluginInit()
        {
            var bLazy = new Blazy({
                offset: 1000,
                success: function(element){
                    //setTimeout(function(){
                        //var parent = element.parentNode;
                        //parent.className = parent.className.replace(/\bloading\b/,'');
                    //}, 200);
                }
           });
        }
        bLazyPluginInit();
});