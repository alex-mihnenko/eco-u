/*
* DD ScrollSpy Menu Script (c) Dynamic Drive (www.dynamicdrive.com)
* Last updated: Aug 1st, 14'
* Visit http://www.dynamicdrive.com/ for this script and 100s more.
*/

// Aug 1st, 14': Updated to v1.2, which supports showing a progress bar inside each menu item (except in iOS devices). Other minor improvements.

var DDScrollAnimation = 'linear';//'swing';
var DDScrollAnimationTime = 250;//500;

$('.qwe').on('DOMMouseScroll mousewheel', function(ev) {
    var $this = $(this),
        scrollTop = this.scrollTop,
        scrollHeight = this.scrollHeight,
        height = $this.height(),
        delta = (ev.type == 'DOMMouseScroll' ?
            ev.originalEvent.detail * -40 :
            ev.originalEvent.wheelDelta),
        up = delta > 0;

    var prevent = function() {
        ev.stopPropagation();
        ev.preventDefault();
        ev.returnValue = false;
        return false;
    }
    
    if (!up && -delta > scrollHeight - height - scrollTop) {
        // Scrolling down, but this will take us past the bottom.
        $this.scrollTop(scrollHeight);
        return prevent();
    } else if (up && delta > scrollTop) {
        // Scrolling up, but this will take us past the top.
        $this.scrollTop(0);
        return prevent();
    }
});



var elem_click_z23 = false;
var elem_click_z24 = false;

var AlphabeticScrollerProto = function() {
            var letterCount;
            var letterHeight = 59;
            var letterTotal = $('.qwe2 .qwe li').length - 1;
            var bottomMargin = 0;
            var obj = {
                initAlphabeticHeight: function() {
                    var res = false;
                    var $fcTop = $('.f-c_top');
                    var h = $fcTop.height() + 67;
                    var screenHeight = $(window).height();
                    h = screenHeight - h - 100 + 15 - bottomMargin;
                    letterCount = Math.ceil(h / letterHeight);
                    letterCount--;
                    if(letterCount < 2) {
                        letterCount = 2;
                    }
                    var newHeight = letterCount * letterHeight;
                    if($('.qwe2').height() !== newHeight) {
                        $('.qwe2').height(newHeight);
                        res = true;
                    }
                    return res;
                },
                scrollToAlphabeticLetter: function($sender) {
                    var midLetter = Math.ceil(letterCount / 2);
                    midLetter--;
                    var $scroller = $('.qwe2 .qwe');
                    var currentLetter;
                    if($sender !== undefined) {
                        currentLetter = $sender.parent().index();
                    } else {
                        currentLetter = $('.list-alphabetic a.selected').parent().index();
                    }
                    if(currentLetter <= midLetter) {
                        $scroller.stop().animate({scrollTop:0}, DDScrollAnimationTime, DDScrollAnimation, function() { 

                        });
                    } else if(currentLetter >= (letterTotal - midLetter)) {
                        var scrollHeight = letterHeight * (letterTotal - midLetter);
                        $scroller.stop().animate({scrollTop:scrollHeight}, DDScrollAnimationTime, DDScrollAnimation, function() { 

                        });
                    } else {
                        var scrollHeight = letterHeight * (currentLetter - midLetter);
                        $scroller.stop().animate({scrollTop:scrollHeight}, DDScrollAnimationTime, DDScrollAnimation, function() { 

                        });
                    }
                },
                refresh: function() {
                    $(window).trigger('scroll');
                }
            };
            obj.initAlphabeticHeight();
            obj.scrollToAlphabeticLetter();
            $(window).resize(function() {
                obj.initAlphabeticHeight();
                obj.scrollToAlphabeticLetter();
            });
            $(window).scroll(function(e) {
                var wH = $(window).height();
                var sH = $('html').height();
                if($('body').height() > sH) {
                    sH = $('body').height();
                }
                var sT = $('html').scrollTop() | $('body').scrollTop();
                var fH = $('footer').height() + 50;
                var dH = sH - sT - wH;
                if(dH < fH) {
                    bottomMargin = fH - dH;
                } else {
                    bottomMargin = 0;
                }
                if(obj.initAlphabeticHeight()) {
                    obj.scrollToAlphabeticLetter();
                }
            });

            return obj;
};
var alphabeticScroller = null;

var CatalogScrollerProto = function() {
            var letterCount;
            var letterHeight = 99;
            var letterTotal = $('.all-l_a2 .qwe li').length - 1;
            var bottomMargin = 0;
            var obj = {
                initCatalogHeight: function() {
                    var res = false;
                    var $fcTop = $('.f-c_top');
                    var h = $fcTop.height() + 67;
                    var screenHeight = $(window).height();
                    h = screenHeight - h - 100 + 15 - bottomMargin;
                    letterCount = Math.ceil(h / letterHeight);
                    letterCount--;
                    if(letterCount < 2) {
                        letterCount = 2;
                    }
                    var newHeight = letterCount * letterHeight;
                    if($('.all-l_a2').height() !== newHeight) {
                        $('.all-l_a2').height(newHeight);
                        res = true;
                    }
                    return res;
                },
                scrollToCatalogItem: function($sender) {
                    var midLetter = Math.ceil(letterCount / 2);
                    midLetter--;
                    var $scroller = $('.all-l_a2 .qwe');
                    var currentLetter;
                    if($sender !== undefined) {
                        currentLetter = $sender.parent().index();
                    } else {
                        currentLetter = $('.list-products a.selected2').parent().index();
                    }
                    if(currentLetter <= midLetter) {
                        $scroller.stop().animate({scrollTop:0}, DDScrollAnimationTime, DDScrollAnimation, function() { 

                        });
                    } else if(currentLetter >= (letterTotal - midLetter)) {
                        var scrollHeight = letterHeight * (letterTotal - midLetter);
                        $scroller.stop().animate({scrollTop:scrollHeight}, DDScrollAnimationTime, DDScrollAnimation, function() { 

                        });
                    } else {
                        var scrollHeight = letterHeight * (currentLetter - midLetter);
                        $scroller.stop().animate({scrollTop:scrollHeight}, DDScrollAnimationTime, DDScrollAnimation, function() { 

                        });
                    }
                },
                refresh: function() {
                    $(window).trigger('scroll');
                }
            };
            obj.initCatalogHeight();
            obj.scrollToCatalogItem();
            $(window).resize(function() {
                obj.initCatalogHeight();
                obj.scrollToCatalogItem();
            });
            $(window).scroll(function(e) {
                var wH = $(window).height();
                var sH = $('html').height();
                if($('body').height() > sH) {
                    sH = $('body').height();
                }
                var sT = $('html').scrollTop() | $('body').scrollTop();
                var fH = $('footer').height() + 50;
                var dH = sH - sT - wH;
                if(dH < fH) {
                    bottomMargin = fH - dH;
                } else {
                    bottomMargin = 0;
                }
                if(obj.initCatalogHeight()) {
//                console.log(bottomMargin);
                    obj.scrollToCatalogItem();
                }
            });

            return obj;
};
var catalogScroller = null;

if (!Array.prototype.filter){
  Array.prototype.filter = function(fun /*, thisp */){
    "use strict";
 
    if (this == null)
      throw new TypeError();
 
    var t = Object(this);
    var len = t.length >>> 0;
    if (typeof fun != "function")
      throw new TypeError();
 
    var res = [];
    var thisp = arguments[1];
    for (var i = 0; i < len; i++){
      if (i in t){
        var val = t[i]; // in case fun mutates this
        if (fun.call(thisp, val, i, t))
          res.push(val);
      }
    }
     return res;
  };
}

(function($){

	var defaults = {
		spytarget: window,
		scrolltopoffset: 115,
		scrollbehavior: 'smooth',
		scrollduration: DDScrollAnimationTime,
		highlightclass: 'selected',
		enableprogress: '',
		mincontentheight: 40
	}

	var isiOS = /iPhone|iPad|iPod/i.test(navigator.userAgent) // detect iOS devices

	function inrange(el, range, field){ // check if "playing field" is inside range
		var rangespan = range[1]-range[0], fieldspan = field[1]-field[0]
		if ( (range[0]-field[0]) >= 0 && (range[0]-field[0]) < fieldspan ){ // if top of range is on field
			return true
		}
		else{
			if ( (range[0]-field[0]) <= 0 && (range[0]+rangespan) > field[0] ){ // if part of range overlaps field
				return true
			}
		}
		return false
	}

	$.fn.ddscrollSpy = function(options){
		var $window = $(window)
		var $body=(window.opera)? (document.compatMode=="CSS1Compat"? $('html') : $('body')) : $('html,body')


		return this.each(function(){
			var o = $.extend({}, defaults, options)
			o.enableprogress = (isiOS)? '' : o.enableprogress // disable enableprogress in iOS
			var targets = [], curtarget = ''
			var cantscrollpastindex = -1 // index of target content that can't be scrolled past completely when scrollbar is at the end of the doc
			var $spytarget = $( o.spytarget ).eq(0)
			var spyheight = $spytarget.outerHeight()
			var spyscrollheight = (o.spytarget == window)? $body.get(0).scrollHeight : $spytarget.get(0).scrollHeight
			var $menu = $(this)
			var totaltargetsheight = 0 // total height of target contents
			function spyonmenuitems($menu){
				var $menuitems = $menu.find('a[href^="#"]')
				targets = []
				curtarget = ''
				totaltargetsheight = 0
				$menuitems.each(function(i){
					var $item = $(this)
					var $target = $( $item.attr('href') )
					var target = $target.get(0)
					var $progress = null // progress DIV that gets dynamically added inside menu A element if o.enableprogress enabled
					if ($target.length == 0) // if no matching links found
						return true
					$item
						.off('click.goto')
						.on('click.goto', function(e){
							if ( o.spytarget == window && (o.scrollbehavior == 'jump' || !history.pushState))
								window.location.hash = $item.attr('href')
							if (o.scrollbehavior == 'smooth' || o.scrolltopoffset !=0){
								var $scrollparent = (o.spytarget == window)? $body : $spytarget
								var addoffset = 1 // add 1 pixel to scrollTop when scrolling to an element to make sure the browser always returns the correct target element (strange bug)
								if (o.scrollbehavior == 'smooth' && (history.pushState || o.spytarget != window)){
									$scrollparent.stop().animate( {scrollTop: targets[i].offsettop + addoffset}, o.scrollduration, function(){
										if (o.spytarget == window && history.pushState){
											history.pushState(null, null, $item.attr('href'))
										}
									})
								}
								else{
									$scrollparent.prop('scrollTop', targets[i].offsettop + addoffset)
								}
								e.preventDefault()
							}
						})
					if (o.enableprogress){ // if o.enableprogress enabled
						if ($item.find('div.' + o.enableprogress).length == 0){ //if no progress DIV found inside menu item
							$item.css({position: 'relative', overflow: 'hidden'}) // add some required style to parent A element
							$('<div class="' + o.enableprogress + '" style="position:absolute; left: -100%" />').appendTo($item)
						}
						$progress = $item.find('div.' + o.enableprogress)
					}
					var targetoffset = (o.spytarget == window)? $target.offset().top : (target.offsetParent == o.spytarget)? target.offsetTop : target.offsetTop - o.spytarget.offsetTop
					targetoffset +=  o.scrolltopoffset
					var targetheight = ( parseInt($target.data('spyrange')) > 0 )? parseInt($target.data('spyrange')) : ( $target.outerHeight() || o.mincontentheight)
					var offsetbottom = targetoffset + targetheight
					if (cantscrollpastindex == -1 && offsetbottom > (spyscrollheight - spyheight)){ // determine index of first target which can't be scrolled past
						cantscrollpastindex = i
					}
					targets.push( {
                                            $menuitem: $item, 
                                            $des: $target, 
                                            get offsettop() {
                                                var val = (o.spytarget == window)? this.$des.offset().top : (this.$des.get(0).offsetParent == o.spytarget)? this.$des.get(0).offsetTop : this.$des.get(0).offsetTop - o.spytarget.offsetTop
                                                val += o.scrolltopoffset;
                                                return val;
                                            }, 
                                            get height() {
                                                var targetheight = ( parseInt(this.$des.data('spyrange')) > 0 )? parseInt(this.$des.data('spyrange')) : ( this.$des.outerHeight() || o.mincontentheight);
                                                return targetheight;
                                            }, 
                                            $progress: $progress, 
                                            index: i} )
//					var targetoffset = (o.spytarget == window)? $target.offset().top : (target.offsetParent == o.spytarget)? target.offsetTop : target.offsetTop - o.spytarget.offsetTop
//					targetoffset +=  o.scrolltopoffset
//					var targetheight = ( parseInt($target.data('spyrange')) > 0 )? parseInt($target.data('spyrange')) : ( $target.outerHeight() || o.mincontentheight)
//					var offsetbottom = targetoffset + targetheight
//					if (cantscrollpastindex == -1 && offsetbottom > (spyscrollheight - spyheight)){ // determine index of first target which can't be scrolled past
//						cantscrollpastindex = i
//					}
//					targets.push( {$menuitem: $item, $des: $target, offsettop: targetoffset, height: targetheight, $progress: $progress, index: i} )
				})
				if (targets.length > 0)
					totaltargetsheight = targets[targets.length-1].offsettop + targets[targets.length-1].height
			}

			function highlightitem(){
				if (targets.length == 0)
					return
				var prevtarget = curtarget
				var scrolltop = $spytarget.scrollTop()
				var cantscrollpasttarget = false
				var shortlist = targets.filter(function(el, index){ // filter target elements that are currently visible on screen
					return inrange(el, [el.offsettop, el.offsettop + el.height], [scrolltop, scrolltop + spyheight])
				})
				if (shortlist.length > 0){
					curtarget = shortlist.shift() // select the first element that's visible on screen
					if (prevtarget && prevtarget != curtarget)
						prevtarget.$menuitem.removeClass(o.highlightclass)
					if (!curtarget.$menuitem.hasClass(o.highlightclass)) { // if there was a previously selected menu link and it's not the same as current
						curtarget.$menuitem.addClass(o.highlightclass) // highlight its menu item
//						if (!elem_click_z23 && $('.all-l_a ul li a.selected').length) {
						if (!elem_click_z23 && $('.qwe2 li a.selected').length) {
                                                    
							$magicLine2 = $(".magic-line2");
//							$el6 = $('.all-l_a ul li a.selected');//curtarget.$menuitem;
							$el6 = $('.qwe2 li a.selected');//curtarget.$menuitem;
							leftPos2 = $el6.position().top;
							newWidth2 = $el6.parent().width();
							$magicLine2
								.data("origTop", leftPos2)
								.data("origWidth", newWidth2);
							$magicLine2.stop().animate({
								top: leftPos2,
								width: newWidth2
							});
                                                        if(alphabeticScroller === null) {
                                                            alphabeticScroller = new AlphabeticScrollerProto();
                                                        }
                                                        alphabeticScroller.scrollToAlphabeticLetter($el6);
						}
						
						if (!elem_click_z24 && $('.all-l_a2 ul li a.selected2').length) {
							$magicLine3 = $(".magic-line3");
							$el7 = $('.all-l_a2 ul li a.selected2');//curtarget.$menuitem;
//                                                        console.log($el7);
							leftPos3 = $el7.position().top;
							newWidth3 = $el7.parent().width();
							$magicLine3
								.data("origTop", leftPos3)
								.data("origWidth", newWidth3);
							$magicLine3.stop().animate({
								top: leftPos3,
								width: newWidth3
							});
                                                        if(catalogScroller === null) {
                                                            catalogScroller = new CatalogScrollerProto();
                                                        }
                                                        catalogScroller.scrollToCatalogItem($el7);
						}
						
					}
					if (curtarget.index >= cantscrollpastindex && scrolltop >= (spyscrollheight - spyheight)){ // if we're at target that can't be scrolled past and we're at end of document
						if (o.enableprogress){ // if o.enableprogress enabled
							for (var i=0; i<targets.length; i++){ // highlight everything
								targets[i].$menuitem.find('div.' + o.enableprogress).css('left', 0)
							}
						}
						curtarget.$menuitem.removeClass(o.highlightclass)
						curtarget = targets[targets.length-1]
						if (!curtarget.$menuitem.hasClass(o.highlightclass))
							curtarget.$menuitem.addClass(o.highlightclass)
						
						return
					}
					if (o.enableprogress){ // if o.enableprogress enabled
						var scrollpct = ((scrolltop-curtarget.offsettop) / curtarget.height) * 100
						curtarget.$menuitem.find('div.' + o.enableprogress).css('left', -100 + scrollpct + '%')
						for (var i=0; i<targets.length; i++){
							if (i < curtarget.index){
								targets[i].$menuitem.find('div.' + o.enableprogress).css('left', 0)
							}
							else if (i > curtarget.index){
								targets[i].$menuitem.find('div.' + o.enableprogress).css('left', '-100%')
							}
						}
					}
				}
				else if (scrolltop > totaltargetsheight){ // if no target content visible on screen but scroll bar has scrolled past very last content already
					if (o.enableprogress){ // if o.enableprogress enabled
						curtarget.$menuitem.removeClass(o.highlightclass)
						for (var i=0; i<targets.length; i++){
							targets[i].$menuitem.find('div.' + o.enableprogress).css('left', 0)
						}
					}
				}
			}

			function updatetargetpos(){
				if (targets.length == 0)
					return
				var $menu = targets[0].$menu
				spyheight = $spytarget.outerHeight()
				spyscrollheight = (o.spytarget == window)? $body.get(0).scrollHeight : $spytarget.get(0).scrollHeight
				totaltargetsheight = 0
				cantscrollpastindex = -1
				for (var i = 0; i < targets.length; i++){
					var $target = targets[i].$des
					var target = $target.get(0)
					var targetoffset = (o.spytarget == window)? $target.offset().top : (target.offsetParent == o.spytarget)? target.offsetTop : target.offsetTop - o.spytarget.offsetTop
					targetoffset += o.scrolltopoffset
					targets[i].offsettop = targetoffset
					targets[i].height = ( parseInt($target.data('spyrange')) > 0 )? parseInt($target.data('spyrange')) : ( $target.outerHeight() || o.mincontentheight)
					if (o.enableprogress){ // if o.enableprogress enabled
						var offsetbottom = targetoffset + targets[i].height // recalculate cantscrollpastindex
						if (cantscrollpastindex == -1 && offsetbottom > (spyscrollheight - spyheight)){
							cantscrollpastindex = i
						}
					}
				}
				totaltargetsheight = targets[targets.length-1].offsettop + targets[targets.length-1].height
			}

			spyonmenuitems($menu)

			$menu.on('updatespy', function(){
				spyonmenuitems($menu)
				highlightitem()
			})

			$spytarget.on('scroll resize', function(){
				highlightitem()
			})

			highlightitem()

			$window.on('load resize', function(){
				updatetargetpos()
			})

		}) // end return
	}

})(jQuery);