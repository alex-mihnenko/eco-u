var app = {
  page: '',
  width: 0,
  height: 0,
  size: '',
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
		
		// Nav
			if( $(window).scrollTop() >= 50 ){ $('nav').attr('data-style', 'solid'); }
			else{ $('nav').attr('data-style', 'default'); }
		// ---

		// Catalog			
			scrollSpyCategory($(window).scrollTop());
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
			
			$(document).on('click', '.dropdown > [data-action="toggle"]', function(){
				// ---
					let $this = $(this).parents('.dropdown');

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

			$(document).on('focusout', '.dropdown > [data-action="toggle"]', function(){
				// ---
					if( app.size != 'xs' && app.size != 'sm' ){
						let $this = $(this).parents('.dropdown');
						$this.attr('data-style','default');
					}
				// ---
			});

			$(document).on('click', '.dropdown .sub-dropdown > [data-action="toggle"]', function(){
				// ---
					let $this = $(this).parents('.sub-dropdown');

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
			
			$(document).on('click', '.dropdown .item:not(.sub-dropdown)', function(){
				// ---
					let $this = $(this).parents('.dropdown');
					$this.attr('data-style','default');
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
	    // ---

        // Change Quantity
	        $('.modal-basket').on('change', 'select.change-m-cart-quantity', function() {
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
			$(document).on('keyup change paste', '.modal-basket [name="address"]', function(){
				// ---
					var $this = $(this);
					var $form = $(this).parents('form');
					var $modal = $(this).parents('.remodal');

					var address = $(this).val();
					var deliveryprice = parseInt($form.find('[name="deliveryprice"]').val());

					if( $this.hasClass('select') ) {
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
					var $button = $(this).find('button[type="submit"]');

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
							$button.html('Подождите');
							$button.attr('disabled','true');

							calculateDistanceFromMKAD(function(){
            					// ---
            						var deliverydistance = parseInt($form.find('[name="deliverydistance"]').val());

									$.post('/?route=ajax/index/ajaxGetDeliveryPrice', { order_id: order_id, firstname: firstname, telephone: telephone, address: address, payment_method: payment_method, payment_code: payment_code, total: total, deliveryprice: deliveryprice, deliverydistance: deliverydistance, date: date, time: time, comment: comment }, function(data){
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
				    	console.log('Down');
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
				    	console.log('Up');
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
			                }
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
					if( app.size != 'xs' && app.size != 'sm' ){
						$.post('/?route=ajax/index/getViewProduct', {product_id:product_id}, function(data){
							// ---
								console.log(data);
								$('[data-remodal-id="modal-product"]').find('.body').html(data.html);


								app.modals.product.open();
								e.preventDefault();
							// ---
						},'json');
						
						e.preventDefault();
					}
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
		
		$('[name="phone"]').mask("+7 (999) 999-99-99");
		$('[name="telephone"]').mask("+7 (999) 999-99-99");

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
		
		var json = { "type": "Polygon", "coordinates": [[[55.78000432402266,37.84172564285271],[55.775874525970494,37.8381207618713],[55.775626746008065,37.83979446823122],[55.77446586811748,37.84243326983639],[55.771974101091104,37.84262672750849],[55.77114545193181,37.84153238623039],[55.76722010265554,37.841124690460184],[55.76654891107098,37.84239076983644],[55.76258709833121,37.842283558197025],[55.758073999993734,37.8421759312134],[55.75381499999371,37.84198330422974],[55.749277102484484,37.8416827275085],[55.74794544108413,37.84157576190186],[55.74525257875241,37.83897929098507],[55.74404373042019,37.83739676451868],[55.74298009816793,37.838732481460525],[55.743060321833575,37.841183997352545],[55.73938799999373,37.84097476190185],[55.73570799999372,37.84048155819702],[55.73228210777237,37.840095812164286],[55.73080491981639,37.83983814285274],[55.729799917464675,37.83846476321406],[55.72919751082619,37.83835745269769],[55.72859509486539,37.838636380279524],[55.727705075632784,37.8395161005249],[55.722727886185154,37.83897964285276],[55.72034817326636,37.83862557539366],[55.71944437307499,37.83559735744853],[55.71831419154461,37.835370708803126],[55.71765218986692,37.83738169402022],[55.71691750159089,37.83823396494291],[55.71547311301385,37.838056931213345],[55.71221445615604,37.836812846557606],[55.709331054395555,37.83522525396725],[55.70953687463627,37.83269301586908],[55.70903403789297,37.829667367706236],[55.70552351822608,37.83311126588435],[55.70041317726053,37.83058993121339],[55.69883771404813,37.82983872750851],[55.69718947487017,37.82934501586913],[55.69504441658371,37.828926414016685],[55.69287499999378,37.82876530422971],[55.690759754047335,37.82894754100031],[55.68951421135665,37.827697554878185],[55.68965045405069,37.82447346292115],[55.68322046195302,37.83136543914793],[55.67814012759211,37.833554015869154],[55.67295011628339,37.83544184655761],[55.6672498719639,37.837480388885474],[55.66316274139358,37.838960677246064],[55.66046999999383,37.83926093121332],[55.65869897264431,37.839025050262435],[55.65794084879904,37.83670784390257],[55.65694309303843,37.835656529083245],[55.65689306460552,37.83704060449217],[55.65550363526252,37.83696819873806],[55.65487847246661,37.83760389616388],[55.65356745541324,37.83687972750851],[55.65155951234079,37.83515216004943],[55.64979413590619,37.83312418518067],[55.64640836412121,37.82801726983639],[55.64164525405531,37.820614174591],[55.6421883258084,37.818908190475426],[55.64112490388471,37.81717543386075],[55.63916106913107,37.81690987037274],[55.637925371757085,37.815099354492155],[55.633798276884455,37.808769150787356],[55.62873670012244,37.80100123544311],[55.62554336109055,37.79598013491824],[55.62033499605651,37.78634567724606],[55.618768681480326,37.78334147619623],[55.619855533402706,37.77746201055901],[55.61909966711279,37.77527329626457],[55.618770300976294,37.77801986242668],[55.617257701952106,37.778212973541216],[55.61574504433011,37.77784818518065],[55.61148576294007,37.77016867724609],[55.60599579539028,37.760191219573976],[55.60227892751446,37.75338926983641],[55.59920577639331,37.746329965606634],[55.59631430313617,37.73939925396728],[55.5935318803559,37.73273665739439],[55.59350760316188,37.7299954450912],[55.59469840523759,37.7268679946899],[55.59229549697373,37.72626726983634],[55.59081598950582,37.7262673598022],[55.5877595845419,37.71897193121335],[55.58393177431724,37.70871550793456],[55.580917323756644,37.700497489410374],[55.57778089778455,37.69204305026244],[55.57815154690915,37.68544477378839],[55.57472945079756,37.68391050793454],[55.57328235936491,37.678803592590306],[55.57255251445782,37.6743402539673],[55.57216388774464,37.66813862698363],[55.57505691895805,37.617927457672096],[55.5757737568051,37.60443099999999],[55.57749105910326,37.599683515869145],[55.57796291823627,37.59754177842709],[55.57906686095235,37.59625834786988],[55.57746616444403,37.59501783265684],[55.57671634534502,37.593090671936025],[55.577944600233785,37.587018007904],[55.57982895000019,37.578692203704804],[55.58116294118248,37.57327546607398],[55.581550362779,37.57385012109279],[55.5820107079112,37.57399562266922],[55.58226289171689,37.5735356072979],[55.582393529795155,37.57290393054962],[55.581919415056234,37.57037722355653],[55.584471614867844,37.5592298306885],[55.58867650795186,37.54189249206543],[55.59158133551745,37.5297256269836],[55.59443656218868,37.517837865081766],[55.59635625174229,37.51200186508174],[55.59907823904434,37.506808949737554],[55.6062944994944,37.49820432275389],[55.60967103463367,37.494406071441674],[55.61066689753365,37.494760001358024],[55.61220931698269,37.49397137107085],[55.613417718449064,37.49016528606031],[55.61530616333343,37.48773249206542],[55.622640129112334,37.47921386508177],[55.62993723476164,37.470652153442394],[55.6368075123157,37.46273446298218],[55.64068225239439,37.46350692265317],[55.640794546982576,37.46050283203121],[55.64118904154646,37.457627470916734],[55.64690488145138,37.450718034393326],[55.65397824729769,37.44239252645875],[55.66053543155961,37.434587576721185],[55.661693766520735,37.43582144975277],[55.662755031737014,37.43576786245721],[55.664610641628116,37.430982915344174],[55.66778515273695,37.428547447097685],[55.668633314343566,37.42945134592044],[55.66948145750025,37.42859571562949],[55.670813882451405,37.4262836402282],[55.6811141674414,37.418709037048295],[55.68235377885389,37.41922139651101],[55.68359335082235,37.419218771842885],[55.684375235224735,37.417196501327446],[55.68540557585352,37.41607020370478],[55.68686637150793,37.415640857147146],[55.68903015131686,37.414632153442334],[55.690896881757396,37.413344899475064],[55.69264232162232,37.41171432275391],[55.69455101638112,37.40948282275393],[55.69638690385348,37.40703674603271],[55.70451821283731,37.39607169577025],[55.70942491932811,37.38952706878662],[55.71149057784176,37.387778313491815],[55.71419814298992,37.39049275399779],[55.7155489617061,37.385557272491454],[55.71849856042102,37.38388335714726],[55.7292763261685,37.378368238098155],[55.730845879211614,37.37763597123337],[55.73167906388319,37.37890062088197],[55.734703664681774,37.37750451918789],[55.734851959522246,37.375610832015965],[55.74105626086403,37.3723813571472],[55.746115620904355,37.37014935714723],[55.750883999993725,37.36944173016362],[55.76335905525834,37.36975304365541],[55.76432079697595,37.37244070571134],[55.76636979670426,37.3724259757175],[55.76735417953104,37.369922155757884],[55.76823419316575,37.369892695770275],[55.782312184391266,37.370214730163575],[55.78436801120489,37.370493611114505],[55.78596427165359,37.37120164550783],[55.7874378183096,37.37284851456452],[55.7886695054807,37.37608325135799],[55.78947647305964,37.3764587460632],[55.79146512926804,37.37530000265506],[55.79899647809345,37.38235915344241],[55.80113596939471,37.384344043655396],[55.80322699999366,37.38594269577028],[55.804919036911976,37.38711208598329],[55.806610999993666,37.3880239841309],[55.81001864976979,37.38928977249147],[55.81348641242801,37.39038389947512],[55.81983538336746,37.39235781481933],[55.82417822811877,37.393709457672124],[55.82792275755836,37.394685720901464],[55.830447148154136,37.39557615344238],[55.83167107969975,37.39844478226658],[55.83151823557964,37.40019761214057],[55.83264967594742,37.400398790382326],[55.83322180909622,37.39659544313046],[55.83402792148566,37.39667059524539],[55.83638877400216,37.39682089947515],[55.83861656112751,37.39643489154053],[55.84072348043264,37.3955338994751],[55.84502158126453,37.392680272491454],[55.84659117913199,37.39241188227847],[55.84816071336481,37.392529730163616],[55.85288092980303,37.39486835714723],[55.859893456073635,37.39873052645878],[55.86441833633205,37.40272161111449],[55.867579567544375,37.40697072750854],[55.868369880337,37.410007082016016],[55.86920843741314,37.4120992989502],[55.87055369615854,37.412668021163924],[55.87170587948249,37.41482461111453],[55.873183961039565,37.41862266137694],[55.874879126654704,37.42413732540892],[55.875614937236705,37.4312182698669],[55.8762723478417,37.43111093783558],[55.87706546369396,37.43332105622856],[55.87790681284802,37.43385747619623],[55.88027084462084,37.441303050262405],[55.87942070143253,37.44747234260555],[55.88072960917233,37.44716141796871],[55.88121221323979,37.44769797085568],[55.882080694420715,37.45204320500181],[55.882346110794586,37.45673176190186],[55.88252729504517,37.463383999999984],[55.88294937719063,37.46682797486874],[55.88361266759345,37.470014457672086],[55.88546991372396,37.47751410450743],[55.88534929207307,37.47860317658232],[55.882563306475106,37.48165826025772],[55.8815803226785,37.48316434442331],[55.882427612793315,37.483831555817645],[55.88372791409729,37.483182967125686],[55.88495581062434,37.483092277908824],[55.8875561994203,37.4855716508179],[55.887827444039566,37.486440636245746],[55.88897899871799,37.49014203439328],[55.890208937135604,37.493210285705544],[55.891342397444696,37.497512451065035],[55.89174030252967,37.49780744510645],[55.89239745507079,37.49940333499519],[55.89339220941865,37.50018383334346],[55.903869074155224,37.52421672750851],[55.90564076517974,37.52977457672118],[55.90661661218259,37.53503220370484],[55.90714113744566,37.54042858064267],[55.905645048442985,37.54320461007303],[55.906608607018505,37.545686966066306],[55.90788552162358,37.54743976120755],[55.90901557907218,37.55796999999999],[55.91059395704873,37.572711542327866],[55.91073854155573,37.57942799999998],[55.91009969268444,37.58502865872187],[55.90794809960554,37.58739968913264],[55.908713267595054,37.59131567193598],[55.902866854295375,37.612687423278814],[55.90041967242986,37.62348079629517],[55.898141151686396,37.635797880950896],[55.89639275532968,37.649487626983664],[55.89572360207488,37.65619302513125],[55.895295577183965,37.66294133862307],[55.89505457604897,37.66874564418033],[55.89254677027454,37.67375601586915],[55.8947775867987,37.67744661901856],[55.89450045676125,37.688347],[55.89422926332761,37.69480554232789],[55.89322256101114,37.70107096560668],[55.891763491662616,37.705962965606716],[55.889110234998974,37.711885134918205],[55.886577568759876,37.71682005026245],[55.88458159806678,37.7199315476074],[55.882281005794134,37.72234560316464],[55.8809452036196,37.72364385977171],[55.8809722706006,37.725371142837474],[55.88037213862385,37.727870902099546],[55.877941504088696,37.73394330422971],[55.87208120378722,37.745339592590376],[55.86703807949492,37.75525267724611],[55.859821640197474,37.76919976190188],[55.82962968399116,37.827835219574],[55.82575289922351,37.83341438888553],[55.82188784027888,37.83652584655761],[55.81612575504693,37.83809213491821],[55.81460347077685,37.83605359521481],[55.81276696067908,37.83632178569025],[55.811486181656385,37.838623105812026],[55.807329380532785,37.83912198147584],[55.80510270463816,37.839079078033414],[55.79940712529036,37.83965844708251],[55.79131399999368,37.840581150787344],[55.78000432402266,37.84172564285271]]] };
        
        app.mkad = new ymaps.Polygon(json.coordinates);
        app.mkad.options.set('visible', true);
        app.map.geoObjects.add(app.mkad);
    }

    function calculateDistanceFromMKAD(callback){
    	// ---

	        var startPoint = [55.6851,37.5646];

	        // Calculation end point
	        	var address = $('.modal-basket [name="address"]').val();

	        	ymaps.geocode(address, { results: 1 }).then(function (res) {
			        var firstGeoObject = res.geoObjects.get(0);
			        var endPoint = firstGeoObject.geometry.getCoordinates();

			        // Create route
			        	ymaps.route([startPoint,endPoint]).then(function (res) {
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
				                var outerObject = routeObjects.remove(objectsInMoscow).remove(boundaryObjects);

				                if( outerObject.getLength() > 0 ){
					                var this_coordinates = outerObject.get(0).geometry.getCoordinates();

					                ymaps.route([this_coordinates[0],endPoint]).then(function (res) {
					                	var deliverydistance = Math.round(res.properties._data.RouterRouteMetaData.length / 1000);
					                	$('.modal-basket [name="deliverydistance"]').val(deliverydistance);

					                	callback();
					                });
				                }
				                else { $('.modal-basket [name="deliverydistance"]').val('-1'); callback(); }
				            }
				        );
			        // ---
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
// ---