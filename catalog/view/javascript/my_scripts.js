$(document).ready(function(){
    
 	// $(".sticker").sticky({
	// 	topSpacing: 0
	// });

	// $(".f-c_top").sticky({
	// 	topSpacing: 67,
	// 	className: 'is-sticky2',
	// });
    
	/*** start basket ***/
	// $(document).on('opening', '.modal-basket', function () {
	// 	$('.scroll-pane').jScrollPane();
	//     var initDropDown2 = function() {
	//         var $items = $('.modal-basket .tech:not(.dd-ready)');
	//         if(!$items.length) {
	//             return;
	//         }
	//         var i = 0;
	//         $items.each(function() {
	//             if(i == 10) {
	//                 return false;
	//             }
	//             var $item = $(this);
 //                    i++;
 //                    if($item.hasClass('dd-ready')) return true;
	//             $item.addClass('dd-ready');
	//             $item.selectric();
	//         });
	//         setTimeout(function() {
	//                 initDropDown2();
	//         }, 10);
	//     }
	//     initDropDown2();
	// });

	// $(document).on('opened', '.modal-basket', function () {
	// 	$('.scroll-pane').jScrollPane();
	// 		/* select */
	//     var initDropDown3 = function() {
	//         var $items = $('.modal-basket .tech:not(.dd-ready)');
	//         if(!$items.length) {
	//             return;
	//         }
	//         var i = 0;
	//         $items.each(function() {
	//             if(i == 10) {
	//                 return false;
	//             }
	//             var $item = $(this);
 //                    i++;
 //                    if($item.hasClass('dd-ready')) return true;
	//             $item.addClass('dd-ready');
	//             $item.selectric();
	//         });
	//         setTimeout(function() {
	//                 initDropDown3();
	//         }, 10);
	//     }
	//     initDropDown3();
	// });
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

    $('.f-p_address_remove').click(function(){
        var tElement = $(this).data('target');
        $.get('/?route=ajax/index/ajaxRemoveAddress', {address_id: tElement}, function(msg){
            if(msg.status == 'success') {
                $('.f-p_address_container[data-index="'+tElement+'"]').remove();
                $('.f-p_address_remove').eq($('.f-p_address_remove').length-1).addClass('last');
            }
        }, 'json');
    });
    
	/*** tabs profile ***/
	$('ul.tabs__caption').on('click', 'li:not(.active)', function() {
		$(this)
		.addClass('active').siblings().removeClass('active')
		.closest('.modal-profile').find('.tabs__content').removeClass('active').eq($(this).index()).addClass('active');
	});
	/*** end ***/

	/* select2 */
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
	

	/*** END tabs catalog  ***/

	/*** **/
    
   
	/*  */
	/** dynamic input **/
	$('.f-p_plus').click(function() {
        $('.f-p_address_remove').removeClass('last');
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
		// $('.liTabs_2').liTabs({
		// 	duration: 500,		//A string or number determining how long the animation will run
		// 	effect:'hSlide' //clasic, fade, hSlide, vSlide
		// });

  //       $('.liTabs_2').find('li:nth-child(2) a, li:nth-child(3) a').click(function(){
  //           $('.fond-f-p').remove();
  //       });
  //       $('.liTabs_2').find('li:nth-child(2) a, li:nth-child(3) a').css('pointer-events', 'none');
	/* end tabs */

	/* calendar */
	// $.datepicker.setDefaults($.extend(
	//   $.datepicker.regional["ru"])
	// );
	// $( "#datepicker" ).datepicker({
	// 	minDate: "-30",			//Минимальная дата которую можно выбрать, т.е. -30 дней от "сейчас"
	// 	maxDate: "+1m +20d",  //Максимальная дата которую можно выбрать, т.е. + 1 месяц, 1 неделя, и 3 дня от "сейчас"
	// });
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

	// $(".sidebar_right").stick_in_parent({
	// 	offset_top: 20,
	// });
	/* active checked */
	// function check_agreement(){
	// 	var ischeck = $('.check_agreement').prop('checked');
	// 	if (ischeck) {
	// 		$('.o-i_submit').prop('disabled', false);
	// 		$('.o-i_submit').removeClass('o-i_submit2');
	// 	} else {
	// 		$('.o-i_submit').prop('disabled', true);
	// 		$('.o-i_submit').addClass('o-i_submit2');
	// 	}
	// }
	// check_agreement();
	// $('.modal-basket').delegate('.check_agreement', 'change', function() {
	// 	check_agreement();
	// });
        
    // $('.modal-basket').delegate('select#delivery_address_m', 'change', function(){
    //     if($(this).val() == 0) {
    //         $(this).parents('.selectric-wrapper').remove();
    //         $('.delivery-address-m').html('<input type="text" class="ca-i_input ta-center" id="delivery_address_m" value="" placeholder="Новый адрес доставки">');
    //         $('input#delivery_address_m[type="text"]').not('.suggestions-input').suggestions({
    //             token: "a4ad0e938bf22c2ffbf205a4935ef651fc92ed52",
    //             type: "ADDRESS",
    //             count: 5,
    //             /* Вызывается, когда пользователь выбирает одну из подсказок */
    //             onSelect: function(suggestion) {
    //                 $('.block-delivery-price').hide();
    //                 $('.shipping-amount').hide();
    //                 $('.c-m_submit').html('Рассчитать стоимость доставки');
    //                 console.log(suggestion);
    //             }
    //         });
    //         $('input#delivery_address_m').keydown(function(){
    //             $('.block-delivery-price').hide();
    //             $('.shipping-amount').hide();
    //             $('.c-m_submit').html('Рассчитать стоимость доставки');
    //         });
    //     } else {
    //         $('.block-delivery-price').hide();
    //         $('.shipping-amount').hide();
    //         $('.c-m_submit').html('Рассчитать стоимость доставки');
    //     }
    // });



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

    $(".menu-cat-link").on("click",function(){
    	$(".hidden-menu").slideUp("slow");
    });

	$(document).click( function(event){
		$(this).find(".h-menu").removeClass("js-background");
	});

	/** **/

 //    $.mask.definitions['h'] = "[0-6,9]";
	// $("#date").mask("99-99",{placeholder:""});
	// $("#date2").mask("99-99",{placeholder:""});
	// $("#phone").mask("+7 (h99) 999-99-99");
	// $('#phone2').mask("+7 (h99) 999-99-99");
	// $("#phone3").mask("+7 (h99) 999-99-99");
	// $("#phone4").mask("+7 (h99) 999-99-99");
	// $("#phone5").mask("+7 (h99) 999-99-99");

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

        // BindAddToCartEvents($('body'));
		// Cart function was here
        
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
        
        // InitClamp('.p-o_link a, .p-o_short-descr');
        
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
                        //initDropDown();
                        //BindAddToCartEvents($('#search-content'));
                    });
                }
            }, 500);
        });
        
        $('.b-seach .cancel-search').on('click', function(e){
            $('.b-seach input[type="text"]').val('');
            $(this).hide();

            if( $('.f-c_top').find('.modal8').hasClass('active') == true ) { $('.f-c_top').find('.modal8').trigger('click'); }
            else if( $('.f-c_top').find('.modal-hide').hasClass('active') == true ) { $('.f-c_top').find('.modal-hide').trigger('click'); }
            else { $('.f-c_top').find('.modal9').trigger('click'); }
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
        
        /*
        if($.cookie('cYloc') == location.pathname && $.cookie('cYpos')) {
            var yPos = $.cookie('cYpos'),
                yBlk = $.cookie('cYblk'),
                yLnk = $.cookie('cYlnk');
                
            // if(yPos && yLnk && yBlk) { 
            //     if(yLnk != 0) {
            //         $('.tabs__catalog li').eq(yLnk).click();
            //     }
                
            //     $('.tabs__catalog li').removeClass('active');
            //     $('section.fond-catalog div.tabs__block').css('opacity', '0').removeClass('active');

            //     $('.tabs__catalog li').eq(yLnk).addClass('active');
            //     $('section.fond-catalog div.tabs__block').eq(yBlk).css('opacity', '1').addClass('active');

            //     $('.tabs__block.active').animate({opacity:'1'}, function(){
            //         $(document).scrollTop(yPos);
            //     });
            //     $('.tabs__block:not(.active)').animate({opacity:'0'});
            // }
        }
        */
        
        // setTimeout(function(){
        //     $(window).scroll(function() {
        //         $.cookie('cYpos', $(document).scrollTop(), { expires: 1 });
        //         $.cookie('cYloc', location.pathname, { expires: 1 });
        //         $.cookie('cYlnk', $('.tabs__catalog li.active').index(), { expires: 1 });
        //         $.cookie('cYblk', $('section.fond-catalog div.tabs__block.active').index('.tabs__block'), { expires: 1 });
        //     });
        // }, 500);
        // $('body').append('<iframe name="ph_iframe" src="/auth.php" style="display:none;"></iframe>');
});

// All loaded
// $(window).on('load', function () {
// 		bLazyPluginInit();
// });

// Functions
	// Images
        // function bLazyPluginInit(){
        //     var bLazy = new Blazy({
        //         offset: 1000,
        //         success: function(element){
        //         }
        //    });
        // }
    // ---
	// function InitClamp(selector) {
 //        $(selector).each(function(i, item, arr){
 //            if($(this).text()) {
 //                $clamp(item, {clamp: 2});
 //            }
 //        });
 //    }
// ---