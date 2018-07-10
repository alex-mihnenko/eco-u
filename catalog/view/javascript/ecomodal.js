var ecoModal = function() {
    var stack = 0;
    $('body').delegate('[data-ecomodal-target]', 'click', function(e) {
        e.preventDefault();
        var $this = $(this);
        var $modal = $('#' + $this.data('ecomodal-target'));
        if(!$modal.length) {
            return;
        }
        if($modal.hasClass('ecomodal-opened')) {
            return;
        }
        var $overlay = $('.ecomodal-overlay');
        if(!$overlay.length) {
            $overlay = $('<div class="ecomodal-overlay"></div>');
            $('body').append($overlay);
        }
        var $parent = $modal.parent();
        if(!$parent.hasClass('ecomodal-wrapper')) {
            $parent = $('<div class="ecomodal-wrapper"></div>');
            $('body').append($parent);
            $parent.append($modal.detach());
        }
        $parent.css('z-index', stack + 20000);
        if(!$overlay.is(':visible')) {
            $overlay.fadeIn('fast');
        }
        var hideEcoModal = function() {
            $parent.fadeOut('fast', function() {
                $modal.removeClass('ecomodal-opened');
                $modal.trigger('ecomodal.onhide');
            });
            stack--;
            if(stack <= 0) {
                stack = 0;
                $overlay.fadeOut('fast');
            }
        };
        if(!$parent.is(':visible')) {
            $modal.addClass('ecomodal-opened');
            $parent.fadeIn('fast');
            $modal.unbind('ecomodal.hide');
            $modal.bind('ecomodal.hide', function() {
                hideEcoModal();
            });
            stack++;
        }
        $modal.find('[data-ecomodal-action]').click(function(e) {
            e.preventDefault();
            hideEcoModal();
            return false;
        });
        $parent.click(function(e) {
            if(!$(e.target).hasClass('ecomodal-wrapper')) {
                return;
            }
            hideEcoModal();
        });


        return false;
    });
}();
