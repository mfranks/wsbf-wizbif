/** David Cohen/Kevin Haag - Spring 2012
 *	called by show_sup_add.php
 *	enables:
 *		- the fancy autocomplete (with support for multiple names)
 *		- the time picker
 *		- adding shows via AJAX
 */

/** on load **/

$(function() {
    $(".success").hide();
    $("#addShowSub").validate({
        rules: {
            name: "required",
            start_time: "required",
            end_time: "required",
            date: "required"

        },
        messages: {
            name: "Who's show is being subbed?",
            start_time: "What time does this show start?",
            end_time: "What time does this show end?",
            date: "What day do you need a sub?"
        },

        submitHandler: function(form) {
            //			form.submit();
            event.preventDefault();
            $('.error').hide();

            $.ajax({
                type: 'POST',
                url: 'show_sub_submit.php',
                data: $("#addShowSub").serialize(),
                success: function(html) {
				    window.location.href = '/wizbif/show_sub/show_sub.php';
                    $("#successMessage").show('fast',
                    function() {
                        $(this).replaceWith('<div id="successMessage" class="success"><b>Success: ' + html + '</div>');
                    });
                    $("#addShowSub").clearForm();
                    return false;
                }
            });
        }

    });


    /*** CLEAR FORM FUNCTION **/
    $.fn.clearForm = function() {
        return this.each(function() {
            var type = this.type,
            tag = this.tagName.toLowerCase();
            if (tag == 'form')
            return $(':input', this).clearForm();
            if (type == 'text' || type == 'password' || tag == 'textarea' || type == 'hidden')
            this.value = '';
            //	    else if (type == 'checkbox' || type == 'radio')
            //	      this.checked = false;
            else if (tag == 'select')
            this.selectedIndex = 0;
            // -1
        });
    };

    function split(val) {
        return val.split(/,\s*/);
    }
    function extractLast(term) {
        return split(term).pop();
    }
    // don't navigate away from the field on tab when selecting an item
    $("#names").bind("keydown", function(event) {
       	if (event.keyCode === $.ui.keyCode.TAB &&
       		$(this).data("autocomplete").menu.active) {
           		event.preventDefault();
        }
    });

    /*** Time Entry Stuff ***/
    $('#timeFrom').timeEntry({
        timeSteps: [1, 30, 0],
        defaultTime: "11:00 AM"
    });

    $('#timeTo').timeEntry({
        timeSteps: [1, 30, 0],
        beforeShow: guessEndTime
    });

    // automatically
    function guessEndTime() {
        var timeFrom = $('#timeFrom').timeEntry('getTime');
        if ((timeFrom.getHours() >= 11 && timeFrom.getHours() < 16)
        || (timeFrom.getHours() >= 1 && timeFrom.getHours() < 5)) {
            timeFrom.setMinutes(timeFrom.getMinutes() + 90);
        }
        else {
            timeFrom.setHours(timeFrom.getHours() + 2);
        }

        return {
            defaultTime: timeFrom
        };
    }

    //	$(selector).timeEntry();
    /*

*/


});
// end onload
