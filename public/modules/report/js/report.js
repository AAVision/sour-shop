"use strict";
(function ($) {
    $(document).ready(function () {
        $("#datePicker").datepicker();
        $("#datePicker2").datepicker();
    });
})(jQuery);

function printReport() {
    $("#printSection").print({
        globalStyles: true,
        title: "Report Print",
        doctype: "<!doctype html>",
        removeEmpty: true,
    });
}




