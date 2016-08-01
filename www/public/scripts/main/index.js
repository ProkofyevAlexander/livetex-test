(function($){

    var $form = $('#form');
    var $submitBtn = $('#submit');
    var $type = $('#type');
    var $textBox = $('#text-box');
    var $text = $('#text');

    var onSubmit = function (e) {
        if (e.isDefaultPrevented()) {
            // handle the invalid form...
        }
    };

    var onTypeChange = function () {
        if ($type.val() === 'text') {
            $textBox.show();
            $text.prop('required', true);
        }
        else {
            $textBox.hide();
            $text
                .removeProp('required')
                .removeAttr('required');
        }
        $form.validator('update');
    };

    $form.validator();
    $submitBtn.on('click', onSubmit);
    $type.on('change', onTypeChange);

})(jQuery);