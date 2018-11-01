var app = {
  page: '',
  width: 0,
  height: 0,
  size: '',
  coordinates: [],
  map: null,
  mkad: null,
  scroll: {
    check: true,
    category: '',
    menu: false
  },
  search: {
    flag: false,
    query: ''
  },
  dashboard: {
    enjoyhint: null
  },
  modals: {
  	product: null,
  	basket: null,
  	auth: null,
  	recovery: null,
  	coupon: null,
  	phone: null,
  	privacy: null
  },
  customer: {
  	roistat_visit: ''
  }
}


$(document).ready(function() {
	// Init
		initStart();
		initAuth();
		initParams();

		checkHash();
		
		// EnjoyHint
			// if( $('.qwe').find('.list-products').length > 0 ){
			// 	// ---
			// 		var enjoyhintVMFlag = Cookies.get('flags-enjoyhint-vertical-menu');
			// 		if( typeof enjoyhintVMFlag == 'undefined' ){
			// 			// ---
			// 				app.dashboard.enjoyhint = false;
			// 				Cookies.set('flags-enjoyhint-vertical-menu', true, { expires: 90 });
			// 			// ---
			// 		}
			// 	// ---
			// }
		// ---

		// Modal coupon
			var modalCouponFlag = Cookies.get('flags-modal-oneoff-coupon');
			
			if( typeof modalCouponFlag == 'undefined' ){
				// ---
					Cookies.set('flags-modal-oneoff-coupon', true, { expires: 30 });

					setTimeout(function(){
						//$('.modal[data-marker="modal-oneoff-coupon"]').modal('show');
						$('.adversting[data-target="modal-oneoff-coupon"]').css('display','block');
						setTimeout(function(){
							$('.adversting[data-target="modal-oneoff-coupon"]').attr('data-visible','true');
						}, 500);
					}, 10000);
				// ---
			}
		// ---
		
		// Nav
			if( $(window).scrollTop() >= 50 ){ $('nav').attr('data-style', 'solid'); }
			else{ $('nav').attr('data-style', 'default'); }
		// ---

		// Catalog
			scrollSpyCategory($(window).scrollTop());
			initTooltips();
		// ---

		// Scroll
			window.onscroll = function() {
				// ---
					if( $('.progress-view .marker').length > 0 ){
						progressScroll();
					}
				// ---
			}
		// ---
	// ---

	// Scroll
		$('[data-action="scrollto"]').on('click', function(e){
			var anchor = $(this).attr('scroll-anchor');
			$("html, body").animate({ scrollTop: $(anchor).offset().top - 100 });

			e.preventDefault();
		});

		$(window).scroll(function(event){
			// Enjoyhint
				if( app.dashboard.enjoyhint == false && app.width >= 1280 ) {
					if( $(this).scrollTop() >= $('.products-grid').offset().top - 200 ){
						app.dashboard.enjoyhint = true;
						enjoyhintForVerticalMenu();
					}
				}
			// ---

			// Nav
				if( $(this).scrollTop() >= 10 ){ $('nav').attr('data-style', 'solid'); }
				else{ $('nav').attr('data-style', 'default'); }
			// ---

			// Category
				if( app.page == 'category' ) {
					scrollSpyCategory($(this).scrollTop());
				}
			// ---
	       
	    });
	// ---

	// Nav
		/* Dropdown */
			$(document).find('.dropdown .list').css('visibility', 'visible');
			
			$(document).on('click', '.dropdown > [data-action="toggle"]', function(e){
				// ---
					var $this = $(this).parents('.dropdown');

					if( $this.attr('data-style') == 'open' ){
						// ---
							$this.attr('data-style','default');
						// ---
					}
					else {
						// ---
							$this.attr('data-style','open');
						// ---
					}

					e.stopPropagation();
				// ---
			});

			$(document).on('click', '.dropdown .sub-dropdown > [data-action="toggle"]', function(){
				// ---
					var $this = $(this).parents('.sub-dropdown');

					if( $this.attr('data-style') == 'open' ){
						// ---
							$this.attr('data-style','default');
						// ---
					}
					else {
						// ---
							$this.attr('data-style','open');
						// ---
					}
				// ---
			});
			
			$(document).on('click', '.dropdown .item:not(.sub-dropdown)', function(e){
				// ---
					var $this = $(this).parents('.dropdown');
					$this.attr('data-style','default');
					e.stopPropagation();
				// ---
			});

			$(document).on('click', function(e){
				// ---
					// Check menu
						if( $('nav .menu[data-marker="menu"]').attr('data-style') == 'open' ){
							// ---
								console.log('Hide menu');

								var $this = $('nav .menu[data-marker="menu"]');
								$this.attr('data-style','default');
							// ---
						}
					// ---
				// ---
			});
		/* Dropdown */

		$(document).on('click', 'nav [data-action="search-open"]', function(){
			// ---
				var $this = $(this);
				var $search = $this.parents('.flex').find('.search');

				if( $search.attr('data-style') == 'default' ){
					$search.attr('data-style','open');
					//$search.find('[name="search"]').focus();
				}
			// ---
		});

		$(document).on('click', 'nav [data-action="search-close"]', function(){
			// ---
				var $this = $(this);
				var $search = $this.parents('.flex').find('.search');

				if( $search.attr('data-style') == 'open' ){
					$search.attr('data-style','default');
				}

				$('#search-content div.container').html('');

				$('.all-l_a2').show();
                $('#search-content').hide();
                $('#container-products-categories').show();
			// ---
		});
		
		$(document).on('keydown', 'nav [name="search"]', function(){
			// ---
				var $this = $(this);
				
				app.search.query = $this.val();
				app.search.flag = true;
			// ---
		});

		$(document).on('keyup', 'nav [name="search"]', function(){
			// ---
				app.search.flag = false;

				setTimeout(function(){ catalogProductSearch(); }, 250);
			// ---
		});
	// ---

	// Cart
		app.modals.basket = $('[data-remodal-id="modal-basket"]').remodal();
		app.modals.coupon = $('[data-remodal-id="modal-coupon"]').remodal();
		app.modals.privacy = $('[data-remodal-id="modal-privacy"]').remodal();

		ymaps.ready(crateYaMap);

		LoadCart();
        
        // Remove From Cart
	        $('.modal-basket').on('click', '.remove', function() {
	            var removeLink = $(this).data('href');
	            $.get(removeLink, {}, function(data){
	                if(data.success) { LoadCart(); }
	            }, "json");
	        });

	         $('.modal-basket').on('click', '[data-action="cart-clear"]', function() {
	            $.post('/?route=ajax/index/cartClear',{}, function(data){
                    // ---
                    	LoadCart();
                    // ---
                }, "json");
	        });
	    // ---

        // Change Quantity
	        $(document).on('change', '.modal-basket select.change-m-cart-quantity', function() {
	            var $this = $(this);

	            var cartId = $this.data('cart-id');
	            var variant = parseFloat($this.data('cart-variant'));
	            var quantity = parseInt($this.val());
	            var prevQuantity = parseInt($this.data('cart-quantity'));

	            if(prevQuantity != quantity) { ChangeCartQuantity(cartId, quantity); }
	        });
	    // ---

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
			$(document).on('keyup change paste', '.modal-basket input[name="address"]', function(){
				// ---
					var $this = $(this);
					var $form = $(this).parents('form');
					var $modal = $(this).parents('.remodal');

					var address = $(this).val();
					var deliveryprice = parseInt($form.find('[name="deliveryprice"]').val());

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

			$(document).on('change', '.modal-basket select[name="address"]', function(){
				var $this = $(this);
				var $form = $(this).parents('form');
				var $modal = $(this).parents('.remodal');

				var address = $(this).val();
				var deliveryprice = parseInt($form.find('[name="deliveryprice"]').val());

				if( address == 0 ) {
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

				//$this.hide();
				$this.parents('.cart-page-total').addClass('light');
				$this.parents('.cart-page-total-mobile').addClass('light');
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

		// Bonus
			$('.modal-basket').on('click', '[data-action="apply-bonus"]', function(){

				var $modal = $(this).parents('.remodal');

                $.post('/?route=ajax/index/ajaxApplyBonus',{}, function(data){
                    // ---
                    	console.log(data);

                    	if( data.status == 'error' ) {
                    		// ---
                    			$modal.find('.modal .message-error').html(data.message);
                    			$modal.find('.modal .message-error').show();
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

				return false;
			});
		// ---

		// Steps
			$(document).on('click', '.modal-basket [data-step]', function(){
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
					var $button = $(this).find('button[type="submit"]');

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

				// Validation
            		var validation = true;

					$(this).find('.input-group.required').each(function(key,val){
						var $this = $(this).find('.form-input');
						var $name = $this.attr('name');
						var $value = $this.val();

						if( $name == 'phone' || $name == 'telephone' ) {
							$value = $value.replace(/\D/g,'');
							if( $value.length < 11 ) { $this.attr('data-style','error'); validation = false; return false; }
							else{ $this.attr('data-style','default'); }
						}
						else {
							if( $value.length < 3 ) { $this.attr('data-style','error'); validation = false; return false; }
							else{ $this.attr('data-style','default'); }
						}
					});

					if( !validation ){ return false; }

				// ---

				// Send request
					if( deliveryprice == -1 ){
						// Get shipping price
							$button.html('Подождите');
							$button.attr('disabled','true');
						
							calculateDistanceFromMKAD(function(){
            					// ---
            						var deliverydistance = parseInt($form.find('[name="deliverydistance"]').val());

									$.post('/?route=ajax/index/ajaxGetDeliveryPrice', { firstname: firstname, telephone: telephone, address: address, payment_method: payment_method, payment_code: payment_code, deliverydistance: deliverydistance, date: date, time: time, comment: comment }, function(data){
					                	// ---
					                		if( typeof yaCounter33704824 != 'undefined' ){
						                        yaCounter33704824.reachGoal('checkout-delivery');
						                        gtag('event', 'Checkout', { 'event_category': 'Order', 'event_label': 'Create' });
					                		}

					                        if(data.status == 'success') {
					                        	// ---
						                            if( data.first_purchase == true ){
						                            	discount = data.first_purchase_discount;
						                            	$form.find('[name="discount"]').val(discount);

						                            	$form.find('.cart-first-purchase [data-type="value"]').html(data.first_purchase_discount+' рублей');
						                            	$form.find('.cart-first-purchase').show();
						                            }

						                            $form.find('[name="deliveryprice"]').val(data.deliveryprice);

						                            $form.find('.cart-shipping-price [data-type="value"]').html(data.deliveryprice+' рублей');

						                            var newtotal = data.total;
						                            var newtotal_string = Math.floor(newtotal).toString();

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


						                            $button.html('Далее');
						                            $button.removeAttr('disabled');
					                        	// ---
					                        }
					                        else{
					                        	alert(data.message);
					                        }
					                	// ---
					                }, "json");
					            // ---
            				});
						// ---
					}
					else {
						// Confirm
							$button.html('Подождите');
							$button.attr('disabled','true');
							$button.attr('type','button');

							$.post('/?route=ajax/index/ajaxConfirmOrder', { firstname: firstname, telephone: telephone, address: address, payment_method: payment_method, payment_code: payment_code, date: date, time: time, comment: comment }, function(data){
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

				                            	// Redirect to success page
						                        setTimeout(function(){
						                        	document.location = data.success;
				                            	}, 3500);
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

		// Add to cart
			$(document).on('click', '.p-o_submit', function(e){
				// ---
	                e.preventDefault();

	                var pElement = $(this).parents('.p-o_block');
	                var product_id = pElement.find('input[name="product_id"]').val();
	                var quantity = 1;
	                var packaging = parseFloat(pElement.find('.form-select .select').attr('data-value'));
	                var weight_variant = parseFloat(pElement.find('.form-select .select').attr('data-index'));
	                var special_price = false;

	                var label = pElement.find('.form-select .select .options .current').text();

	                // Change button
	                	pElement.find('.p-o_select, .p-o_right').hide();
	                	pElement.find('.clearfix').append('<div class="not-available clearfix basket-added"><div class="n-a_text">'+label+' в корзине</div><input type="submit" value="" class="p-o_submit2"></div>');
	                // ---
	                
	                $.post('/?route=checkout/cart/add', { product_id: product_id, quantity: quantity, packaging: packaging, weight_variant: weight_variant, special_price: special_price }, function(msg){
	                    LoadCart();

	                	// Change button
		                    setTimeout(function(){
		                        pElement.find('.basket-added').remove();
		                        pElement.find('.p-o_select, .p-o_right').show();
		                    }, 3000, pElement);
		                // ---
	                }, "json");
                // ---
            });

            $(document).on('click', '.product .c-p_submit', function(e){
	            e.preventDefault();

	            if($(this).hasClass('sold')) return false;

                var pElement = $(this).parents('.c-p_right');
                var product_id = pElement.find('input[name="product_id"]').val();
                var btnTxt = pElement.find('.c-p_submit').html();

                var quantity = 1;
                var packaging = parseFloat(pElement.find('.form-select .select').attr('data-value'));
                var weight_variant = parseFloat(pElement.find('.form-select .select').attr('data-index'));
                var special_price = false;


                // Change button
                	pElement.find('.c-p_submit').html('Добавлено в корзину');
                // ---

                $.post('/?route=checkout/cart/add', { product_id: product_id, quantity: quantity, packaging: packaging, weight_variant:weight_variant, special_price: special_price }, function(msg){
                    LoadCart();
                   	
                   	// Change button
	                    setTimeout(function(){
	                        pElement.find('.c-p_submit').html(btnTxt);
	                    }, 3000, pElement);
	                // ---
                }, "json");
	        });
		// ---
	// ---

	// Catalog
		// Menu
			$(document).on('click', '.all-l_a2 .list-products li a', function(e){
				var $this = $(this);
				var anchor = $this.attr('href');

				$("html, body").animate({ scrollTop: $(anchor).offset().top - 100 });

				e.preventDefault();
			});

			$('.qwe.vertical').mousewheel(function(e) {
				if( app.scroll.menu == false ){

					//app.scroll.menu = true;
					
					var $menuList = $('.products-grid').find('.list-products');
					var $scroll = parseInt($menuList.attr('data-scroll'));
					var $count = parseInt($menuList.find('li').length)-1;
					var $size = parseInt($menuList.parents('.all-l_a2').attr('data-size'));

				    if( e.deltaY==-1 ){
				    	setTimeout(function(){ app.scroll.menu = false; },250);

				    	if( $scroll > 1 - ($count*100 - $size*100) ){
					    	$menuList.attr('data-scroll',($scroll - 100));

					    	$menuList.css({
								'-webkit-transform': 'translateY('+($scroll - 100)+'px)',
								'-moz-transform': 'translateY('+($scroll - 100)+'px)',
								'-ms-transform': 'translateY('+($scroll - 100)+'px)',
								'-o-transform': 'translateY('+($scroll - 100)+'px)',
								'transform': 'translateY('+($scroll - 100)+'px)'
							});
						}

				    	e.preventDefault();
        				return;
				    }
				    else{
				    	setTimeout(function(){ app.scroll.menu = false; },250);

				    	if( $scroll < 0 ){
					    	$menuList.attr('data-scroll',($scroll + 100));

					    	$menuList.css({
								'-webkit-transform': 'translateY('+($scroll + 100)+'px)',
								'-moz-transform': 'translateY('+($scroll + 100)+'px)',
								'-ms-transform': 'translateY('+($scroll + 100)+'px)',
								'-o-transform': 'translateY('+($scroll + 100)+'px)',
								'transform': 'translateY('+($scroll + 100)+'px)'
							});
						}

				    	e.preventDefault();
        				return;
				    }
					
				}
			});
		// ---

		// Show more
			$(document).on('click', '.show-more', function(e){
				// ---
		            var $button = $(this);
		            var dataMode = $(this).data('mode');
		            var dataTarget = $(this).data('target');
		            var $buttons = $('.show-more[data-mode="' + dataMode + '"][data-target="' + dataTarget + '"]');
		            var pElement = $(this).parent().find('.list-letter');


		            if(pElement.hasClass('ll-opened')) {
		            	// ---
			                pElement.removeClass('ll-opened');

			                $buttons.each(function() {
			                    $(this).html($(this).attr('data-default'));
			                });

			                $("html, body").animate({ scrollTop: $('#'+$button.parents('.subcategory').attr('id')).offset().top - 100 });
			                
			                return;
			            // ---
		            }
		            else{
		            	pElement.addClass('ll-opened');
		            }

		            if(pElement.hasClass('all-loaded')) { return; }

		            $buttons.html('загрузка');
					$buttons.attr('disabled','true');

		            var nInclude = [];
		            
		            pElement.find('li[data-product]').each(function(i, item, arr){
		                nInclude[nInclude.length] = $(item).data('product');
		            });
		            
		            $.post('/?route=ajax/index/ajaxShowMore', { mode: $(this).data('mode'), target: $(this).data('target'), parent: $(this).data('parent'), not_include: nInclude }, function(products){
		                // ---
		                	// Buttons
				                $buttons.html('скрыть');
				                $buttons.removeAttr('disabled');
			            	// ---

			                if(!pElement.hasClass('all-loaded')) {
			                    pElement.addClass('all-loaded');

			                    pElement.append(products);
			                    lazyLoadImages(pElement);
			                    catalogProductDymanmicShow(pElement);
			                    initTooltips();
			                }
		                // ---
		            });
		        // ---
	        });
		// ---

		// Products modal
			$(document).on('click', '#category .tabs__block .n-p_list', function(e){
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

		// Product modal
			app.modals.product = $('[data-remodal-id="modal-product"]').remodal();

			$(document).on('click', '[data-product] a.p-o_thumb', function(e){
				var $this = $(this);
				var href = $(this).attr('href');
				var product_id = $(this).parents('[data-product]').attr('data-product');

				// Show product modal
					$.post('/?route=ajax/index/getViewProduct', {product_id:product_id}, function(data){
						// ---
							console.log(data);
							$('[data-remodal-id="modal-product"]').find('.body').html(data.html);


							app.modals.product.open();
							e.preventDefault();
						// ---
					},'json');
					
					e.preventDefault();
				// ---
			});
		// ---
	// ---

	// Account
		app.modals.auth = $('[data-remodal-id="modal"]').remodal();
		app.modals.recovery = $('[data-remodal-id="modal-recovery"]').remodal();
		app.modals.phone = $('[data-remodal-id="modal-phone"]').remodal();

		app.modals.repeat = $('[data-remodal-id="modal-repeat"]').remodal();
		app.modals.orderAbout = $('[data-remodal-id="modal-order-about"]').remodal();

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
	                    if( data.result == true ) {
	                    	console.log('Auth success');
	                    	console.log(data.redirect);

	                    	if( typeof data.redirect == 'undefined' ) {
	                        	window.location.href = '/account';
	                    	}
	                    	else {
	                        	window.location.href = data.redirect;
	                    	}
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

				// Validation
            		var validation = true;

					$form.find('.input-group.required').each(function(key,val){
						var $this = $(this).find('input');
						var $name = $this.attr('name');
						var $value = $this.val();

						console.log($name + ' ' + $value);

						if( $name == 'phone' || $name == 'telephone' ) {
							$value = $value.replace(/\D/g,'');
							if( $value.length < 11 ) { $this.attr('data-style','error'); validation = false; return false; }
							else{ $this.attr('data-style','default'); }
						}
						else {
							if( $value.length < 3 ) { $this.attr('data-style','error'); validation = false; return false; }
							else{ $this.attr('data-style','default'); }
						}
					});

					if( !validation ){ return false; }
				// ---

	            if(phone != '' ) {
	                $.post('/?route=ajax/index/sendCallRequest',{ phone: phone, roistat_visit: app.customer.roistat_visit }, function(data){
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

					$.post('.?route=ajax/index/rbsPostPayment', {order_id:$order_id, action: 'payment'}, function(data){
						// ---
							console.log(data.redirect);
							document.location = data.redirect;
						// ---
					},'json');

					e.preventDefault();
				// ---
			});
		// ---

		// Surcharge
			$('[data-action="rbs-surcharge"]').on('click', function(e){
				// ---
					$order_id = $(this).attr('data-order-id');

					$.post('.?route=ajax/index/rbsPostPayment', {order_id:$order_id, action:'surcharge'}, function(data){
						// ---
							console.log(data.redirect);
							document.location = data.redirect;
						// ---
					},'json');

					e.preventDefault();
				// ---
			});
		// ---

		// About order
			$('[data-action="order-about"]').on('click', function(e){
				// ---
					$order_id = $(this).parents('[data-order-id]').attr('data-order-id');

					$.post('.?route=ajax/index/getAboutOrderModal', {order_id:$order_id}, function(data){
						// ---
							$('[data-remodal-id="modal-order-about"]').find('.body').html(data.html);
							app.modals.orderAbout.open();
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

					$.post('.?route=ajax/index/getReorderModal', {order_id:$order_id}, function(data){
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

		// Profile
			$('.account-edit-btn').click(function(){
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
	            var vegan_card = $('input[name="vegan_card"]').val();
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
	                vegan_card: vegan_card,
	                newsletter: newsletter
	            }, function(msg){
	                if(msg.status == 'success') {
	                    msg.dadata.forEach(function(item, i, arr){
	                        $('.f-p_input[data-target-id="'+item.id+'"]').val(item.value);
	                    });
	                    $('.f-p_input').removeClass('input-error_2');
	                    $('.account-edit-btn').html('Изменения сохранены').addClass('changes-applied');
	                    setTimeout(function(){
	                        $('.account-edit-btn').html('Сохранить изменения').removeClass('changes-applied');
	                    }, 3000);
	                } else {
	                    
	                }
	            }, "json");
	        });

	        $('.account-edit-vegan-card').click(function(){
	            var vegan_card = $('input[name="vegan_card"]').val();

	            $.post('/?route=ajax/index/ajaxSetCustomerVeganCard', {
	                vegan_card: vegan_card
	            }, function(msg){
	                if(msg.status == 'success') {
	                    $('.account-edit-vegan-card').html('Изменения сохранены').addClass('changes-applied');

	                    setTimeout(function(){
	                        $('.account-edit-vegan-card').html('Сохранить изменения').removeClass('changes-applied');
	                    }, 3000);
	                } else {
	                    
	                }
	            }, "json");
	        });
		// ---

		// Testimonials
			// if( $('#account.testimonials').length > 0 ){
			// 	accountTestimonialsLoadItems();
			// }


			$('#account.testimonials').on('submit', 'form.post', function(){
				var form = $(this);
				var text = $(this).find('[name="text"]').val();
				var rating = $(this).find('[name="rating"]').val();

				if( $(this).find('[name="order_id"]').val() == '' ) { var order_id = 0; }
				else { var order_id = $(this).find('[name="order_id"]').val(); }
				
				if( $(this).find('[name="customer_id"]').val() == '' ) { var customer_id = 0; }
				else { var customer_id = $(this).find('[name="customer_id"]').val(); }
				
				$.post('/?route=account/testimonials/addItem', {customer_id:customer_id, text:text, rating:rating, order_id:order_id}, function(data){
					// ---
						form.find('.form-control').val('');

						accountTestimonialsLoadItems();
					// ---
				}, 'json');

				return false;
			});

			$('#account.testimonials').on('submit', 'form.answer', function(){
				var form = $(this);
				var text = $(this).find('[name="text"]').val();
				var parent_id = $(this).find('[name="parent_id"]').val();
				var rating = $(this).find('[name="rating"]').val();

				

				$.post('/?route=account/testimonials/addItem', {text:text, parent_id:parent_id, rating:rating}, function(data){
					// ---
						form.find('.form-control').val('');

						accountTestimonialsLoadItems();
					// ---
				}, 'json');

				return false;
			});

			$('#account.testimonials').on('click', '[data-action="testimonial-answer"]', function(){
				// ---
					var $this = $(this);
					var parent_id = $this.parents('.item').attr('data-id');

					$('#account.testimonials').find('form.answer [name="parent_id"]').val(parent_id);
					$('#account.testimonials').find('form.answer').attr('data-view','visible');

					$("html, body").animate({ scrollTop: $('#form-testimonials-answer').offset().top - 50 });
				// ---
			});

			$('#account.testimonials form.answer').on('click', '[data-action="close"]', function(){
				// ---
					$('#account.testimonials').find('form.answer').attr('data-view','none');
				// ---
			});


			$('#account.testimonials').on('click', '.item [data-action="delete"]', function(){
				// ---
					var $this = $(this);
					var testimonials_id = $this.parents('.item').attr('data-id');


					$.post('/?route=account/testimonials/deleteItem', {testimonials_id:testimonials_id}, function(data){
					// ---
						accountTestimonialsLoadItems();
					// ---
				}, 'json');

				return true;
				// ---
			});

		// ---
	// ---

	// Blog 
		// Blog modal
			app.modals.blog = $('[data-remodal-id="modal-blog"]').remodal();

			$(document).on('click', '.blog .featured a.post-featured', function(e){
				var $this = $(this);
				var href = $(this).attr('href');
				var post_id = $(this).attr('data-id');

				// Show blog modal
					$.post('/?route=extension/module/iblog/postAjax', {post_id:post_id}, function(data){
						// ---
							console.log(data);
							$('[data-remodal-id="modal-blog"]').find('.body').html(data.html);


							app.modals.blog.open();
							e.preventDefault();
						// ---
					},'json');
					
					e.preventDefault();
				// ---
			});
		// ---

		// Search
			$('.blog-search button[id=\'iblog-search-button\']').on('click', function(e) {
				url = $('base').attr('href') + '/blog/search';
				var value = $('.blog-search input[name=\'search\']').val();
				if (value) {
					url += '&search=' + encodeURIComponent(value);
				}

				location = url;
			});

			$('.blog-search input[name=\'search\']').on('keydown', function(e) {
				if (e.keyCode == 13) {
					$('.blog-search button[id=\'iblog-search-button\']').trigger('click');
				}
			});
		// ---
	// ---

	// Components
		// Form
			// Select
				$(document).on('click', '.form-select .select', function(e){
					// ---
						$this = $(this);
						$options = $this.parents('.options');
						$current = $options.find('.current');
						$original = $this.find('select');

						$style = $this.attr('data-style');

						if( $style == 'active' ){
							$this.attr('data-style', 'default');
						}
						else {
							$this.attr('data-style', 'active');
						}


						// Custom
							$this.parents('[data-product]').css('z-index', '1000');
						// ---
					// ---
				});

				$(document).on('focusout', '.form-select .select', function(e){
					$this = $(this);
					$this.attr('data-style', 'default');
					$this.parents('[data-product]').css('z-index', '0');
					console.log('Blur focus');
					e.stopPropagation();
				});

				$(document).on('click', '.form-select .options .option', function(e){
					// ---
						$this = $(this);
						$select = $this.parents('.select');

						$options = $select.find('.options');
						$current = $select.find('.current');
						$original = $select.find('select');

						$value = $(this).attr('data-value');
						$index = $(this).attr('data-index');

						// Change style
							$select.find('.option').attr('data-style', 'default');
							$this.attr('data-style', 'active');
						// ---

						// Set value
							$select.attr('data-value', $value);
							$select.attr('data-index', $index);
							$current.html($this.html());

							$original.val($value);
						// ---

						// Close options
							$select.attr('data-style', 'default');
						// ---

						// Customisation
							var custom = $this.parents('.form-select').attr('data-custom');

							if( custom == 'product-grid' ){
								// ---
									var currencyStr = ' руб';
									
									var quantity = parseFloat($(this).parents('.p-o_block').find('.form-select .select').attr('data-value'));
					                var price = parseFloat($(this).parents('.p-o_block').find('meta[itemprop="price"]').attr('content'));
					                var compPrice = $(this).parents('.p-o_block').find('.composite_price').val();
					                var mtpl = 1;

					                if(typeof(compPrice) != 'undefined') {
					                    var cpFormat = JSON.parse(compPrice);
					                    if(cpFormat[quantity]) {
					                        mtpl = cpFormat[quantity];
					                    }
					                }
					                
					                var totalPrice = Math.round(mtpl * quantity * price);
					                if(totalPrice > 999) currencyStr = ' р';
					                $(this).parents('.p-o_block').find('.p-o_price').html(totalPrice + currencyStr);

					                // Sale price
					                	var saleprice = parseFloat($(this).parents('.p-o_block').find('meta[itemprop="baseprice"]').attr('content'));

					                	var totalPrice = Math.round(mtpl * quantity * saleprice);
					                	if(totalPrice > 999) currencyStr = ' р';
					                	$(this).parents('.p-o_block').find('.product-sale span.price').html(totalPrice + currencyStr);
					                // ---
								// ---
							}

							if( custom == 'product-page' ){
								// ---
									var currencyStr = ' руб';
									
									var quantity = parseFloat($(this).parents('.c-p_right').find('.form-select .select').attr('data-value'));
					                var price = parseFloat($(this).parents('.c-p_right').find('meta[itemprop="price"]').attr('content'));
					                var compPrice = $(this).parents('.c-p_right').find('.composite_price').val();
					                var mtpl = 1;

					                if(typeof(compPrice) != 'undefined') {
					                    var cpFormat = JSON.parse(compPrice);
					                    if(cpFormat[quantity]) {
					                        mtpl = cpFormat[quantity];
					                    }
					                }
					                
					                var totalPrice = Math.round(mtpl * quantity * price);
					                if(totalPrice > 999) currencyStr = ' р';
					                $(this).parents('.c-p_right').find('.c-p_price .c-p_price_shadow').html(totalPrice + currencyStr);

					                // Sale price
					                	var saleprice = parseFloat($(this).parents('.c-p_right').find('meta[itemprop="baseprice"]').attr('content'));

					                	var totalPrice = Math.round(mtpl * quantity * saleprice);
					                	if(totalPrice > 999) currencyStr = ' р';
					                	$(this).parents('.c-p_right').find('.product-sale span.price').html(totalPrice + currencyStr);
					                // ---
								// ---
							}
						// ---

						e.stopPropagation();
					// ---
				});

				$(document).on('change', '.form-select .select select', function(e){
					// ---
						$this = $(this);
						$value = $this.val();
						$select = $this.parents('.select');
						
						$select.find('.options .option[data-value="'+$value+'"]').trigger('click');
						
					// ---
				});
			// ---

			// Checkbox
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

		// Accordion
			$(".b-accordion li").find(".b-accordion_text").hide().prev().on('click', function() {
				$(this).parents(".b-accordion").find(".b-accordion_text").not(this).slideUp().prev().removeClass("active");
				$(this).next().not(":visible").slideDown().prev().addClass("active");
			});
		// ---

		// Buttons
			// $(document).on('mouseenter', '.btn-toggle button', function(){
			// 	var $this = $(this);
			// 	var value = parseInt($this.attr('data-value'));
			// 	var count = 1;

			// 	$this.parents('.btn-toggle').find('button').each(function(key, val){

			// 		if( count <= value ){
			// 			$(this).addClass('active');
			// 			$(this).find('i').removeClass('fa-star-o').addClass('fa-star');
			// 		}
			// 		else {
			// 			$(this).removeClass('active');
			// 			$(this).find('i').addClass('fa-star-o').removeClass('fa-star');
			// 		}

			// 		count++;
			// 	});
			// });

			// $(document).on('mouseleave', '.btn-toggle', function(){
			// 	if( $(this).attr('data-set') !== 'true' ){
			// 		$(this).find('button').each(function(key, val){
			// 			$(this).removeClass('active');
			// 			$(this).find('i').addClass('fa-star-o').removeClass('fa-star');
			// 		});
			// 	}
			// });
			
			$(document).on('click', '.btn-toggle button', function(){
				var $this = $(this);
				var value = $this.attr('data-value');

				var count = 1;

				$this.parents('.btn-toggle').attr('data-set', 'true');

				$this.parents('.btn-toggle').find('button').each(function(key, val){

					if( count <= value ){
						$(this).addClass('active');
						$(this).find('i').removeClass('fa-star-o').addClass('fa-star');
					}
					else {
						$(this).removeClass('active');
						$(this).find('i').addClass('fa-star-o').removeClass('fa-star');
					}

					count++;
				});

				$this.parents('.btn-toggle').find('input').val(value);
			});
		// ---
	// ---

	// Modals
		$(document).on('click', '[data-action="modal"]', function(){
			// ---
				$('.modal[data-marker="'+$(this).attr('data-target')+'"]').modal('show');
			// ---
		});

		// Coupon
			$('.modal form.coupon-form').submit(function(){
				console.log('Modal coupon - Check customer phone');

				var form = $(this);
				var modal = $(this).parents('.modal');

				var phone = form.find('input[name="phone"]').val();

				$.post('/?route=ajax/index/createCustomerOneOffCoupon', {phone:phone}, function(data){
					// ---
						form.attr('data-visible', 'false');

						modal.find('.message label').text(data.message);
						modal.find('.message').attr('data-visible', 'true');

						if( data.sended == true ) {
							$('.adversting[data-target="modal-oneoff-coupon"]').remove();
						}

						setTimeout(function(){
							if( data.sended == true ) {
								$('.modal[data-marker="modal-oneoff-coupon"]').modal('hide');
							}

							form.attr('data-visible', 'true');
							modal.find('.message label').text('...');
							modal.find('.message').attr('data-visible', 'false');
						},5000);

						return false;
					// ---
				}, 'json');
				
				return false;
			});
		// ---
	// ---
});

// General
	function watchEnv(){
		app.width = $(window).width();
		app.height = $(window).height();
		
		// Get current page
			app.page = $('[data-page]').attr('data-page');
		// ---

		// Get size
			if (app.width < 576) { app.size = 'xs'; }
			else if (app.width >= 576 && app.width < 768) { app.size = 'sm'; }
			else if (app.width >= 768 && app.width < 992) { app.size = 'md'; }
			else if (app.width >= 992 && app.width < 1200) { app.size = 'lg'; }
			else if (app.width >= 1200 && app.width < 1920) { app.size = 'xl'; }
			else if (app.width >= 1920) { app.size = 'xxl'; }
		// ---

		// Product grid
			catalogMenuInit();

			$('.slider-profitable_offer .box-p_o').each(function(key,val){
				$(this).find('a').attr('target','_blank');
			});

			$('.tabs__block .box-p_o').each(function(key,val){
				$(this).find('a').attr('target','_blank');
			});

			$('.remodal .modal-product').each(function(key,val){
				$(this).find('a').attr('target','_blank');
			});
		// ---

		// Product modal
			if( app.size == 'xs' || app.size == 'sm' ){
				if( app.modals.product != null && app.modals.product.state == 'opened' ){
					app.modals.product.close();
				}
			}
		// ---
	}

	function initStart(){
		$('[name="telephone"]').inputmask("+7 (g99) 999-99-99", {
		    definitions: {'g': {validator: "[1234569]", cardinality: 1}}
		});
		$('[name="phone"]').inputmask("+7 (g99) 999-99-99", {
		    definitions: {'g': {validator: "[1234569]", cardinality: 1}}
		});

		// Handlers
			watchEnv();
			$(window).resize(watchEnv);
	    // ---

	    // Roistat
	    	checkRoistat();
	    // ---
	}

	function initAuth(){
		$.post('.?route=ajax/index/isCustomerLogged', {}, function(data){
			// ---
				if( data.status == true ){
					$('a[data-marker="auth-button"]').removeAttr('data-remodal-target');
					$('a[data-marker="auth-button"]').attr('href','/account');

					if( window.location.pathname.indexOf('/account')>=0 ){
						$('a[data-marker="auth-button"').css('display','none');
						$('a[data-marker="logout-button"]').css('display','block');
					}
				}
				else {
					$('a[data-marker="auth-button"]').attr('data-remodal-target','modal');
					$('a[data-marker="auth-button"]').attr('href','#auth');

					$('a[data-marker="logout-button"]').css('display','none');
				}
			// ---
		},'json');
	}

	function initParams(){
		// ---
			// Payments
				var payment = getUrlParam('payment');

				if( payment == 'rbs-success' ){
					var order_id = getUrlParam('order_id');
					var $modal = $('[data-remodal-id="modal-payment"]');
					
					$modal.find('.message-success').html('Заказ №'+order_id+' успешно оплачен!');
					$modal.find('.success .message-success').show();
	                $modal.find('.success').fadeIn('fast');

					var modalPayment = $('[data-remodal-id="modal-payment"]').remodal();
					modalPayment.open();
				}
			// ---
		// ---
	}
// ---

// Cart
	function initSuggestionsDadata(){
		// ---
			$('.modal-basket input[name="address"]').suggestions({
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
		            
		            $('nav .cart .counter').html(totalPositions);

		            $.get('/?route=ajax/index/ajaxGetOrderPrice', {}, function(data){
		                if(data.status == 'success') {
		                    $('#form-customer').find('.o-i_price').html(data.price + ' <span>руб</span>');
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

	function ChangeCartQuantity(cart_id, quantity) {
		// ---
	        $.post('/?route=ajax/index/ajaxChangeCartQuantity', { cart_id: cart_id, quantity: quantity }, function(data){
	            if(data.status == 'success') {
	                LoadCart();
	            }
	        }, "json");
		// ---
    }

    function crateYaMap(){
    	app.map = new ymaps.Map("ya-map", {
            center: [55.73, 37.75],
            zoom: 9
        });
		
		var json = { "type": "Polygon", "coordinates": [[[55.909881156755596,37.54508775121138],[55.90855536918938,37.54607480412886],[55.90932674203806,37.555044111074665],[55.909943147120636,37.56128222170017],[55.91060602277034,37.5673976582541],[55.91112426307951,37.57196814241549],[55.91129530017153,37.576472451707694],[55.910921687419545,37.58346765281851],[55.91043960110756,37.58709399940665],[55.91162070188107,37.58827417137322],[55.912066618288385,37.58926122429069],[55.91182558303203,37.59069888832268],[55.90865582941906,37.59398191215689],[55.906775548437764,37.60076253654656],[55.90424425638983,37.61016099693474],[55.902749510799005,37.61608331443962],[55.901519918643594,37.62127607109245],[55.90041084116433,37.62651174308952],[55.899337898951465,37.63288467170891],[55.89762595191614,37.64378516914541],[55.89664938476749,37.652990510484535],[55.89602243976006,37.66060798408683],[55.89562456553605,37.668075253984284],[55.895879894503956,37.66960365271346],[55.89709760979707,37.67155630087632],[55.89744724372948,37.67280084585923],[55.89653095497987,37.67460329031722],[55.89551818949873,37.67679197287339],[55.89545790500197,37.67943126654403],[55.895506132606904,37.680613180160385],[55.89531623606431,37.68188991165146],[55.89510222455638,37.68361725425705],[55.89504193941057,37.6847062311171],[55.89499069696267,37.68635310745224],[55.894960554314515,37.68788733100876],[55.8948640976824,37.69194595762263],[55.894755583683384,37.69402198740016],[55.894472369880965,37.69664939774745],[55.894312611434536,37.69788321389431],[55.894186009932376,37.698693241016805],[55.89403227897918,37.69963201417201],[55.89375264470145,37.70095234149241],[55.893297473625985,37.7027547859504],[55.893053306799686,37.7036721014335],[55.89268554644551,37.70477717154765],[55.89221830488141,37.70609681838295],[55.891943986234075,37.70685320132516],[55.891597317026,37.70764177077555],[55.891139105996196,37.708703925545436],[55.8906447143175,37.7098465465858],[55.89023472617658,37.71075849765084],[55.88973335093811,37.711814618110665],[55.889226882422676,37.71283922195435],[55.88880783500207,37.71371898651124],[55.88818052166454,37.71488760706813],[55.88736048874429,37.71648083922296],[55.88643793091026,37.71826182600884],[55.8859973919465,37.719135860742604],[55.88550594948914,37.720096091569914],[55.88508686172091,37.720959762872724],[55.88456827200488,37.722472528757116],[55.884315004731,37.72341666633037],[55.883820525746586,37.72444663459208],[55.883838616552616,37.72588429862407],[55.883904949435646,37.7272951405659],[55.884239438051004,37.72794104802062],[55.884549992347196,37.72879935490538],[55.88484546887563,37.729604017609844],[55.884167075526456,37.73055351960113],[55.883606261367774,37.73000634896208],[55.88288563337984,37.72955573784758],[55.88221739042094,37.729358088886144],[55.88187666452803,37.72939027539433],[55.88155704281879,37.729374182140255],[55.88105649785086,37.7295458435172],[55.88057102334702,37.730221760188954],[55.880043057601235,37.73112930447486],[55.87942488956888,37.732341662949594],[55.87901779790549,37.73311950356393],[55.878725292359945,37.733586207932504],[55.878300099939786,37.73443915039923],[55.87779951279901,37.73537255913644],[55.877018463289986,37.73688532502082],[55.87608661138014,37.73871459156899],[55.87531155886462,37.74023272187141],[55.8744262133742,37.741983004444734],[55.873596840827375,37.743570872181536],[55.872675389397195,37.74544656929158],[55.87187010784123,37.74701834377431],[55.87138204091312,37.74795232323279],[55.87072754053258,37.74923441914191],[55.86990226627862,37.75085438709901],[55.869021519811845,37.752458348089924],[55.868215230568005,37.75401507489582],[55.8673917576358,37.75565122239492],[55.866930243160894,37.75656853787801],[55.86609769331757,37.75821541421313],[55.86542500408649,37.75949751012226],[55.8647100704203,37.76088689439199],[55.863955906661616,37.76230846516987],[55.86337066545234,37.763477908300366],[55.86276128117508,37.76463125817678],[55.86204629827395,37.7660045491924],[55.8617506474739,37.76660536401174],[55.861165099507325,37.76775172053136],[55.860540595733596,37.768996265514275],[55.85985272483703,37.770294454677476],[55.85911958581185,37.771775034053704],[55.858528237216476,37.772933748348144],[55.85749700456981,37.77490024258742],[55.856700458699436,37.77642910172591],[55.85552792751572,37.778710841396],[55.85474944552576,37.78023970053449],[55.85374161999706,37.782208441951425],[55.85299025993404,37.78367829249157],[55.852203019756764,37.785160506578436],[55.8513550645922,37.78682347616768],[55.849959336757536,37.78954509922565],[55.849446311448574,37.790532152143115],[55.84893952062386,37.79156228850568],[55.84800095619737,37.79332718203749],[55.847141939021775,37.79503778572823],[55.84618522258633,37.796824136932145],[55.84530393604987,37.79857293720987],[55.84445281151296,37.80026272888922],[55.84378578239494,37.801518002708214],[55.843746545030044,37.80160383339669],[55.842967826022836,37.80313269253518],[55.84218305458646,37.804650822837594],[55.84106623703635,37.806812683303605],[55.83979242201501,37.80927495117927],[55.83906494013172,37.81069115753913],[55.838016386708304,37.812742516845596],[55.83696284358394,37.81478636011495],[55.83540931817587,37.817900850434604],[55.83373311990825,37.82116654679604],[55.83272474705995,37.823146017049034],[55.83152311878067,37.82534542844125],[55.830739186914684,37.826911472646394],[55.83049764420261,37.82907869753043],[55.83053085641486,37.829937004415214],[55.83038894949072,37.83019449648064],[55.82994510958444,37.83008720812003],[55.829649213487826,37.830076479283974],[55.82915705476911,37.83009257253806],[55.82870716188011,37.83043589529198],[55.8281585980453,37.83130343250223],[55.82768151795239,37.8320169001002],[55.82747921006279,37.832226112403355],[55.82708666935238,37.83274109653422],[55.826543888075065,37.83343972801301],[55.82603357205332,37.83401908516022],[55.825529288670154,37.834550162545185],[55.824759262455444,37.835268994561154],[55.82342796744785,37.83630800270418],[55.82235590783884,37.837000012630014],[55.82150045992129,37.83748075908706],[55.82032264408886,37.837985014381864],[55.81992339044126,37.83811775561905],[55.819005263805614,37.838321603504184],[55.81812335852557,37.838471807209025],[55.81741466988425,37.83860296188363],[55.81695557956219,37.838672699318],[55.8162155870016,37.83911794601447],[55.81548464170128,37.840652169571015],[55.81525612622525,37.84138981639878],[55.81506583542833,37.8427631074144],[55.81521988043122,37.84398619472521],[55.81532861771163,37.84435633956926],[55.815201757521486,37.84452263652818],[55.81493595387164,37.84392182170884],[55.81432882733339,37.842891853447114],[55.814041915597045,37.84260566427987],[55.81367642416963,37.84223551943582],[55.81329884676111,37.841897561099955],[55.81287293502913,37.84132356837075],[55.81238056315107,37.8403204221992],[55.81217116601753,37.84001172985858],[55.81165763989616,37.839598669670295],[55.81116525257817,37.839448465965454],[55.810620128926,37.839515959045656],[55.81031200173881,37.83953741671776],[55.8095688613857,37.83961788298821],[55.80739949417252,37.83986871902024],[55.8052144918559,37.83990657621626],[55.803974039000565,37.84000870089036],[55.80274933908086,37.8401934885171],[55.80213597862076,37.84022567502529],[55.80095342010053,37.84034905663997],[55.79972058926767,37.84052071801692],[55.798326148040886,37.840678740693015],[55.79715064967098,37.840828944397856],[55.79582793617883,37.84093251459755],[55.794443837499365,37.841109540392544],[55.79407263798709,37.84112796170614],[55.79365558147697,37.84117087705038],[55.79324456487615,37.841229885648694],[55.79260078094897,37.84129425866506],[55.790757682359235,37.84148848988439],[55.78950034745293,37.84167088009742],[55.7862336272918,37.84201880751546],[55.784258721855394,37.84221093241848],[55.78256400931106,37.84240562474491],[55.78119778820757,37.842566524554286],[55.78083803340482,37.84258261780839],[55.78010642091195,37.84268990616899],[55.77912973450708,37.84299709622237],[55.77883345029763,37.84332969014024],[55.778404136510915,37.84443476025436],[55.778234828359516,37.844987295311434],[55.77805851717888,37.84562189056211],[55.77807363407682,37.84668404533199],[55.77820061578709,37.847531623380696],[55.77805851717888,37.847660369413404],[55.777583843587706,37.847955412405064],[55.77730871165071,37.8471722073727],[55.777139398719925,37.846512383955044],[55.776976131979914,37.84571845008663],[55.77684612278827,37.84558433963589],[55.77654679764476,37.845407313840894],[55.776144971645834,37.8451177324667],[55.775558402535644,37.84432916301633],[55.77508067475097,37.843562051238074],[55.77460294108562,37.843331381262786],[55.773801852288216,37.84336594596285],[55.77290077570754,37.84348932757753],[55.77145185503226,37.84436763249376],[55.7694560330914,37.8456336351488],[55.76882026177047,37.84590227915028],[55.76862067066335,37.8459773810027],[55.768548091824506,37.84617050005178],[55.7682910406789,37.84624023748617],[55.76800979453434,37.84665329767446],[55.767885803651396,37.84643335653522],[55.7680793502221,37.845972016584675],[55.76807632606437,37.84540338827352],[55.76796745622858,37.84516198946218],[55.76780717508139,37.844909861814784],[55.7677134254244,37.84468992067555],[55.76751080442458,37.84433050466755],[55.76714764305708,37.84364106367466],[55.76676356392038,37.843431851371506],[55.766315970840715,37.8433245630109],[55.76520904955301,37.84324946115848],[55.7647947071136,37.843238732322426],[55.76391761728958,37.843179723724084],[55.762664639894474,37.84312071512577],[55.76199317745366,37.84307243536351],[55.76077236591496,37.84301838011187],[55.75971974670789,37.84297546476763],[55.75952806354864,37.8429502917191],[55.75873555163529,37.84290737637486],[55.758544983381356,37.842891283120785],[55.75799149870487,37.84287518986668],[55.75756800431207,37.84285373219458],[55.757456080022415,37.84284300335852],[55.75650017294735,37.84280545243231],[55.75585316461673,37.84279509196813],[55.755650481716806,37.84283264289434],[55.75550225026959,37.84295066009099],[55.75547804916325,37.843352991443226],[55.755344942808875,37.84336372027928],[55.75516948373563,37.84294529567297],[55.75491536918978,37.84279509196813],[55.754585622836245,37.84271462569768],[55.754407135110114,37.8426985324436],[55.75387469227444,37.84266098151739],[55.753200052516505,37.84261270175513],[55.75269482134704,37.842585879664966],[55.75245412768619,37.8425902573483],[55.751806692090184,37.842563435258164],[55.75046032751274,37.84248726771192],[55.749362406917925,37.84245508120376],[55.74860599566038,37.84240143702345],[55.74775814049558,37.84237582000955],[55.74729519943742,37.842408006517715],[55.746832252862696,37.842542116968474],[55.74648125701162,37.84282106670602],[55.74606803405047,37.8435024291876],[55.745257091849986,37.84498300856383],[55.7446458481088,37.84649577444824],[55.74448244468768,37.847944167316285],[55.744639796142486,37.850047019183954],[55.744730575538114,37.85124864882263],[55.7444703407032,37.85129156416687],[55.744191948019946,37.85020795172485],[55.74393170957741,37.849038508594354],[55.74354437471426,37.84741845434934],[55.74295731295131,37.84535851782591],[55.74242471316292,37.84431782072812],[55.74179526763738,37.84334149664671],[55.74102055145469,37.84237590140135],[55.740348714760266,37.84196820563108],[55.73909055987339,37.84182564480648],[55.73663303273927,37.84151450856074],[55.7344662299513,37.84126265402554],[55.733085995736985,37.84143431540251],[55.73187522361698,37.841992214877614],[55.731463552504984,37.842861250598425],[55.73131220173539,37.8443311011386],[55.730870254115615,37.84445984717131],[55.730731009304066,37.84312947149992],[55.7304404098299,37.84227116461514],[55.72977444450806,37.84111245032072],[55.729247203283514,37.84080306899812],[55.72857516305235,37.84067432296541],[55.72767909133933,37.8404597462442],[55.72642576720422,37.8402773560312],[55.72523539307153,37.84013737723856],[55.72403648677897,37.839997902369774],[55.722952595233316,37.83983696982888],[55.72220778073576,37.839815512156775],[55.72083922260084,37.83960093543559],[55.7197067951636,37.83963312194375],[55.71947667218779,37.83992280051738],[55.71904064597298,37.84083475158243],[55.71863489496526,37.84257282302407],[55.71840476564674,37.843366756892486],[55.71787788550024,37.8438710121873],[55.71707241124987,37.84439672515422],[55.716745371830406,37.844482555842696],[55.71670297763025,37.84401048705607],[55.71706635498932,37.84333457038431],[55.717623527012805,37.84263719604044],[55.718198859312714,37.841253176188765],[55.71816857887753,37.84025539443522],[55.71770831336012,37.839718952632246],[55.717241986162534,37.839332714534095],[55.71653945671106,37.83907522246867],[55.716110515643045,37.839085951304725],[55.71549275693123,37.8390644936326],[55.7147296296822,37.838946476435936],[55.71392409023188,37.83892501876383],[55.71303373774648,37.84013737723856],[55.71231901828501,37.84128536269692],[55.71200405299786,37.84202565238505],[55.71151342891284,37.84396757171183],[55.7105745631054,37.84337748572854],[55.710992512617935,37.84104932830363],[55.711265085967845,37.839053764796546],[55.711222685794404,37.83787359283],[55.710780509803165,37.837079658961585],[55.71024141172717,37.836747065043745],[55.7097083635878,37.83651103065043],[55.70928113624278,37.83631271943789],[55.708875283418976,37.83624834642155],[55.708626923632664,37.83731050119144],[55.70835433179831,37.83831901178104],[55.70786366168353,37.83896274194461],[55.706749030033265,37.84065789804202],[55.70639215536299,37.84130852342543],[55.70589540250241,37.84070770860609],[55.70635580780774,37.839495350131365],[55.70661629786999,37.83840100885329],[55.706216475198104,37.83654492021498],[55.70596204044958,37.835987020739886],[55.705865112488695,37.83548276544507],[55.705313830119195,37.834399153003055],[55.704899051747184,37.834069540986874],[55.70459008458955,37.83383887101159],[55.704253852836345,37.83367257405267],[55.704066046448325,37.833597472200246],[55.70364499334884,37.8333399801348],[55.702726888736656,37.83285939880944],[55.702378523789996,37.83263409325218],[55.70137279183767,37.832011820760734],[55.70078941577383,37.83162595854656],[55.70035318006505,37.83130409346477],[55.700201708189105,37.83123972044841],[55.70010173642805,37.83123972044841],[55.69972911214712,37.83104123698131],[55.699305135435964,37.830880497224214],[55.699080950813176,37.830842946298],[55.69898400571146,37.83103070092903],[55.69888100127631,37.83103070092903],[55.69874164190073,37.83071956468332],[55.698605311593944,37.83056936097848],[55.69839324127865,37.83052644563424],[55.698096340895944,37.83042452169166],[55.6976843121226,37.83033332658516],[55.697335902035725,37.83020994497048],[55.6971451455185,37.830202085270386],[55.696914889055755,37.83010016132781],[55.696248349511364,37.82990167786073],[55.69610595094717,37.82985339809844],[55.695954462541025,37.82983194042634],[55.69564375476928,37.82979195575297],[55.69533471412573,37.82977586249886],[55.694707535865646,37.829679302974334],[55.69431668053456,37.82960956553994],[55.69393188114229,37.82960956553994],[55.69327059240837,37.829583041700474],[55.69231494867032,37.82966649462001],[55.69144834263566,37.8297201388003],[55.690899886191495,37.8299186222674],[55.69065029057137,37.830012439693625],[55.68992000868619,37.83042549988192],[55.68934728841234,37.8310155858652],[55.68903292861211,37.83136763092225],[55.68840867918034,37.8320971917743],[55.687784419739764,37.83284284588046],[55.687374972688,37.83345976529153],[55.68597488340733,37.83505299744636],[55.68495170704877,37.83681682893145],[55.684466803166465,37.83797554322589],[55.68378792758694,37.8371172363411],[55.684388005715256,37.835604470456715],[55.68435769896079,37.8336947376381],[55.68373943602126,37.83287934609758],[55.683181778675724,37.8326862270485],[55.68155725306781,37.83317975350726],[55.68060554054342,37.83357672044144],[55.67856885302014,37.83429219311199],[55.67664707100385,37.83502175396404],[55.67429668850279,37.83592413575267],[55.67232012670018,37.83654640824411],[55.669045846629615,37.83783386857126],[55.66836567877045,37.83803358847408],[55.666904265381625,37.83861294562129],[55.66459378633528,37.83940687948971],[55.66344604714517,37.83979311758786],[55.66234227288494,37.84009352499752],[55.66052590253132,37.84013107592368],[55.659276479677736,37.83998087221887],[55.65853285564907,37.83985749060418],[55.6581507353142,37.84041539007928],[55.658014262858224,37.841311247890246],[55.65784139706136,37.84162774855401],[55.65726213941675,37.841509731357355],[55.65695582692105,37.84148827368522],[55.656831215293664,37.84163747419251],[55.65678269016588,37.84205589879882],[55.656452111125425,37.84197006811035],[55.65644604542877,37.84131560911072],[55.656315632722304,37.84112249006164],[55.655975951066615,37.84103665937317],[55.65572725369184,37.84089718450438],[55.65557560817245,37.84047339548004],[55.655469455957885,37.83994231809507],[55.65549371934669,37.839303952349546],[55.65536299224242,37.83898096557785],[55.6552052794163,37.83885758396317],[55.654962643053274,37.838610820733805],[55.65414070115079,37.83813338752915],[55.653373337533885,37.83761840339828],[55.65273877123274,37.837135719421454],[55.651625594064484,37.83616475975806],[55.65112111402938,37.835669342085026],[55.65086328583515,37.83548158745398],[55.650672188543986,37.83556741814245],[55.65053265659693,37.835476223035954],[55.65047502369047,37.83507389168372],[55.6503172910898,37.8348163996183],[55.64972882152551,37.83405465225807],[55.648960788233424,37.833071356190615],[55.64857250714376,37.83251882113354],[55.64785963726518,37.831451301945606],[55.6472134928816,37.830426698101924],[55.646669553045825,37.82960031178698],[55.645780695433366,37.82823774960742],[55.645486427527814,37.827744223148684],[55.64519215740174,37.827642299206104],[55.6451284491439,37.82770667222247],[55.645013167269916,37.82759401944384],[55.64501620100781,37.82729897645221],[55.644916087532785,37.82683763650164],[55.64445192169953,37.826150990993824],[55.643870918929245,37.82531311414121],[55.643294487564646,37.82466938397763],[55.64265736930204,37.823891543363324],[55.64206878413459,37.82315125367522],[55.641619754425626,37.82260408303617],[55.64132545512281,37.8228079309213],[55.641091834507556,37.823312186216114],[55.64087780108689,37.82370931681892],[55.640723063735244,37.82395071563026],[55.640568325769735,37.824159927933415],[55.64032863241513,37.82417602118749],[55.64001308448718,37.82379514750739],[55.63969146570302,37.82347864684363],[55.639469971979416,37.82360202845831],[55.63935770755703,37.823864884941756],[55.6392536974489,37.82372343386076],[55.639391752669184,37.82307165707015],[55.63932196657494,37.8224681600418],[55.63918542820336,37.82192367161176],[55.63923700942214,37.821336267837516],[55.639411474803616,37.82088565672299],[55.63942967984194,37.820274113067605],[55.639339665994925,37.81887847056788],[55.638992250912466,37.81778144708079],[55.638729791178314,37.817306696085154],[55.63831561702296,37.81673538556498],[55.63770461746232,37.81572801706377],[55.63729498392976,37.81514865991654],[55.63675814743192,37.81427642324071],[55.63629843613008,37.813544180179626],[55.635300048397916,37.81214587238971],[55.6347781120126,37.81135193852132],[55.63416816587699,37.81044535187427],[55.63334612058136,37.80915697246351],[55.632696701028785,37.8081967416362],[55.632226320303594,37.807488638456256],[55.63183549417707,37.806925229839365],[55.63129833742009,37.806061558536584],[55.63091898497252,37.805535845569665],[55.630767242960786,37.80523543815998],[55.63050017558685,37.804913573078196],[55.629643365840806,37.80359720015629],[55.62917901824489,37.80273889327153],[55.62853559937126,37.80184839987859],[55.628074273648636,37.80110274577244],[55.62774338106258,37.800611268179956],[55.6275218194969,37.80026258100801],[55.62689961933252,37.79937208761507],[55.62658396277318,37.79888928999239],[55.62646559090519,37.79873908628755],[55.62623491648979,37.79842795004181],[55.62585855004277,37.797912965910974],[55.62537594581976,37.797145854132715],[55.62492975924765,37.79640556444459],[55.62448544508548,37.795664413178045],[55.62411816785741,37.79500458976036],[55.62354447998218,37.79399071475274],[55.622888826370435,37.79279444953211],[55.62232726243716,37.79178593894249],[55.62070015567506,37.78880709916761],[55.619868384440046,37.78702611238173],[55.61952838525539,37.78621072084121],[55.61833836478768,37.78398985177686],[55.61806489361749,37.783866426505064],[55.61752451141829,37.78437068179985],[55.61739700441539,37.78551866725824],[55.61723306622828,37.78678466991326],[55.616978049902144,37.78731038288017],[55.61683232553928,37.78794338420769],[55.61687482853464,37.78836180881404],[55.616644097432165,37.788490554846746],[55.61634050179862,37.78794338420769],[55.61623120679259,37.78740694240473],[55.616595522289444,37.78692414478205],[55.61678375063078,37.78540065006158],[55.616820181817815,37.783866426505064],[55.61680803809258,37.78232147411249],[55.61639514918689,37.782053253211],[55.6160611917306,37.7810447426214],[55.6158911759282,37.77982165531062],[55.61555973621905,37.778982179926714],[55.61487358766792,37.777694719599566],[55.61415706569022,37.776289242075755],[55.61322882475955,37.774551895773705],[55.612539598979524,37.77333417288095],[55.611416082303045,37.77131884215209],[55.610517312039306,37.769655872562836],[55.609177488128694,37.767282784928845],[55.608302959091446,37.76574856137233],[55.606704416019504,37.762891532612834],[55.60610314133423,37.76168453855613],[55.60562029283983,37.76081013841727],[55.604508810810586,37.7587395776448],[55.603622029395034,37.757173167580106],[55.60336388718577,37.757017599457235],[55.603069299286126,37.75635777603958],[55.60295996712918,37.755960809105375],[55.60245599939144,37.75504368806719],[55.6007430668768,37.751320781954504],[55.5999000103902,37.749341019880106],[55.59911943023338,37.747501024495904],[55.59897480490151,37.74715621610558],[55.598737893342886,37.7466734184829],[55.59798202954557,37.74481529581477],[55.59742314474776,37.74348492014338],[55.596915889123984,37.742202824234255],[55.59601000687329,37.740114894201],[55.5958064905619,37.73961063890619],[55.59567283748221,37.73934778242274],[55.59482838245381,37.73729321031733],[55.59461304076705,37.736963186608],[55.59435180134142,37.73655549083774],[55.594139163310146,37.7363301852805],[55.593947788092564,37.736158523903526],[55.59378982688938,37.735815201149634],[55.59352207771021,37.73501816642961],[55.59309375368377,37.7339721049138],[55.59268972742645,37.73328545940599],[55.59208823698845,37.73266318691452],[55.59168723821921,37.73260417831621],[55.59107965648537,37.73243251693926],[55.59027459613647,37.73218575370989],[55.589600155454285,37.732400330431076],[55.58907930011803,37.73307584395356],[55.588921319233144,37.73329042067474],[55.588854480974206,37.73358009924834],[55.58875726148442,37.7337678538794],[55.588629660537045,37.73401461710876],[55.588447372746614,37.73385368456787],[55.58821647365812,37.73353181948608],[55.588605355547436,37.73245357146209],[55.58898373120311,37.73191686215862],[55.588998921676314,37.73124630990489],[55.58925108266875,37.730978089003415],[55.58956704113372,37.73031290116771],[55.58971894333258,37.728869872717695],[55.589749323701476,37.72827442231638],[55.58986780691457,37.727560954718435],[55.58986780691457,37.72661681714518],[55.589779704046784,37.72620375695689],[55.58966729665121,37.72580679002269],[55.58943944283038,37.72523816171153],[55.58924421864684,37.72474254849823],[55.58894344806655,37.7240183520642],[55.588536340765764,37.723197596105656],[55.587874022811384,37.72139515164764],[55.58747534110182,37.72040544370396],[55.58737507996098,37.7198207221387],[55.58721101572116,37.71924672940952],[55.58676439291224,37.71786807397587],[55.58604431681951,37.716011985337566],[55.58595299180427,37.71568745665904],[55.58562485069624,37.71476477675792],[55.58524505430751,37.713793817094526],[55.58467383358942,37.71218985610362],[55.583984103830836,37.710328403047285],[55.58333576048751,37.70869279810569],[55.58320206474988,37.70825291582724],[55.58290428623619,37.707480439630956],[55.58271589457636,37.70698154875417],[55.58254269499125,37.7064451069512],[55.582117289244145,37.70523274847647],[55.58161895090937,37.70403648325582],[55.58120401502863,37.70288614292696],[55.580578037815286,37.70121244450166],[55.580353169767214,37.70054189224793],[55.58005233076117,37.69978014488769],[55.57970687299054,37.698821194277585],[55.57920242621604,37.6974908186062],[55.57865542838185,37.69606924782831],[55.578214785671705,37.69490516911584],[55.57748382474499,37.692871253753076],[55.576678484316076,37.691953938269975],[55.57649006265129,37.69179837014711],[55.575944942938236,37.691547562463725],[55.57532192066958,37.691445638521174],[55.574182220097754,37.69133298574254],[55.57339824574615,37.691518858728536],[55.572769104186605,37.69178707963003],[55.57227064668204,37.691953376588955],[55.57199406081354,37.692200139818326],[55.57172659129983,37.69232888585103],[55.57166276307732,37.69201774960532],[55.571620210871096,37.691524223146565],[55.57182081372361,37.690982416925564],[55.572477324955024,37.69090731507315],[55.57297274048532,37.69033868676199],[55.573413442276966,37.689496473131314],[55.57374776446085,37.68808026677145],[55.57393739815116,37.6871304151192],[55.57408936109826,37.68479689327624],[55.5738522986428,37.68327339855578],[55.573502780862505,37.68181427685168],[55.5731198698938,37.68084100385693],[55.57226884868343,37.676570927105224],[55.57195275037375,37.67409256597545],[55.57183725227707,37.67254761358288],[55.571727832712895,37.670434032879136],[55.571709596089114,37.66886762281443],[55.571721753839256,37.66814879079844],[55.571697438335185,37.667258297405496],[55.57175822706699,37.66444734235791],[55.57194059269542,37.66137889524485],[55.57214727271286,37.657945667705796],[55.57236610918771,37.654576813183105],[55.57230836079247,37.65431932111766],[55.572077366358634,37.65280119081525],[55.57203177519072,37.651304518184915],[55.57178558196548,37.65075734754589],[55.57186612683322,37.65047303339031],[55.57213055598734,37.65060714384105],[55.57259417935432,37.650006948207775],[55.57274918687897,37.649084268306645],[55.57275830494953,37.64813476631539],[55.57289811509839,37.646381360323836],[55.572910272479014,37.64526556137363],[55.57275222623606,37.6430446923093],[55.57267624223803,37.642572623522675],[55.572320635164836,37.642186385424544],[55.57218690175354,37.64159093502323],[55.57237230476943,37.63997088077824],[55.57293154788601,37.63928959968846],[55.573211166445,37.63828860909767],[55.57341783974695,37.6375751414997],[55.57351661704223,37.63586389214821],[55.573598677990084,37.635448149750914],[55.57358196188504,37.6350350895626],[55.573673140553,37.6338280955059],[55.57379448099227,37.631803134249665],[55.573920610741034,37.628976085947976],[55.57402698491169,37.62722728567027],[55.57417894751062,37.624791839884736],[55.57433090951898,37.62209919082769],[55.5744403217993,37.619669109460204],[55.5745314984657,37.619245320435866],[55.5745254200279,37.61855599271902],[55.574695615929436,37.615089674221636],[55.57484149754114,37.61205341361676],[55.575084632351114,37.60865237258589],[55.575060318938164,37.60712887786543],[55.57487796785901,37.60597016357099],[55.574567969072724,37.605036754833804],[55.57380001828335,37.60354485627425],[55.57330765254822,37.60282602425825],[55.572566052949554,37.60161366578352],[55.57200680460577,37.60092702027571],[55.57149617956756,37.600830460751155],[55.571210469791865,37.600798274243],[55.57078494072433,37.60060515519392],[55.56999466017124,37.60137763139021],[55.568024968367084,37.60189261552107],[55.56659019213717,37.60257926102888],[55.566055177358706,37.60047640916121],[55.56620413107194,37.59829845544112],[55.56643212035068,37.59707000371229],[55.56690937360481,37.59732749577773],[55.56739574035636,37.597375775539994],[55.5690545453018,37.59712973716087],[55.569656390256085,37.59669521930045],[55.57099411448483,37.59552690273475],[55.57195762209202,37.594893901407225],[55.573419025452836,37.59377343836751],[55.57420619756056,37.593151165876044],[55.57556177712584,37.59212214814138],[55.57596598046866,37.59171981678917],[55.57637625778386,37.59097416268302],[55.57665551119821,37.59017698842949],[55.57697764954972,37.588900256938395],[55.577308902256426,37.5875484235949],[55.577719165479635,37.58584790307946],[55.57806416228584,37.58431077172854],[55.578498728873534,37.58239567449189],[55.57903053455839,37.58022844960786],[55.57941950784999,37.578474284912126],[55.58025821834044,37.57503569295502],[55.58019744282266,37.57407249194036],[55.58029468360576,37.57383377533803],[55.5806411019298,37.573686253842205],[55.580744419081256,37.5733617065514],[55.580862929595206,37.57279576044926],[55.58108834044715,37.57158708040533],[55.58136486198801,37.57054638330757],[55.58131624295771,37.56956469480811],[55.58205015120451,37.56756658057433],[55.58273687650884,37.56473953227263],[55.584313868157814,37.55693430403929],[55.58502182252093,37.555040664474774],[55.5858573744768,37.55133921603422],[55.58656161675398,37.54784149896673],[55.58741840331645,37.544966170902754],[55.58809895798065,37.54219813119938],[55.58892532985485,37.53875417482427],[55.589678771334455,37.535546252842444],[55.590292450837325,37.532960603352095],[55.590393358885386,37.529990087531246],[55.58940903914073,37.52911032297436],[55.589402963015935,37.5276941166145],[55.58968853986037,37.527114759467295],[55.59041766276297,37.52781213381116],[55.591438411971865,37.52656758882824],[55.5920520638414,37.52540887453383],[55.59274468902777,37.52242625810927],[55.593564887186304,37.519282709143816],[55.593892961629,37.51775921442336],[55.59449535630148,37.51558741566195],[55.5954066456943,37.51262625690952],[55.59665811515515,37.51006206509127],[55.597636177456806,37.508077230420255],[55.59792776893269,37.50720819469942],[55.5983347783953,37.506693210568585],[55.59924597823647,37.50535210606112],[55.600132859004134,37.50415047642247],[55.600412283019885,37.50392517086521],[55.60043050625583,37.50301321980013],[55.60120802307012,37.50215491291537],[55.602204193871415,37.50118931767001],[55.602799897866454,37.50066534183748],[55.60410883200894,37.49913111828097],[55.60492271789609,37.4981440653635],[55.60665976089706,37.49610558651217],[55.60760720633459,37.49486104152926],[55.607655792660516,37.49363795421848],[55.60837243394651,37.492371951563435],[55.609331983888445,37.49123469494113],[55.61032794749793,37.491084491236286],[55.611178140237165,37.490354930384235],[55.611797554719345,37.48992577694187],[55.61258699030743,37.48941079281101],[55.61320638244049,37.48831645153293],[55.61538024961496,37.486170684321],[55.6167403731031,37.48451844356783],[55.618877614276386,37.481943522913554],[55.62245965950199,37.47784510753879],[55.62511866014429,37.47486249111423],[55.62570559858738,37.47429386280307],[55.62637031044575,37.473392640574055],[55.627314242533906,37.47227147720583],[55.6280548034941,37.47126296661623],[55.62892282017151,37.47036174438723],[55.62956335395402,37.46956955263304],[55.630003417739545,37.46933888265775],[55.63061949870154,37.4685181266992],[55.631120246996836,37.46787439653563],[55.6315329801543,37.467348683568716],[55.63175755371472,37.46712337801145],[55.63208227265825,37.46683369943785],[55.63261941862281,37.46621679136443],[55.63358309871293,37.46499806067178],[55.63423250351134,37.4641558470411],[55.63551434952377,37.46270492533889],[55.636169791028095,37.46170177916732],[55.637143829587345,37.460044173996096],[55.63732285583555,37.45774283866132],[55.636998180508115,37.456766514579904],[55.63637916585454,37.45562389353957],[55.636078758144336,37.45470657805647],[55.636564264402935,37.45415404299939],[55.637174173077064,37.45525911311354],[55.63779014081574,37.45595648745741],[55.6387594137651,37.456244426737456],[55.64016726851847,37.45655556298319],[55.64104550786181,37.45622943235092],[55.641712992414305,37.455371125466165],[55.642052798344,37.45725940061266],[55.64238359606189,37.454710701000444],[55.64271126048261,37.45434592057442],[55.64350310480125,37.4534393339274],[55.64431616959434,37.45275268841959],[55.64471359435151,37.452398636829614],[55.64510798132813,37.451969483387224],[55.645590341515806,37.45136330414986],[55.64679613541619,37.45004373862398],[55.64730881032911,37.44940537287844],[55.648504434099074,37.447949789024136],[55.64979060264535,37.44637265012339],[55.65135578826796,37.44425906941966],[55.65178044019756,37.44370116994456],[55.6523021490936,37.443186185813694],[55.65304830201013,37.44218840406015],[55.6537216472618,37.44140519902781],[55.65493485259473,37.43986024663524],[55.6560449023969,37.438583515144146],[55.65607523118286,37.43789686963633],[55.65641491197343,37.437521360374255],[55.656930493231684,37.437575004554546],[55.65864656622291,37.43548196338124],[55.65933194527997,37.43445199511954],[55.65967889215456,37.433734619872034],[55.66012771393204,37.43202873493857],[55.660831261993394,37.42978640820211],[55.660825196978166,37.428595507399514],[55.66181984686061,37.42829509998983],[55.66205637571865,37.42963620449727],[55.66274775953457,37.4302155616445],[55.66367565011591,37.430194103972376],[55.664500423197126,37.42978640820211],[55.66515537762097,37.42929288174338],[55.665598072061556,37.429131949202485],[55.666010440440886,37.428842270628884],[55.66661079248598,37.42846676136678],[55.66766593427236,37.427694285170496],[55.66791152352008,37.426844024912775],[55.667966098698265,37.426342451827004],[55.668104052280064,37.4262351634664],[55.66840572928998,37.426337087408974],[55.668777327184486,37.42605100359242],[55.66982634668985,37.425498468535345],[55.67039329056137,37.4255091973714],[55.671160319138814,37.425160510199476],[55.67218805312543,37.42446850027361],[55.67297929887346,37.42386232103625],[55.67368784735478,37.42315227559777],[55.67395158756933,37.422873325860216],[55.6743123327874,37.42264802030298],[55.67454575438367,37.42266411355706],[55.67528238739225,37.42213303617212],[55.67616754114105,37.42146784833641],[55.677279309597786,37.4205749454143],[55.67825838377358,37.419818562472095],[55.67910103472042,37.41923384090686],[55.6799861016959,37.4185579242351],[55.68047409235105,37.418139499628786],[55.680844699958456,37.41765731786337],[55.68126296881616,37.41693312142935],[55.68190854888769,37.4156242034301],[55.68210858561136,37.41506093953697],[55.68228437461123,37.41479271863547],[55.68259351885864,37.41510921929923],[55.68285719877749,37.41514677022544],[55.683314549148996,37.414878549323916],[55.683963125919405,37.41467470143879],[55.68404310295229,37.414535811460546],[55.68418099960861,37.41458945564084],[55.684429515426544,37.41489522746854],[55.685051174284276,37.41505625515118],[55.68544969912389,37.41497578888073],[55.68590428521597,37.41484436063898],[55.68610430141525,37.41475316553248],[55.6863891711981,37.41471829681529],[55.68698920924152,37.414479580212955],[55.68747862736432,37.41428377895487],[55.688008949654964,37.41408261327875],[55.68846653622633,37.413814392377276],[55.68927563309499,37.4133530524267],[55.689963503725565,37.412950721074466],[55.690614999944515,37.412467923451786],[55.69125739484358,37.41195830373897],[55.69153313657233,37.411690082837474],[55.69223308757399,37.411126818944346],[55.69284515562492,37.41065475015772],[55.693651131546936,37.40969451933041],[55.69438134344399,37.40869673757686],[55.69504791495923,37.40779551534784],[55.69564478976565,37.40700158147945],[55.69628407191984,37.406175461102855],[55.697183896782384,37.40496310262812],[55.697669624834056,37.40429367617157],[55.698539121948876,37.403108139786994],[55.69886328489186,37.40261997774629],[55.69927530118435,37.402099629197394],[55.70009023232898,37.40079071119811],[55.70010840903727,37.39986803129702],[55.70024170463877,37.39909555510071],[55.70074761788062,37.398215790543844],[55.70068702981508,37.39765252665072],[55.700853646768365,37.39723410204437],[55.70134137593128,37.39799584940461],[55.70177760056313,37.397555967126166],[55.70335887386362,37.39494885996368],[55.70422524838215,37.39502853861556],[55.70503098867513,37.394223875911095],[55.70571140609806,37.39309128767814],[55.70602642233252,37.39261921889151],[55.70651711561729,37.392066683834464],[55.70687150136329,37.391396131580734],[55.7077226197559,37.39037152773705],[55.70868275587822,37.38934692389335],[55.709330534799506,37.38878622701046],[55.70970241768365,37.38820074883233],[55.70995834231483,37.38796203223001],[55.71008554683058,37.387884248168575],[55.71076547820075,37.387237835795986],[55.71114102539975,37.38685964432487],[55.711696768030045,37.38575993862877],[55.71205867789942,37.38472460594902],[55.71232165007117,37.383847759605935],[55.712571500511466,37.38306187236458],[55.712645855317696,37.382216033844635],[55.712624655989224,37.38172250738588],[55.71316069260477,37.3814167355582],[55.71340599503305,37.38218384733646],[55.713587699538145,37.382438657192864],[55.71391476550838,37.38302874317615],[55.71405709891441,37.38327014198749],[55.71442958600138,37.383766350655236],[55.71487777711773,37.38410699120011],[55.7150897576036,37.384165999798455],[55.71571509330604,37.38398092737642],[55.71611633246903,37.38383608808963],[55.7165690464039,37.38362687578647],[55.71689865792006,37.38346574270779],[55.717122739898706,37.38344696724467],[55.717437663740526,37.38340941631846],[55.71777983771659,37.38333431446604],[55.71784796934359,37.383248483777564],[55.71813109282618,37.38311705553584],[55.718530793057525,37.38300708496622],[55.71888506937389,37.3829105254417],[55.71918332513807,37.38274422848278],[55.71966729550855,37.38239353611411],[55.71995191969984,37.38209849312248],[55.720695490879855,37.38183831884802],[55.720686407334924,37.38175248815954],[55.72079995149397,37.38169616177024],[55.721178429629774,37.38151645376623],[55.721494834524385,37.38133406355323],[55.721777931458426,37.380931732201],[55.72172040390606,37.38069569780768],[55.72162502909226,37.380542811893825],[55.72163411241794,37.380167302631754],[55.72172040390606,37.379944679283504],[55.721905097324836,37.37986957743108],[55.72228659242892,37.37974887802541],[55.72272863763054,37.379539665722255],[55.72319585993111,37.379153938651584],[55.72345018197428,37.378724785209194],[55.72345472342421,37.378469975352786],[55.72365454669645,37.3785450772052],[55.72374688892231,37.37886694228699],[55.72397395903685,37.37923976934005],[55.72444228789697,37.379345184893296],[55.724584582779016,37.37936664256543],[55.72520522457974,37.37953025731534],[55.72562150316357,37.37940419349164],[55.72590002889105,37.37932372722119],[55.726311758920346,37.37911987933606],[55.72683881435683,37.378827307782615],[55.72735346378546,37.37848130281971],[55.727638031721085,37.37826940830754],[55.728060338807666,37.377969000897856],[55.72864459839622,37.37749424990221],[55.72901007194221,37.37629594086926],[55.72954285558789,37.374193089001594],[55.73005141498378,37.371661083691535],[55.73096408078767,37.37129093884748],[55.73140082008135,37.37259538999893],[55.732257455713,37.37312646738387],[55.73271754903706,37.373394688285366],[55.73332212778345,37.3736412912262],[55.73407278299808,37.37387732561952],[55.73525624625019,37.3739846139801],[55.73602805069678,37.37368420657044],[55.73692998313909,37.37322823103789],[55.73790464561055,37.372898824427025],[55.73882771956154,37.37262523910753],[55.73999893265504,37.372029788706215],[55.741197346723254,37.37138605854264],[55.74307962955817,37.37043119213333],[55.744928535658936,37.36972845337144],[55.74578789905086,37.36945486805192],[55.747900855778475,37.368887325468215],[55.75085075269467,37.36838848024554],[55.75377327541109,37.368645972310965],[55.75592115915323,37.36861378580278],[55.75927888023348,37.36875326067157],[55.762211213079134,37.368688887655196],[55.76277983648985,37.36819536119646],[55.76355411880798,37.3681309881801],[55.76412877148337,37.3679485979671],[55.764338971258454,37.36648411184495],[55.76428301891148,37.365566796361875],[55.76439794796932,37.3655560675258],[55.76458243864023,37.366140789091055],[55.764854636389856,37.36654043823428],[55.76516010048082,37.366623586713736],[55.76535214845602,37.36667454868501],[55.76550185448644,37.36678183704562],[55.76575741193179,37.36698300272174],[55.76616569548397,37.367578453123045],[55.76672821245596,37.36838311582751],[55.76720150671236,37.36908317238039],[55.767981749183164,37.36913145214265],[55.768757439815154,37.368731802999434],[55.76975538157251,37.36723513036913],[55.77020898298114,37.366569942533445],[55.770683746778076,37.36673623949237],[55.77098614107375,37.36689180761521],[55.771621161426495,37.365620440542166],[55.77177840295669,37.36594767004198],[55.77189330982593,37.36712784200852],[55.77226524288587,37.368490404188094],[55.7729395514591,37.3691555920238],[55.77453305027661,37.369252151548324],[55.77675508695251,37.369290309595456],[55.77894402598816,37.36936541144787],[55.7817314187832,37.369435148882246],[55.78431305517567,37.36960144584119],[55.78659829486894,37.3703631932014],[55.7878919952294,37.37069578711926],[55.78894385063695,37.369848209070554],[55.78982944241203,37.368716316866276],[55.79041277338896,37.36763806884228],[55.790968894234624,37.36811013762891],[55.79090844670286,37.36897380893169],[55.79121370577488,37.371248322176314],[55.791488739118975,37.372551875757566],[55.791809105170714,37.37373204772411],[55.79258280849622,37.374847846674314],[55.793673824848184,37.37608166282115],[55.79477689882081,37.37743349616466],[55.794849428612835,37.37719746177135],[55.79539490874395,37.37778486554562],[55.79582705653087,37.37818183247983],[55.79599024395304,37.37824352328716],[55.79610810110865,37.378187196897855],[55.796201782181946,37.378409820246084],[55.79668680476496,37.37902404611049],[55.79749950425624,37.37988036901319],[55.800043992474706,37.38203488626441],[55.80209867161425,37.38383733072242],[55.80303230600686,37.384706366443254],[55.803978003359916,37.385484207057566],[55.804428183452075,37.38589190282783],[55.80517444363266,37.38625668325385],[55.805742437704666,37.386685836696245],[55.80673640730528,37.387157905482844],[55.80712009088022,37.38618158140143],[55.80861249323653,37.38635324277838],[55.80890250807897,37.3861949924465],[55.80900522114864,37.38541446962317],[55.80922726170731,37.385545897864894],[55.809293722579575,37.38592945375404],[55.80956711724186,37.386095750712954],[55.80978764412787,37.38637738265952],[55.80973175729599,37.38744490184746],[55.80972722592773,37.38811008968314],[55.809899417549865,37.38846950569114],[55.81042202954328,37.38861970939598],[55.81080718705112,37.38871090450248],[55.811496430790584,37.38894461258833],[55.81221838916406,37.38909749850216],[55.81298564399126,37.38934426173155],[55.813915995609605,37.389591024960914],[55.81522086674087,37.38994507655089],[55.81635655214755,37.39029912814083],[55.81827143567453,37.391039417828964],[55.8201560235891,37.391608046140114],[55.82092916134438,37.39151148661556],[55.821321764454204,37.3916402326483],[55.82192576146999,37.39218740328732],[55.82312999206834,37.39249236542998],[55.82441643433391,37.39286787469208],[55.824777294855515,37.39306099374113],[55.8252423319092,37.39315487105667],[55.825582047007316,37.39318705756486],[55.826021407449254,37.393232655118105],[55.826935570129734,37.39346788632218],[55.827461338634315,37.39362989991611],[55.827936911692746,37.393739870485724],[55.82851457479287,37.3939196833372],[55.828952392265634,37.39402965390682],[55.82972993791826,37.39425558223862],[55.83019340585606,37.3944299258246],[55.83061615921723,37.394564184337895],[55.83135641865517,37.39417526403078],[55.83174287770188,37.393445703178735],[55.832038757802955,37.393011185318315],[55.832349731391055,37.3918578354419],[55.83243728661099,37.390994164139116],[55.83343057162401,37.39156279245027],[55.83327357994324,37.3927912441791],[55.83351812440031,37.393735381752336],[55.833385285627564,37.39509257951388],[55.83389852381959,37.39537152925143],[55.83456572329998,37.39550027528414],[55.835124229862885,37.39561292806277],[55.83594235196098,37.39553246179232],[55.83708950512678,37.395457359939904],[55.84230733910155,37.393182083019376],[55.84452879758111,37.39165858829894],[55.84646039698459,37.39131526554502],[55.84872990311555,37.391401096233494],[55.849285187543174,37.39193753803649],[55.85034744859715,37.392366691478856],[55.85150624558516,37.3930962523309],[55.85307539453261,37.39384727085508],[55.85467465394779,37.39474849308407],[55.855905736925315,37.39539222324765],[55.85659367799994,37.395907207378514],[55.85737514008334,37.39635781849299],[55.85821542050434,37.396872802623854],[55.858563897695156,37.39708737934503],[55.858714752648424,37.39703909958277],[55.858927457133106,37.39729122723016],[55.859601767875695,37.39762650335702],[55.86045557624863,37.39824072922142],[55.86100616706053,37.398677929290855],[55.861591444043455,37.39918486679468],[55.86229340904586,37.39974878849992],[55.862575480135646,37.40007333579072],[55.86306118061456,37.40007870020875],[55.863646426506236,37.399885581159694],[55.86434629656507,37.400336192274196],[55.864780692301395,37.40015380206117],[55.865263348509,37.401001380109875],[55.865293514322005,37.402106450224025],[55.86580632954348,37.403200791502094],[55.86654989953782,37.40432463707934],[55.86759358827526,37.40570329251299],[55.868217978187865,37.406636701250164],[55.8685558081409,37.40692369761476],[55.8690112706384,37.40770690264712],[55.86955419905779,37.408610807085125],[55.87006092537243,37.40953616919526],[55.870418344404555,37.41023890795713],[55.8707983812708,37.41073243441586],[55.87158257252357,37.410576866293],[55.8718117946554,37.410174534940786],[55.87211641670159,37.411124036932044],[55.8717665535528,37.41193406405454],[55.87176956962796,37.41293184580809],[55.87291566115739,37.41571061434752],[55.873458534762676,37.417421863699005],[55.873980289433845,37.41931550326352],[55.87483981212339,37.42205135645871],[55.8754188482162,37.4238484364987],[55.875699315706264,37.424830124998145],[55.87602803307812,37.42506079497343],[55.87660705137639,37.42465846362114],[55.87702321542217,37.42489449801446],[55.87755396886491,37.425967381620424],[55.877572062603846,37.427415774488466],[55.877976153900335,37.42931477847101],[55.87795806035052,37.43028037371637],[55.87786156127515,37.43129961314202],[55.878157088932774,37.43261925997736],[55.878657671443555,37.43420712771416],[55.87903159631879,37.434958146238344],[55.87932108406088,37.43566624941828],[55.87967087885702,37.43684642138483],[55.88006891736122,37.436975167417536],[55.88037648976199,37.43758671107293],[55.88014128755805,37.438262627744685],[55.88063581361021,37.43985049548152],[55.88123888559992,37.44127743067743],[55.882089201120095,37.44196407618524],[55.88297568036404,37.442103551054004],[55.88352444304121,37.44316570582392],[55.88327116892752,37.44368068995478],[55.8831023185964,37.44441025080683],[55.88294552834279,37.446727679395686],[55.8827344635355,37.449324057722116],[55.882806828742126,37.4516629439831],[55.882873163395836,37.45343320193293],[55.88293346752769,37.45509617152216],[55.88293346752769,37.457199023389826],[55.883011862758465,37.4603103858471],[55.883084227445764,37.4622523051739],[55.883295290343185,37.46508471789362],[55.88375962466077,37.46788494410515],[55.88445913076365,37.470363305234926],[55.885006954873454,37.47250254266955],[55.88548935866212,37.4742406141112],[55.886104414760354,37.47584993952014],[55.88670740143327,37.478145910436886],[55.88814247187553,37.48138601892689],[55.88867307266259,37.482179952795306],[55.88891425243023,37.48507673853137],[55.888612777485505,37.48574192636707],[55.88872130873653,37.48728687875964],[55.88913131293418,37.48861725443103],[55.88934837221895,37.48948092573384],[55.889800575146424,37.49073083513478],[55.89041960334127,37.49218234663401],[55.89122449856594,37.493115755371186],[55.892195153710844,37.4937004769364],[55.89391939879166,37.494022342018184],[55.89464283535136,37.495610209754986],[55.89383499697735,37.49872157221228],[55.89470312111989,37.50153252725987],[55.895643566929635,37.50382849817662],[55.8971385875048,37.507240268043574],[55.900863830622534,37.51552292948157],[55.90328685418197,37.52101609354406],[55.90402216957663,37.5227327073136],[55.90476952497702,37.5247497284928],[55.90567356460145,37.52791473513036],[55.9064449950141,37.5308383429566],[55.90694468980042,37.53297759425038],[55.90723698179776,37.53344966303701],[55.90742681974503,37.534704936855974],[55.907658842635506,37.53688289057607],[55.90774924078689,37.53805769812458],[55.90777033365843,37.53933979403371],[55.907908943670535,37.54075063597554],[55.90822232099438,37.54116906058188],[55.908674303040165,37.54147483240956],[55.90899370049578,37.54194690119619],[55.909415542143186,37.5427944792449],[55.90971384166948,37.54312707316274],[55.91002419125599,37.543266548031525]]] };
        
        app.mkad = new ymaps.Polygon(json.coordinates);
        app.mkad.options.set('visible', true);
        app.map.geoObjects.add(app.mkad);
    }

    function calculateDistanceFromMKAD(callback){
    	// ---

	        var startPoint = [55.7533,37.6225]; // Moscow geo center

	        // Calculation end point
	        	var address = $('.modal-basket [name="address"]').val();

	        	ymaps.geocode(address, { results: 1 }).then(
		        	function (res) {
				        var firstGeoObject = res.geoObjects.get(0);
				        var endPoint = firstGeoObject.geometry.getCoordinates();

	    				console.log('#DEBUG');

				        // Create route
				        	ymaps.route([startPoint,endPoint]).then(function (res) {
				        		// ---
					                // Объединим в выборку все сегменты маршрута.
					                var pathsObjects = ymaps.geoQuery(res.getPaths());
					                var edges = [];

					                // Переберем все сегменты и разобьем их на отрезки.
					                pathsObjects.each(function (path) {
					                    var coordinates = path.geometry.getCoordinates();
					                    for (var i = 1, l = coordinates.length; i < l; i++) {
					                        edges.push({
					                            type: 'LineString',
					                            coordinates: [coordinates[i], coordinates[i - 1]]
					                        });
					                    }
					                });
					                
					                var routeObjects = ymaps.geoQuery(edges).add(res.getWayPoints()).add(res.getViaPoints()).setOptions('strokeWidth', 3).addToMap(app.map);
					                
					                var objectsInMoscow = routeObjects.searchInside(app.mkad);
					                var boundaryObjects = routeObjects.searchIntersect(app.mkad);

					                //routeObjects.setOptions({strokeColor: '#EEEEEE00'});
					                //routeObjects.remove(objectsInMoscow).setOptions({strokeColor: '#FF0000FF'});
					                var outerObject = routeObjects.remove(objectsInMoscow);

					                if( outerObject.getLength() > 0 ){
						                var this_coordinates = outerObject.get(0).geometry.getCoordinates();

						                ymaps.route([this_coordinates[0],endPoint]).then(function (res) {
						                	// ---
							                	var deliverydistance = Math.ceil(res.properties._data.RouterRouteMetaData.length / 1000);
							                	console.log('Delivery distance is ' + deliverydistance);
							                	$('.modal-basket [name="deliverydistance"]').val(deliverydistance);

							                	callback();
							                // ---
						                },
									    function(e) {
										  // Rejected
										  	console.log("Rejected ymaps", e);

										  	var deliverydistance = -1;
											$('.modal-basket [name="deliverydistance"]').val(deliverydistance);

						    				callback();
										  // ---
										});
					                }
					                else {
					                	// ---
										  	console.log("Point into MKAD");

					                		var deliverydistance = 0;
											$('.modal-basket [name="deliverydistance"]').val(deliverydistance);

											callback();
					                	// ---
					                }
					        	// ---
					        },
						    function(e) {
								// Rejected
								  	console.log("Rejected ymaps", e);

								  	var deliverydistance = -1;
									$('.modal-basket [name="deliverydistance"]').val(deliverydistance);

				    				callback();
								// ---
							});
				        // ---
				    },
				    function(e) {
						// Rejected
						  	console.log("Rejected ymaps", e);

						  	var deliverydistance = -1;
							$('.modal-basket [name="deliverydistance"]').val(deliverydistance);

		    				callback();
						// ---
					}
				).catch(function(e){
				    callback();
				});
	        // ---
	    // ---
    }
// ---

// Catalog
	function catalogMenuInit(){
		var preHeght = app.height - 180;
		var fixHeight  = 0;
		var size  = 0;

		if( preHeght >= 720 ){ fixHeight = 712; size = 7; }
		else if( preHeght >= 620 && preHeght < 720 ){ fixHeight = 612; size = 6; }
		else if( preHeght >= 520 && preHeght < 620 ){ fixHeight = 512; size = 5; }
		else if( preHeght >= 420 && preHeght < 520 ){ fixHeight = 412; size =4; }
		else { fixHeight = 312; size = 3; }

		$('.all-l_a2').attr('data-size', size);
		$('.all-l_a2').css('height', fixHeight);
	}

	function catalogProductDymanmicShow($container){
		setTimeout(function(){
			$container.find('[data-product]').css({
				'-webkit-transform': 'translateY(0px)',
				'-moz-transform': 'translateY(0px)',
				'-ms-transform': 'translateY(0px)',
				'-o-transform': 'translateY(0px)',
				'transform': 'translateY(0px)',
				'opacity': '1'
			});
		},500);
	}

	function catalogProductSearch(){
		// ---
			if( app.search.flag == false ){
				// ---
					if( app.search.flag == false && app.search.query.length > 1 ){

						app.search.flag = true;

						$.get('/?route=ajax/index/ajaxSearchProducts', { search:app.search.query }, function(data){
							// ---
								$("html, body").animate({ scrollTop: $('.products-grid').offset().top });
		                       
		                        $('#search-content div.container').html(data);

		                        $('.all-l_a2').hide();
		                        $('#container-products-categories').hide();
		                        $('#search-content').show();

			                    catalogProductDymanmicShow($('#search-content'));
			                    lazyLoadImages($('#search-content'));
			                    initTooltips();
							// ---
                    	});
					}
					else{
						$('#search-content div.container').html('');

						$('.all-l_a2').show();
		                $('#search-content').hide();
		                $('#container-products-categories').show();

			            app.search.query = '';
					}
				// ---
			}
		// ---
	}
// ---

// Account
	function accountTestimonialsLoadItems(){
		// ---
			$.post('/?route=account/testimonials/getItems', {}, function(data){
				// ---
					if ( data.result == true ){
						$('#account.testimonials').find('.testimonials-list').html('');

						$.each(data.items, function(key, item){
							// ---
								$('#account.testimonials').find('.testimonials-list').append(item);
							// ---
						});
					}
				// ---
			}, 'json');
		// ---
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

// Scroll
	function scrollSpyCategory(scrollTop){
		$('[data-scrollspy="category"]').each(function(){
			var $curent = $(this);

			if( scrollTop > $(this).offset().top - 200 && scrollTop < $(this).offset().top + $(this).height() - 200 ){
				if( app.scroll.category != $(this).attr('id') ){
					// ---
						app.scroll.category = $curent.attr('id');	
						
						if( app.scroll.check == true ){
							app.scroll.check = false;

							$('[data-scrollspy="category"]').each(function(){
								if( app.scroll.category != $(this).attr('id') ){
									if( $(this).attr('data-lazy-load') == 'false' ){
										lazyLoadImages($(this));
									}
								}
								else{ return false; }
							});
						}

						// Change catalog menu
							var $menuItem = $('.products-grid').find('.all-l_a2 .list-products > li a[href="#'+app.scroll.category+'"]');
							var $menuCount = parseInt($('.products-grid').find('.all-l_a2 .list-products > li').length-1);
							var $menuSize = parseInt($('.products-grid').find('.all-l_a2').attr('data-size'));
							var $menuIndex = parseInt($menuItem.parent().index());

							// Change style
								$menuItem.parents('.list-products').find('li>a').attr('data-style','default');
								$menuItem.attr('data-style','active');

								$menuItem.parents('.list-products').find('.slide-selector').css({
									'-webkit-transform': 'translateY('+($menuIndex*100)+'px)',
									'-moz-transform': 'translateY('+($menuIndex*100)+'px)',
									'-ms-transform': 'translateY('+($menuIndex*100)+'px)',
									'-o-transform': 'translateY('+($menuIndex*100)+'px)',
									'transform': 'translateY('+($menuIndex*100)+'px)'
								});

								if( $menuIndex > 2 ){
									//console.log($menuIndex);

									if( $menuIndex <= $menuCount-$menuSize+2 ){
										$menuItem.parents('.list-products').css({
											'-webkit-transform': 'translateY(-'+($menuIndex*100 - 200)+'px)',
											'-moz-transform': 'translateY(-'+($menuIndex*100 - 200)+'px)',
											'-ms-transform': 'translateY(-'+($menuIndex*100 - 200)+'px)',
											'-o-transform': 'translateY(-'+($menuIndex*100 - 200)+'px)',
											'transform': 'translateY(-'+($menuIndex*100 - 200)+'px)'
										});
									}

									$menuItem.parents('.list-products').attr('data-scroll',(1-($menuIndex*100 - 200)));
								}
								else{
									$menuItem.parents('.list-products').css({
										'-webkit-transform': 'translateY('+(0)+'px)',
										'-moz-transform': 'translateY('+(0)+'px)',
										'-ms-transform': 'translateY('+(0)+'px)',
										'-o-transform': 'translateY('+(0)+'px)',
										'transform': 'translateY('+(0)+'px)'
									});

									$menuItem.parents('.list-products').attr('data-scroll',0);
								}
							// ---
						// ---

						var $next_one = $curent.next();
						var $next_two = $next_one.next();

						if( $curent.attr('data-lazy-load') == 'false' ){
							lazyLoadImages($curent);
						}

						if( $next_one.attr('data-lazy-load') == 'false' ){
							lazyLoadImages($next_one);
						}

						if( $next_two.attr('data-lazy-load') == 'false' ){
							lazyLoadImages($next_two);
						}
					// ---
				}
			}

			// Menu
				if( scrollTop < $('.products-grid').offset().top ){
					$('.products-grid').find('.all-l_a2').css({
						'position': 'absolute',
						'top': '100px',
						'bottom': 'auto'
					});
				}
				else if( scrollTop >= $('.products-grid').offset().top && scrollTop < $('.products-grid').offset().top + $('.products-grid').height() - app.height + 100 ){
					$('.products-grid').find('.all-l_a2').css({
						'position': 'fixed',
						'top': '100px',
						'bottom': 'auto'
					});
				}
				else if( scrollTop >= $('.products-grid').offset().top + $('.products-grid').height() - app.height + 100 ){
					$('.products-grid').find('.all-l_a2').css({
						'position': 'absolute',
						'top': 'auto',
						'bottom': '100px',
					});
				}
				else{
					console.log('No scroll proccessor');
				}
			// ---
		});
	}

	function progressScroll(){
		var winScroll = document.body.scrollTop || document.documentElement.scrollTop;
		var height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
		var scrolled = (winScroll / height) * 100;

		$('.progress-view .marker').css('width', scrolled + '%');
	}
// ---

// Helpers
	function lazyLoadImages($lazyContainer){
		$lazyContainer.find('img.image-lazy-load').each(function(key, val){
			$(this).attr('src', $(this).attr('data-src'));
			$(this).removeClass('image-lazy-load');
		});

		$lazyContainer.attr('data-lazy-load', 'true');
	}

	function checkRoistat() {
		// ---
			var roistat_visit = Cookies.get('roistat_visit');

			if( typeof roistat_visit != 'undefined' ){
				// ---
					app.customer.roistat_visit = roistat_visit;
					$('body').append('<div class="roistat-visit"><p>№ '+roistat_visit+'</p></div>');
				// ---
			}
		// ---
	}

	function checkHash() {
		var hash = window.location.hash.replace('#','');

		if( $('.b-accordion .b-accordion_title[data-marker="'+hash+'"]').length > 0 ){
			console.log($('.b-accordion .b-accordion_title[data-marker="'+hash+'"]').length);
			
			setTimeout(function(){
				$("html, body").animate({ scrollTop: $('.b-accordion .b-accordion_title[data-marker="'+hash+'"]').offset().top - 50 });
				$('.b-accordion').find('.b-accordion_title[data-marker="'+hash+'"]').trigger('click');
			},1000);
		}
	}


	function getUrlParam(param){
	  param = param.replace(/([\[\](){}*?+^$.\\|])/g, "\\$1");
	  var regex = new RegExp("[?&]" + param + "=([^&#]*)");
	  var url   = decodeURIComponent(window.location.href);
	  var match = regex.exec(url);
	  return match ? match[1] : "";
	}
// ---

// Components
	// Modal
		jQuery.fn.modal = function (method) {
			var $this = $(this);

			switch (method) {
				case 'show':
					// ---

						$this.attr('data-display','flex');
						$this.css('display','flex');

						$this.find('.close, .overlay').on('click', function(){

							$(this).parents('.modal').modal('hide');

							if( $(this).parents('.modal').attr('data-marker') == 'modal-oneoff-coupon' ){
								$('.adversting[data-target="modal-oneoff-coupon"]').css('display','block');
								setTimeout(function(){
									$('.adversting[data-target="modal-oneoff-coupon"]').attr('data-visible','true');
								}, 500);
							}
						});
					// ---
				break;
				
				case 'hide':
					// ---
						$this.attr('data-display','none');

						setTimeout(function(){
							$this.css('display','none');
						}, 1000);
					// ---
				break;
			}
		}
	// ---

	// Tooltips
		function initTooltips() {
			// ---
				var targets = $( '[rel~=tooltip]' ),
					target  = false,
					tooltip = false,
					title   = false;

					targets.bind( 'mouseenter', function() {
					target  = $( this );
					tip     = target.attr( 'title' );
					tooltip = $( '<div id="tooltip"></div>' );
					if( !tip || tip == '' ) return false;

					target.removeAttr( 'title' );
					tooltip.css( 'opacity', 0 ).html( tip ).appendTo( 'body' );

					var init_tooltip = function() {
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
			 
					var remove_tooltip = function(){
						tooltip.animate( { top: '-=10', opacity: 0 }, 50, function() {
							$( this ).remove();
						});
			 
						target.attr( 'title', tip );
					};
			 
					target.bind( 'mouseleave', remove_tooltip );
					tooltip.bind( 'click', remove_tooltip );
				});
			// ---
		}
	// ---
// ---