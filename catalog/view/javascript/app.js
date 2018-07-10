var app = {
  page: '',
  width: 0,
  height: 0,
  size: '',
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

				setTimeout(function(){ catalogProductSearch(); }, 500);
			// ---
		});
	// ---

	// Cart
		app.modals.basket = $('[data-remodal-id="modal-basket"]').remodal();
		app.modals.coupon = $('[data-remodal-id="modal-coupon"]').remodal();
		app.modals.privacy = $('[data-remodal-id="modal-privacy"]').remodal();


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
					if( app.search.flag == false && app.search.query.length > 2 ){

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