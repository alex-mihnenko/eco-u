var app = {
  dashboard: {
    enjoyhint: null
  },
  modals: {
  	auth: null,
  	recovery: null
  },
  customer: {}
}

$(document).ready(function() {
	// Init
		// Handlers
	    	$(window).resize(columnizerInit);
	    // ---

	    // Roistat
	    	checkRoistat();
	    // ---

	    // Hashes
	    	checkHash();
	    // ---

		// EnjoyHint
			if( $('.qwe').find('.list-products').length > 0 ){
				// ---
					var enjoyhintVMFlag = Cookies.get('flags-enjoyhint-vertical-menu');
					if( typeof enjoyhintVMFlag == 'undefined' ){
						// ---
							app.dashboard.enjoyhint = false;
							Cookies.set('flags-enjoyhint-vertical-menu', true, { expires: 90 });
						// ---
					}
				// ---
			}
		// ---

		// Masks
			$('[name="phone"]').mask("+7 (h99) 999-99-99");
		// ---
	// ---

	// Scroll
		$('[data-action="scrollto"]').on('click', function(e){
			var anchor = $(this).attr('href');
			$("html, body").animate({ scrollTop: $(anchor).offset().top });
			//e.stopPropagation();
			//e.preventDefault();
		});

		$(window).scroll(function(event){


			if( app.dashboard.enjoyhint == false && $(window).width() >= 1280 ) {
				if( $(this).scrollTop() >= $('#contentcontainer2').offset().top - 200 ){
					app.dashboard.enjoyhint = true;
					enjoyhintForVerticalMenu();
				}
			}
	       
	    });
	// ---

	// Cart
		// Input phone
			$('.modal-basket').on('keyup change paste', 'input[id="phone2_m_disable"]', function(){
				$phone = $(this).val().replace(/\D/g,'');

				if( $phone.length == 11 ){
					// ---
						$.post('.?route=ajax/index/getCustomerByTelephone', {telephone:$phone}, function(data){
							// ---

							if( data.result == true ){
								// ---
									$('input[id="customer-name"]').val(data.customer.firstname);
									

									var options = '';
									$.each(data.addresses, function(key, address){
										options += '<option value="'+address.address_id+'">'+address.address_1+'</option>';
									});

									if( options != '' ){
										// ---
											options += '<option class="new_address" value="0">Новый адрес доставки</option>';

											$('.delivery-address-m').html(''+
												'<select id="delivery_address_m" name="tech" class="tech ca-i_input ta-center">'+
													options+
												'</select>'+
											'');
										// ---
									}
									else{
										// ---
											$('.delivery-address-m').html('<input type="text" class="ca-i_input ta-center" id="delivery_address_m" value="" placeholder="Новый адрес доставки">');
										// ---
									}					
								// ---
							}
							else {
								$('.delivery-address-m').html('<input type="text" class="ca-i_input ta-center" id="delivery_address_m" value="" placeholder="Новый адрес доставки">');
							}
							// ---
						},'json');
					// ---
				}
			});
		// ...

		// Close
			$(document).on('closing', '.remodal', function (e) {
			  if( $(this).hasClass('modal-basket') ) {
			  	LoadCart();
			  	console.log('Modal is closing' + (e.reason ? ', reason: ' + e.reason : ''));
			  }
			});
		// ---
	// ---

	// Catalog
		// Filter
			$('.tabs__catalog li.modal8').on( 'click', function(){
				if(!$(this).hasClass('active')) {
					// ---
						$category_id = $('#category').attr('data-id');
						$type = "alphabetic";
						
						$.post('/?route=product/category/loadCategoryTemplate', {category_id: $category_id , type: $type}, function(data){
							// ---

								// List
									$listAlphabeticCount = 0;

									$('.qwe2').find('.list-alphabetic').html('');
									$('.remodal.modal-alphabetic').find('.list-m_a').html('');

									$.each(data.alphabet_list, function(key, val){
										$('.qwe2').find('.list-alphabetic').append('<li><a href="#letter_'+key+'"><span>'+val+'</span></a></li>');
										$('.remodal.modal-alphabetic').find('.list-m_a').append('<li><a href="#letter_'+key+'">'+val+'</a></li>');
										$listAlphabeticCount++;
									});

									$('.qwe2').find('.list-alphabetic').append('<li class="magic-line2"></li>');
								// ---

								// Products
									$('#container-products-alphabetic').find('.container').html(data.template);
								// ---

								// Handlers
									$('.list-alphabetic').ddscrollSpy({
										highlightclass: "selected",
										scrolltopoffset: -200,
									}); 

									var $magicLine2 = $(".magic-line2");
		
									$(".list-alphabetic li").find("a").click(function() {
										$el6 = $(this);
										leftPos2 = $el6.position().top;
										newWidth2 = $el6.parent().width();
										elem_click_z23 = true;
										$magicLine2.data("origTop", leftPos2).data("origWidth", newWidth2);
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
								// ---
								
								$("html, body").animate({ scrollTop: $('.fond-catalog').offset().top });
								
								if(alphabeticScroller === null) {
				                    alphabeticScroller = new AlphabeticScrollerProto();
				                } else {
				                    alphabeticScroller.refresh();
				                }

								setTimeout(function() { bLazyPluginInit(); }, 200);
							// ---
						}, 'json');

					// ---
				} else {
					e.preventDefault();
					return false;
				}
			});

			// Categories
				$('.tabs__catalog li.modal9').on( 'click', function(e){
					if(!$(this).hasClass('active')) {
						$("html, body").animate({ scrollTop: $('.fond-catalog').offset().top });
						setTimeout(function() {
							bLazyPluginInit();
						}, 200);
						
					} else {
						e.preventDefault();
						return false;
					}
				});
			// ---

			// List
				$('.tabs__catalog li.modal-hide').on( 'click', function(){
					// ---
						if(!$(this).hasClass('active')) {
							// ---
								$category_id = $('#category').attr('data-id');
								$type = "list";
								
								$.post('/?route=product/category/loadCategoryTemplate', {category_id: $category_id , type: $type}, function(data){
									// ---
										// Products
											$('#container-products-list').find('.auto-columnizer.clearfix').html(data.template);
										// ---

										columnizerInit();
										$("html, body").animate({ scrollTop: $('.fond-catalog').offset().top });
									// ---
								}, 'json');

							// ---
						} else {
							e.preventDefault();
							return false;
						}
					// ---
				});
			// ---

			$('.list-products').ddscrollSpy({
				highlightclass: "selected2",
				scrolltopoffset: -185,
			});
		// ---

		// Show more
			$('#category .tabs__block').on('click', '.show-more', function(e){
				// ---
		            var $button = $(this);
		            var dataMode = $(this).data('mode');
		            var dataTarget = $(this).data('target');
		            var $btns = $('.show-more[data-mode="' + dataMode + '"][data-target="' + dataTarget + '"]');
		            var pElement = $(this).parent().find('.list-letter');

		            if(pElement.hasClass('ll-opened')) {
		            	// ---
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
			            // ---
		            } else {
		            	// ---
			                pElement.addClass('ll-opened');
			                $btns.each(function() {
			                    var $btn = $(this);
			                    $btn.data('html', $btn.html());
			                });
			                $btns.html('скрыть');
		            	// ---
		            }

		            if(pElement.hasClass('all-loaded')) {
		                return;
		            }

		            var nInclude = [];
		            
		            pElement.find('li[data-product]').each(function(i, item, arr){
		                nInclude[nInclude.length] = $(item).data('product');
		            });
		            
		            $.post('/?route=ajax/index/ajaxShowMore', { mode: $(this).data('mode'), target: $(this).data('target'), not_include: nInclude }, function(products){
		                // ---
			                if(!pElement.hasClass('all-loaded')) {
			                    pElement.addClass('all-loaded');
			                    pElement.append(products);
			                    //initDropDown();
			                    //BindAddToCartEvents(pElement.find('li[data-type="dynamic"]'));
			                    InitClamp('.p-o_link a, .p-o_short-descr');
			                }

			                bLazyPluginInit();
		                // ---
		            });
		        // ---
	        });
		// ---

		// Products modal
			$("#category .tabs__block").on('click', '.n-p_list', function(e){
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
		                //initDropDown();
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
		// ---

		// Add to cart
			$('.products-grid').on('click', '.p-o_submit', function(e){
				// ---
	                e.preventDefault();
	                var pElement = $(this).parents('.p-o_block');
	                var product_id = pElement.find('input[name="product_id"]').val();
	                var label = pElement.find('.selectric .label').html();
	                var quantity = 1;
	                var packaging = parseFloat(label);
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
	                    packaging: packaging,
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
                // ---
            });
		// ---
	// ---

	// Account
		app.modals.basket = $('[data-remodal-id="modal-basket"]').remodal();
		app.modals.auth = $('[data-remodal-id="modal"]').remodal();
		app.modals.recovery = $('[data-remodal-id="modal-recovery"]').remodal();
		app.modals.coupon = $('[data-remodal-id="modal-coupon"]').remodal();
		app.modals.phone = $('[data-remodal-id="modal-phone"]').remodal();

		$('.remodal').on('focus', '.input-error_1 input', function(){
			$(this).parents('.remodal').find('.t-c_input').removeClass('input-error_1');
			$(this).parents('.remodal').find('.message-error').hide();
		});

		// Auth
			$('#form-auth').submit(function(){
				console.log('Auth init');
				
				var form = $(this);

				var phone = $('#phone3').val();
	            var pass = $('#password3').val();

	            if(phone != '' && pass != '') {
	                $.post('/?route=ajax/index/ajaxLoginByPhone',{ telephone: phone, password: pass}, function(data){
	                    if(data.status == 'success') {
	                    	console.log('Auth success');

	                        window.location.href = '/my-account';
	                    } else {
	                    	console.log('Auth error');

	                        form.find('.t-c_input').addClass('input-error_1');
	                        form.find('.message-error').html( data.message );
	                        form.find('.message-error').show();
	                    }
	                }, "json");
	            }

				return false;
			});
		// ---

		// Recovery
			$('#form-registration').submit(function(){
				console.log('Registration init');

				var $form = $(this);
				var $modal = $(this).parents('.remodal');
				var firstname = $form.find('[name="firstname"]').val();
				var telephone = $form.find('[name="phone"]').val();

	            if(telephone != '' ) {
	                $.post('/?route=ajax/index/registrationCustomer',{firstname: firstname, telephone: telephone}, function(data){
	                    // ---
	                    	console.log(data);

	                    	if( data.status == 'error' ) {
	                    		// ---
	                    			$form.find('.t-c_input').addClass('input-error_1');
	                    			$form.find('.message-error').html(data.message);
	                    			$form.find('.message-error').show();
	                    		// ---
	                    	}
	                    	else{
	                    		// ---
	                    			$modal.find('.form').css('display','none');

	                    			$modal.find('.success .message-success').html(data.message);
	                    			$modal.find('.success .message-success').show();
	                    			$modal.find('.success').fadeIn('fast');

									setTimeout(function(){
	                    				$modal.find('.tabs__caption li.auth').trigger('click');
									},3000);
	                    		// ---
	                    	}

							return false;
	                    // ---
	                }, "json");
	            }

				return false;
			});
		// ---

		// Recovery
			$('[data-remodal-id="modal"]').on('click', '[data-action="auth-recovery-init"]', function(){
				// ---
					app.modals.auth.close();

			        $('#phone4').val('');

					setTimeout(function(){
						app.modals.recovery.open();
					},500);
				// ---
			});

			$('#form-recovery').submit(function(){
				console.log('Recovery init');

				var $form = $(this);
				var $modal = $(this).parents('.remodal');
				var telephone = $('#phone4').val();

	            if(telephone != '' ) {
	                $.post('/?route=ajax/index/recoveryPasswordByTelephone',{ telephone: telephone}, function(data){
	                    // ---
	                    	console.log(data);

	                    	if( data.status == 'error' ) {
	                    		// ---
	                    			$form.find('.t-c_input').addClass('input-error_1');
	                    			$form.find('.message-error').html(data.message);
	                    			$form.find('.message-error').show();
	                    		// ---
	                    	}
	                    	else{
	                    		// ---
	                    			$modal.find('.form').css('display','none');

	                    			$modal.find('.success .message-success').html(data.message);
	                    			$modal.find('.success .message-success').show();
	                    			$modal.find('.success').fadeIn('fast');


							        $('#phone4').val('');

									setTimeout(function(){ app.modals.recovery.close(); },3000);
									setTimeout(function(){ app.modals.auth.open(); },3500);
	                    		// ---
	                    	}

							return false;
	                    // ---
	                }, "json");
	            }

				return false;
			});
		// ---

		// Coupon
			$('#form-coupon').submit(function(){
				console.log('Сoupon init');

				var $form = $(this);
				var $modal = $(this).parents('.remodal');
				var coupon = $form.find('input[name="coupon"]').val();

	            if(coupon != '' ) {
	                $.post('/?route=ajax/index/ajaxApplyCoupon',{ code: coupon}, function(data){
	                    // ---
	                    	console.log(data);

	                    	if( data.status == 'error' ) {
	                    		// ---
	                    			$form.find('.t-c_input').addClass('input-error_1');
	                    			$form.find('.message-error').html(data.message);
	                    			$form.find('.message-error').show();
	                    		// ---
	                    	}
	                    	else{
	                    		// ---
	                    			LoadCart();
									setTimeout(function(){ app.modals.coupon.close(); },1000);
									setTimeout(function(){ app.modals.basket.open(); },1500);
	                    		// ---
	                    	}

							return false;
	                    // ---
	                }, "json");
	            }

				return false;
			});
		// ---

		// Phone
			$('#form-phone').submit(function(){
				console.log('Phone init');

				var $form = $(this);
				var $modal = $(this).parents('.remodal');
				var phone = $form.find('input[name="phone"]').val();

	            if(phone != '' ) {
	                $.post('/?route=ajax/index/sendCallRequest',{ phone: phone}, function(data){
	                    // ---
	                    	if( data.status == 'error' ) {
	                    		// ---
	                    			$form.find('.t-c_input').addClass('input-error_1');
	                    			$form.find('.message-error').html(data.message);
	                    			$form.find('.message-error').show();
	                    		// ---
	                    	}
	                    	else{
	                    		// ---
	                    			$modal.find('.form').css('display','none');

	                    			$modal.find('.success .message-success').html(data.message);
	                    			$modal.find('.success .message-success').show();
	                    			$modal.find('.success').fadeIn('fast');


							        $form.find('input[name="phone"]').val('');

									setTimeout(function(){ app.modals.phone.close(); },5000);
									setTimeout(function(){
										$modal.find('.form').css('display','block');

		                    			$modal.find('.success .message-success').html('');
		                    			$modal.find('.success .message-success').hide();
		                    			$modal.find('.success').css('display','none');
									},5500);
	                    		// ---
	                    	}

							return false;
	                    // ---
	                }, "json");
	            }

				return false;
			});
		// ---

		// Payment
			$('[data-action="rbs-payment"]').on('click', function(e){
				// ---
					$order_id = $(this).attr('data-order-id');

					$.post('.?route=ajax/index/rbsPostPayment', {order_id:$order_id}, function(data){
						// ---
							console.log(data.redirect);
							document.location = data.redirect;
						// ---
					},'json');

					e.preventDefault();
				// ---
			});
		// ---
	// ---

});


// Catalog
	function columnizerInit() {
		var $columnizer = $('.auto-columnizer');
		var columnizerHTML = $columnizer.html();

		$columnizer.find('.no-pictures').each(function() {
			var $this = $(this);
			$columnizer.append($this);
		});
		$columnizer.find('.column').remove();
		$columnizer.find('br').remove();

		var windowsize789 = $(window).width();
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
// ---

// EnjoyHint
	function enjoyhintForVerticalMenu(){
		var enjoyhint_instance = new EnjoyHint({});

		var enjoyhint_script_steps = [
		  {
		    'click .all-l_a2' : 'Прокручивайте вверх или вниз, чтобы посмотреть все категории.',
		    shape: 'circle',
		    timeout: 500,
		    scrollAnimationSpeed: 500,
		    nextButton: { className:'enjoyhint-next',text:'Далее'},
		    skipButton: { className:'enjoyhint-skip',text:'Понятно!'},
		    showNext: true,
		    showSkip: true,
		  },
		  {
		    'click .all-l_a2' : 'Нажмите на нужную категорию, чтобы посмотреть товары.',
		    shape: 'rect',
		    nextButton: { className:'enjoyhint-next',text:'Далее'},
		    skipButton: { className:'enjoyhint-skip',text:'Понятно!'},
		    showNext: false,
		    showSkip: true,
		  } 
		];

		// Set script config
		enjoyhint_instance.set(enjoyhint_script_steps);

		// Run Enjoyhint script
		enjoyhint_instance.run();
	}
// ---

// Helpers
	function checkRoistat() {
		// ---
			var roistat_visit = Cookies.get('roistat_visit');

			if( typeof roistat_visit != 'undefined' ){
				// ---
					console.log(roistat_visit);
					$('body').append('<div class="roistat-visit"><p>№ '+roistat_visit+'</p></div>');
				// ---
			}
		// ---
	}

	function checkHash() {
		var hash = window.location.hash.replace('#','');
		console.log(hash);

		if( $('.b-accordion .b-accordion_title[data-marker="'+hash+'"]').length > 0 ){
			console.log($('.b-accordion .b-accordion_title[data-marker="'+hash+'"]').length);
			
			setTimeout(function(){
				$("html, body").animate({ scrollTop: $('.b-accordion .b-accordion_title[data-marker="'+hash+'"]').offset().top - 50 });
				$('.b-accordion').find('.b-accordion_title[data-marker="'+hash+'"]').trigger('click');
			},1000);
		}
	}
// ---