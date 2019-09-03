$(document).ready(function () {
    $('#destination_check').change(function () {

        if (this.checked)
            for(i = 0; i < 11; ++i) {
                $('#destination_' + i ).fadeIn('slow');
            }
        else
            for(i = 0; i < 11; i++) {
                $('#destination_' + i ).fadeOut('slow');
            }

    }).change();
});

$(document).ready(function () {
    $('#timestamp_check').change(function () {

        if (this.checked)
            for(i = 0; i < 11; ++i) {
                $('#timestamp_' + i ).fadeIn('slow');
            }
        else
            for(i = 0; i < 11; i++) {
                $('#timestamp_' + i ).fadeOut('slow');
            }

    }).change();
});

$(document).ready(function () {
    $('#fan_check').change(function () {

        if (this.checked)
            for(i = 0; i < 11; ++i) {
                $('#fan_' + i ).fadeIn('slow');
            }
        else
            for(i = 0; i < 11; i++) {
                $('#fan_' + i ).fadeOut('slow');
            }

    }).change();
});

$(document).ready(function () {
    $('#temperature_check').change(function () {

        if (this.checked)
            for(i = 0; i < 11; ++i) {
                $('#temperature_' + i ).fadeIn('slow');
            }
        else
            for(i = 0; i < 11; i++) {
                $('#temperature_' + i ).fadeOut('slow');
            }

    }).change();
});

$(document).ready(function () {
    $('#keypad_check').change(function () {

        if (this.checked)
            for(i = 0; i < 11; ++i) {
                $('#keypad_' + i ).fadeIn('slow');
            }
        else
            for(i = 0; i < 11; i++) {
                $('#keypad_' + i ).fadeOut('slow');
            }

    }).change();
});

$(document).ready(function () {
    $('#switches_check').change(function () {

        if (this.checked)
            for(i = 0; i < 45; ++i) {
                $('#switches_' + i ).fadeIn('slow');
            }
        else
            for(i = 0; i < 45; i++) {
                $('#switches_' + i ).fadeOut('slow');
            }

    }).change();
});