(function ($, window) {

    $(document).ready(function () {
        
        var hostname = window.location.hostname.replace('www.', '');
        $('a').each(function (index, value) {
            if (value.hostname !== hostname) {
                $(this).attr('target', '_blank');
            }
        });

        $(window).bind('beforeunload', function (e) {
            var clean = true;
            $('form').each(function () {
                var $form = $(this);
                if ($form.data('dirty')) {
                    clean = false;
                }
            });
            if (!clean) {
                var message = 'You have unsaved changes.';
                e.returnValue = message;
                return message;
            }
        });

        $('form').each(function () {
            var $form = $(this);
            $form.data('dirty', false);
            $form.on('change', function () {
                $form.data('dirty', true);
            });
            $form.on('submit', function () {
                $(window).unbind('beforeunload');
            });
        });
    });

})(jQuery, window);
