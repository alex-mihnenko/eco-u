/*
 * jQuery liTabs v 2.1 (14.08.13)
 *
 * Copyright 2012, Linnik Yura | LI MASS CODE | http://masscode.ru
 * http://masscode.ru/index.php/k2/item/43-litabs
 * Free to use
 * 
 */
(function($){
	$.fn.liTabs = function(params){
		var p = $.extend({
			duration: 500,	// A string or number determining how long the animation will run
			effect:'clasic'	//simple, fade, hSlide, vSlide
		}, params);
		return this.each(function(){
			
			var 
			tWrap = $(this),
			tItem = tWrap.children('.t_item'),
			tContents = tWrap.children('.t_item').children('.t_content'),
			tLinks = tWrap.children('.t_item').children('.t_link');
			
			$('<div>').addClass('t_include').appendTo(tWrap)
			
			var 
			tInclude = tWrap.children('.t_include'),
			tCur = tWrap.children('.t_item').children('a:first');
			if(tWrap.children('.t_item').children('.cur').length){
				tCur = tWrap.children('.t_item').children('.cur');
			}
			tCur.addClass('cur');
			
			tCur.parent().children('.t_content').show();
			tItem.each(function(){
				
				var 
				tItemEl = $(this),
				tCont = tItemEl.children('.t_content').appendTo(tInclude),
				tLink = tItemEl.children('.t_link');
				
				tLink.on('click',function(){
					if(!$(this).is('.cur')){
						tLinks.removeClass('cur').filter(this).addClass('cur');
						if(p.effect == 'clasic'){
							tContents.hide().filter(tCont).show();
						};
						if(p.effect == 'fade'){
							tContents.fadeOut(p.duration).filter(tCont).fadeIn(p.duration);
						};
						if(p.effect == 'hSlide'){
							tContents.stop().animate({left:'-10%',opacity:'0'},p.duration,function(){
								$(this).hide();	
							}).filter(tCont).stop().css({left:'10%'}).show().animate({left:'0',opacity:'1'},p.duration);	
						};
						if(p.effect == 'vSlide'){
							tContents.stop().animate({top:'30px', opacity:'0'},p.duration,function(){
								$(this).hide();	
							}).filter(tCont).stop().css({top:'-30px'}).show().animate({top:'0',opacity:'1'},p.duration);	
						};
					};
					return false;
				});
				$(".c-m_txt").on("click",function(){
					$('.liTabs_2 .t_link:last-child').trigger( "click" );
					//if(!$(this).closest('liTabs').find(".t_link").is('.cur')){
					//	tLinks.removeClass('cur').filter(this).addClass('cur');
					//	if(p.effect == 'clasic'){
					//		tContents.hide().filter(tCont).show();
					//	};
					//};
					return false;
				});
				
				
				
			});
		});
	};
})(jQuery);