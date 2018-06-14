var app = {
  width: 0,
  height: 0,
  size: '',
  dashboard: {
    enjoyhint: null
  },
  modals: {
  	basket: null,
  	auth: null,
  	recovery: null,
  	coupon: null,
  	phone: null,
  	privacy: null
  },
  customer: {}
}

$(document).ready(function() {
	// Init
		initStart();

		// Handlers
			watchEnv();
			$(window).resize(watchEnv);
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
	// ---

	// Scroll
		$('[data-action="scrollto"]').on('click', function(e){
			var anchor = $(this).attr('href');
			$("html, body").animate({ scrollTop: $(anchor).offset().top - 100 });
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
		app.modals.basket = $('[data-remodal-id="modal-basket"]').remodal();
		app.modals.coupon = $('[data-remodal-id="modal-coupon"]').remodal();
		app.modals.privacy = $('[data-remodal-id="modal-privacy"]').remodal();

		// Input phone
			$(document).on('keyup change paste', '.modal-basket [name="telephone"]', function(){
				// ---
					$phone = $(this).val().replace(/\D/g,'');

					if( $phone.length == 11 ){
						$('[name="address"]').focus();
					}
				// ---
			});
		// ...

		// Input address
			$(document).on('keyup change paste', '.modal-basket [name="address"]', function(){
				// ---
					var $this = $(this);
					var $form = $(this).parents('form');
					var $modal = $(this).parents('.remodal');

					var address = $(this).val();
					var deliveryprice = parseInt($form.find('[name="deliveryprice"]').val());
					console.log(address);

					if( $this.hasClass('select') ) {
						console.log('Ok select');
						$this.parents('.delivery-address-container').html('<input type="text" class="form-input text-align-center text-align-left-xs input" name="address" value="" placeholder="Адрес доставки" required="">');
						initSuggestionsDadata();
					}

					if( deliveryprice != 0 ){
                        $form.find('[name="deliveryprice"]').val('-1');

                        $form.find('.cart-shipping-price .h4').html('');
                        $form.find('.cart-total-price .h4').html('');

                        $form.find('.cart-shipping-price').hide();
                        $form.find('.cart-total-price').hide();


                        $form.find('button[type="submit"]').html('Рассчитать стоимость доставки');
					}
				// ---
			});
		// ...

		// Close
			$(document).on('closing', '.remodal.modal-basket', function (e) {
			  	LoadCart();
			  	console.log('Basker modal is closing' + (e.reason ? ', reason: ' + e.reason : ''));
			});
		// ---

		// Privacy
			$(document).on('click', '.modal-basket .privacy span', function(){
				app.modals.privacy.open();
			});

			$(document).on('closing', '.remodal.modal-privacy', function (e) {
				app.modals.basket.open();
			  	console.log('Privacy modal is closing' + (e.reason ? ', reason: ' + e.reason : ''));
			});
		// ---

		// Coupon
			$('.modal-basket').on('click', '[data-action="show-apply-coupon"]', function(){
				$this = $(this);
				$modal = $(this).parents('.remodal');

				$this.hide();
				$modal.find('.coupon .apply').show();
			});

			$('.modal-basket').on('click', '[data-action="send-apply-coupon"]', function(){
				console.log('Сoupon init');

				var $modal = $(this).parents('.remodal');
				var coupon = $modal.find('input[name="coupon"]').val();

	            if(coupon != '' ) {
	                $.post('/?route=ajax/index/ajaxApplyCoupon',{code: coupon}, function(data){
	                    // ---
	                    	console.log(data);

	                    	if( data.status == 'error' ) {
	                    		// ---
	                    			$modal.find('.coupon .message-error').html(data.message);
	                    			$modal.find('.coupon .message-error').show();
	                    		// ---
	                    	}
	                    	else{
	                    		// ---
	                    			LoadCart();
	                    		// ---
	                    	}

							return false;
	                    // ---
	                }, "json");
	            }

				return false;
			});

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

		// Steps
			$('.modal-basket').on('click', '[data-step]', function(){
				$this = $(this);
				$modal = $(this).parents('.remodal');
				$step = parseInt($(this).attr('data-step'));

				$modal.find('.steps .step').hide();
				$modal.find('.steps .step').attr('data-display','default');
				$modal.find('.steps .step[data-marker="'+$step+'"]').show();
				$modal.find('.steps .step[data-marker="'+$step+'"]').attr('data-display','active');

				if($step > 1){	
					$modal.find('button.back').attr('data-step',($step-1));
					$modal.find('button.back').show();
				}
				else{
					$modal.find('button.back').hide();
				}

				$modal.parents(".remodal-wrapper").animate({ scrollTop: 0 });
			});
		// ---

		// Form
            $(document).on('submit', '.modal-basket form', function(){
				// Init
					var $form = $(this);
					var $modal = $(this).parents('.remodal');

					var order_id = parseInt($form.find('[name="order_id"]').val());
					var firstname = $form.find('[name="firstname"]').val();
					var telephone = $form.find('[name="telephone"]').val();
					var address = $form.find('[name="address"]').val();
					var comment = $form.find('[name="comment"]').val();

					var payment_method = $form.find('[name="payment_method"]').val();
					var payment_code = $form.find('[name="payment_code"]').val();
					var total = parseInt($form.find('[name="total"]').val());
					var discount = parseInt($form.find('[name="discount"]').val());
					var deliveryprice = parseInt($form.find('[name="deliveryprice"]').val());

					var date = $form.find('#delivery_date_m').val();
            		var time = $form.find('#delivery_time_m').val();
				// ---

				// Send request
					if( deliveryprice == -1 ){
						// Get shipping price
							$.post('/?route=ajax/index/ajaxGetDeliveryPrice', { order_id: order_id, firstname: firstname, telephone: telephone, address: address, payment_method: payment_method, payment_code: payment_code, total: total, deliveryprice: deliveryprice, date: date, time: time, comment: comment }, function(data){
			                	// ---
			                		if( typeof yaCounter33704824 != 'undefined' ){
				                        yaCounter33704824.reachGoal('checkout-delivery');
				                        gtag('event', 'Checkout', { 'event_category': 'Order', 'event_label': 'Create' });
			                		}

			                        if(data.status == 'success') {
			                        	// ---
				                            if((data.order_id !== undefined) && (data.order_id > 0)) {
				                                $form.find('[name="order_id"]').val(data.order_id);
				                            }

				                            if( data.first_purchase == true ){
				                            	discount = data.first_purchase_discount;
				                            	$form.find('[name="discount"]').val(discount);

				                            	$form.find('.cart-first-purchase [data-type="value"]').html(data.first_purchase_discount+' рублей');
				                            	$form.find('.cart-first-purchase').show();
				                            }

				                            $form.find('[name="address"]').val(data.address);
				                            $form.find('[name="deliveryprice"]').val(data.deliveryprice);

				                            $form.find('.cart-shipping-price [data-type="value"]').html(data.deliveryprice+' рублей');

				                            var newtotal = total+data.deliveryprice-discount;
				                            var newtotal_string = newtotal.toString();;

				                            // Check curency
				                            	var currency = 'рублей';

				                            	var totalend = parseInt(newtotal_string.substring(newtotal_string.length - 2));
										        var totallast = parseInt(newtotal_string.substring(newtotal_string.length - 1));

										        if( totalend > 10 && totalend <= 19 ) {
										            currency = 'рублей';
										        } else {
										          	if(totallast == 1 ) { currency = 'рубль'; } 
										            else if( totallast > 1 && totallast < 5 )  { currency = 'рубля'; }
										            else { currency = 'рублей'; }
										        }

				                            // ---

				                            $form.find('.cart-total-price [data-type="value"]').html(newtotal+' '+currency);

				                            $form.find('.cart-shipping-price').show();
				                            $form.find('.cart-total-price').show();


				                            $form.find('button[type="submit"]').html('Далее');
			                        	// ---
			                        }
			                        else{
			                        	alert(data.message);
			                        }
			                	// ---
			                }, "json");
						// ---
					}
					else {
						// Confirm
							$form.find('button[type="submit"]').html('Подождите');
							$form.find('button[type="submit"]').attr('disabled','true');
							$form.find('button[type="submit"]').attr('type','button');

							$.post('/?route=ajax/index/ajaxConfirmOrder', { order_id: order_id, firstname: firstname, telephone: telephone, address: address, payment_method: payment_method, payment_code: payment_code, total: total, deliveryprice: deliveryprice, date: date, time: time, comment: comment }, function(data){
			                	// ---
			                		if( typeof yaCounter33704824 != 'undefined' ){
										yaCounter33704824.reachGoal('checkout-confim');
			                        	gtag('event', 'Checkout', { 'event_category': 'Order', 'event_label': 'Confirm' });
			                        }

			                        if(data.status == 'success') {
			                        	// ---
						                    // Show success
						                    $modal.find('[data-marker="order-id"]').html(data.order_id);

						                    
											$step = parseInt($modal.find('.steps .step[data-display="active"]').attr('data-marker'));

											$modal.find('.steps .step').hide();
											$modal.find('.steps .step').attr('data-display','default');
											$modal.find('.steps .step[data-marker="'+($step+1)+'"]').show();
											$modal.find('.steps .step[data-marker="'+($step+1)+'"]').attr('data-display','active');
											$modal.find('button.back').hide();


											$modal.find('.step-last[data-marker="'+payment_code+'"]').show();
				                            
				                            if(typeof data.redirect != 'undefined' && data.redirect != '' ) {
				                            	// Redirect to online payment
				                            	setTimeout(function(){
						                        	document.location = data.redirect;
				                            	}, 3500);
						                    } else {
						                    	// Show success checkout
						                    	if( typeof yaCounter33704824 != 'undefined' ){
						                        	yaCounter33704824.reachGoal('checkout-success');
						                        }
						                    }
			                        	// ---
			                        }
			                        else{
			                        	alert(data.message);
			                        }
			                	// ---
			                }, "json");
						// ---
					}
				// ---

				return false;
			});


            $(document).on('click', '.modal-basket .payment-method', function(){
				// ---
					var $this = $(this);
					var $form = $(this).parents('form');
					var $container = $(this).parents('.payment-methods');

					var title = $this.attr('data-title');
					var code = $this.attr('data-code');

					$container.find('.payment-method').attr('data-display','default');
					$this.attr('data-display','active');

					$form.find('[name="payment_method"]').val(title);
					$form.find('[name="payment_code"]').val(code);
				// ---
			});
		// ---
	// ---

	// Catalog
		// Filter
			$('.tabs__catalog li.modal8').on( 'click', function(e){
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
		            
		            $.post('/?route=ajax/index/ajaxShowMore', { mode: $(this).data('mode'), target: $(this).data('target'), parent: $(this).data('parent'), not_include: nInclude }, function(products){
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

	                console.log(label);
	                console.log(quantity);
	                console.log(weight_class);
	                
	                $.post('/?route=checkout/cart/add', {
	                    product_id: product_id,
	                    quantity: quantity,
	                    packaging: packaging,
	                    weight_variant: weight_variant,
	                    special_price: special_price
	                }, function(msg){
	                    pElement.find('.p-o_select, .p-o_right').hide();
	                    pElement.find('.clearfix').append('<div class="not-available clearfix basket-added"><div class="n-a_text">'+label+' в корзине</div><input type="submit" value="" class="p-o_submit2"></div>');
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
		app.modals.auth = $('[data-remodal-id="modal"]').remodal();
		app.modals.recovery = $('[data-remodal-id="modal-recovery"]').remodal();
		app.modals.phone = $('[data-remodal-id="modal-phone"]').remodal();

		app.modals.repeat = $('[data-remodal-id="modal-repeat"]').remodal();

		$('.remodal').on('focus', '.input-error_1 input', function(){
			$(this).parents('.remodal').find('.t-c_input').removeClass('input-error_1');
			$(this).parents('.remodal').find('.message-error').hide();
		});

		// Auth
			$('#form-auth').submit(function(){
				console.log('Auth init');
				
				var $form = $(this);

				var telephone = $form.find('[name="phone"]').val();
				var password = $form.find('[name="password"]').val();

	            if(telephone != '' && password != '') {
	                $.post('/?route=ajax/index/ajaxLoginByPhone',{ telephone: telephone, password: password}, function(data){
	                    if(data.status == 'success') {
	                    	console.log('Auth success');

	                        window.location.href = '/my-account';
	                    } else {
	                    	console.log('Auth error');

	                        $form.find('.t-c_input').addClass('input-error_1');
	                        $form.find('.message-error').html( data.message );
	                        $form.find('.message-error').show();
	                    }
	                }, "json");
	            }

				return false;
			});
		// ---

		// Registration
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
					var $form = $(this).parents('#form-auth');
					var telephone = $form.find('[name="phone"]').val();
					
					app.modals.auth.close();

			        $('#form-recovery').find('[name="phone"]').val(telephone);

					setTimeout(function(){
						app.modals.recovery.open();
					},500);
				// ---
			});

			$('#form-recovery').submit(function(){
				console.log('Recovery init');

				var $form = $(this);
				var $modal = $(this).parents('.remodal');
				var telephone = $form.find('[name="phone"]').val();

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


							        $('#form-recovery').find('[name="phone"]').val('');
							        $('#form-auth').find('[name="phone"]').val(telephone);

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

		// Phone
			$('#form-phone').submit(function(){
				console.log('Phone init');

				var $form = $(this);
				var $modal = $(this).parents('.remodal');
				var phone = $form.find('input[name="phone"]').val();

	            if(phone != '' ) {
	                $.post('/?route=ajax/index/sendCallRequest',{ phone: phone}, function(data){
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

		// Repeat
			$('[data-action="order-repeat"]').on('click', function(e){
				// ---
					$order_id = $(this).attr('data-order-id');

					$.post('.?route=ajax/index/getCustomerOrder', {order_id:$order_id}, function(data){
						// ---
							$('[data-remodal-id="modal-repeat"]').find('.body').html(data.html);
							app.modals.repeat.open();
						// ---
					},'json');

					e.preventDefault();
				// ---
			});

			$(document).on('click', '[data-action="repeat-confirm"]', function(e){
				// ---
					$body = $(this).parents('.body');
					$order_id = $(this).attr('data-order-id');

					$.post('.?route=ajax/index/repeatCustomerOrder', {order_id:$order_id}, function(data){
						// ---
							console.log(data);

							if( data.status == 'error' ) {
	                    		// ---
	                    			$body.find('.message-error p').html(data.message);
	                    			$body.find('.message-error').show();
	                    		// ---
	                    	}
	                    	else{
	                    		// ---
	                    			$body.find('.content').css('display','none');

	                    			$body.find('.success .message-success').html(data.message);
	                    			$body.find('.success .message-success').show();
	                    			$body.find('.success').fadeIn('fast');
	                    		// ---
	                    	}

	                    	setTimeout(function(){
	                    		app.modals.repeat.close();
	                    	}, 3000);

							LoadCart();
						// ---
					},'json');

					e.preventDefault();
				// ---
			});
		// ---
	// ---

	// Components
		// Form
			$(document).on('click', '.form-checkbox', function(){
				// ---
					$this = $(this);
					$form = $(this).parents('form');
					$privacy = $(this).parents('.privacy');

					if( $this.attr('data-checked') == 'true' ){
						$this.attr('data-checked','false');
						$this.find('input').attr('checked', false);

						if( $privacy.length > 0 ){ $form.find('[type="submit"]').attr('disabled','true'); }
					}
					else{
						$this.attr('data-checked','true');
						$this.find('input').attr('checked', true);

						if( $privacy.length > 0 ){ $form.find('[type="submit"]').removeAttr('disabled'); }
					}

					console.log($privacy.length);
				// ---
			});
		// ---
	// ---
});

// General
	function watchEnv(){
		app.width = $(window).width();
		app.height = $(window).height();
		
		//console.log(app.width + ' x ' + app.height);

		// Get size
			if (app.width < 576) { app.size = 'xs'; }
			else if (app.width >= 576 && app.width < 768) { app.size = 'sm'; }
			else if (app.width >= 768 && app.width < 992) { app.size = 'md'; }
			else if (app.width >= 992 && app.width < 1200) { app.size = 'lg'; }
			else if (app.width >= 1200 && app.width < 1920) { app.size = 'xl'; }
			else if (app.width >= 1920) { app.size = 'xxl'; }
		// ---

		// Product grid
			$('.slider-profitable_offer .box-p_o').each(function(key,val){
				if( app.size == 'xs' || app.size == 'sm' || app.size == 'md' ){
					$(this).find('a').attr('target','_blank');
				}
				else {
					$(this).find('a').removeAttr('target');
				}
			});

			$('.tabs__block .box-p_o').each(function(key,val){
				if( app.size == 'xs' || app.size == 'sm' || app.size == 'md' ){
					$(this).find('a').attr('target','_blank');
				}
				else {
					$(this).find('a').removeAttr('target');
				}
			});

			$('.remodal .modal-product').each(function(key,val){
				if( app.size == 'xs' || app.size == 'sm' || app.size == 'md' ){
					$(this).find('a').attr('target','_blank');
				}
				else {
					$(this).find('a').removeAttr('target');
				}
			});
		// ---

		// Addon
			columnizerInit();
		// ---
	}

	function initStart(){
		$('[name="phone"]').mask("+7 (h99) 999-99-99");
		$('[name="telephone"]').mask("+7 (h99) 999-99-99");
	}
// ---

// Cart
	function initSuggestionsDadata(){
		// ---
			$('.modal-basket [name="address"]').suggestions({
                token: "a4ad0e938bf22c2ffbf205a4935ef651fc92ed52",
                type: "ADDRESS",
                count: 5,
                /* Вызывается, когда пользователь выбирает одну из подсказок */
                onSelect: function(suggestion) {}
            });
		// ---
	}

	function LoadCart() {
		// ---
		    $.get('/?route=ajax/index/ajaxGetCart', {}, function(data){
		    	// ---
		        
		            var totalPrice = data.total;
		            var totalPositions = data.count;
		            
		            var $html = $(data.html);
		            
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

		            $.get('/?route=ajax/index/ajaxGetOrderPrice', {}, function(data){
		                if(data.status == 'success') {
		                    $('#form-customer').find('.o-i_price').html(data.price + ' <span>руб</span>');
		                    //$('.b-discount .c-d_amount').html(data.discount);
		                } else {
		                    console.log('Ошибка расчета цены');
		                }
		            }, "json");


		            var $items = $('.modal-basket .tech:not(.dd-ready)');

			        if($items.length) {
	                    $items.each(function() {
	                        var $item = $(this);
	                        if($item.hasClass('dd-ready')) return true;
	                        $item.addClass('dd-ready');
	                        $item.selectric();
	                    });
			        }

			        // Init
			        	initStart();
						initSuggestionsDadata();
			        // ---
				// ---
		    }, "json");
		// ---
	}
// ---

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