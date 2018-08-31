$(function() {
	$('#mainTabs a:first').tab('show'); // Select first tab
	$('#langtabs').children().last().children('a').click();
	if (window.localStorage && window.localStorage['currentTab']) {
		$('.mainMenuTabs a[href="'+window.localStorage['currentTab']+'"]').tab('show');
	}
	if (window.localStorage && window.localStorage['currentSubTab']) {
		$('a[href="'+window.localStorage['currentSubTab']+'"]').tab('show');
	}
	$('.fadeInOnLoad').css('visibility','visible');
	$('.mainMenuTabs a[data-toggle="tab"]').click(function() {
		if (window.localStorage) {
			window.localStorage['currentTab'] = $(this).attr('href');
		}
	});
	$('a[data-toggle="tab"]:not(.mainMenuTabs a[data-toggle="tab"], .followup_tabs a[data-toggle="tab"])').click(function() {
		if (window.localStorage) {
			window.localStorage['currentSubTab'] = $(this).attr('href');
		}
	});
});

$(window).load(function(){
	$('#addPostModalBody').css('height', $(window).height() * 0.72 + 'px');
});
$(window).resize(function() {
	$('#addPostModalBody').css('height', $(window).height() * 0.72 + 'px')
});

$(window).load(function(){
	$('#addCategoryModalBody').css('height', $(window).height() * 0.72 + 'px');
});
$(window).resize(function() {
	$('#addCategoryModalBody').css('height', $(window).height() * 0.72 + 'px')
});


$(function() {
    var $typeSelector = $('#Checker');
    var $toggleArea = $('#blogs_tab');
	var $toggleArea2 = $('#frontend-dropdown');
	 if ($typeSelector.val() === 'yes') {
            $toggleArea.show(); 
			$toggleArea2.show();
        }
        else {
            $toggleArea.hide(); 
			$toggleArea2.hide();
        }
    $typeSelector.change(function(){
        if ($typeSelector.val() === 'yes') {
            $toggleArea.show(300); 
			$toggleArea2.show(300);
        }
        else {
            $toggleArea.hide(300); 
			$toggleArea2.hide(300);
        }
    });
});
 $(function() {
    var $typeSelector = $('#DisqusChecker');
    var $toggleArea = $('#DisqusOptions');
	 if ($typeSelector.val() === 'yes') {
            $toggleArea.show(); 
        }
        else {
            $toggleArea.hide(); 
        }
    $typeSelector.change(function(){
        if ($typeSelector.val() === 'yes') {
            $toggleArea.show(300); 
        }
        else {
            $toggleArea.hide(300); 
        }
    });
});
 $(function() {
    var $typeSelector = $('#MainImageChecker');
    var $toggleArea = $('#MainImageOptions');
	 if ($typeSelector.val() === 'yes') {
            $toggleArea.show(); 
        }
        else {
            $toggleArea.hide(); 
        }
    $typeSelector.change(function(){
        if ($typeSelector.val() === 'yes') {
            $toggleArea.show(300); 
        }
        else {
            $toggleArea.hide(300); 
        }
    });
});
 $(function() {
    var $typeSelector = $('#LinkChecker');
    var $toggleArea = $('#MainLinkOptions');
	 if ($typeSelector.val() === 'yes') {
            $toggleArea.show(); 
        }
        else {
            $toggleArea.hide(); 
        }
    $typeSelector.change(function(){
        if ($typeSelector.val() === 'yes') {
            $toggleArea.show(300); 
        }
        else {
            $toggleArea.hide(300); 
        }
    });
});
 $(function() {
    var $typeSelector = $('#FeaturedChecker');
    var $toggleArea = $('#FeaturedOptions');
	 if ($typeSelector.val() === 'yes') {
            $toggleArea.show(); 
        }
        else {
            $toggleArea.hide(); 
        }
    $typeSelector.change(function(){
        if ($typeSelector.val() === 'yes') {
            $toggleArea.show(300); 
        }
        else {
            $toggleArea.hide(300); 
        }
    });
});