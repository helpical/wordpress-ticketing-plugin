import $ from 'jquery';
import select2 from 'select2';
let ids = [];
let ticket;

$(document).ready(function() {
    if ($('.helpical-1').length > 0)
        getTickets();
    new select2();
    $(".--helpical-ticket-departement").select2({
        dir: "rtl",
        placeholder: "انتخاب دپارتمان یا گروه",
        width: "100%"
    });
    $(".--helpical-ticket-category").select2({
        dir: "rtl",
        width: "100%"
    });
    // $(".helpical-ticket-statusSelect").select2({
    //     dir: "rtl",
    //     width: "100%"
    // });
});

$(document).on('click', '.--helpical-show-ticket', function(e) {
    e.preventDefault();
    helpicalScroll();
    $('.helpical-1').fadeOut('slow', function() {
        $('.helpical-loading').fadeIn('slow');
    });
    ticket = ids[parseInt($(this).parents('tr').data('id'))];
    getTicket();
});

$(document).on('click', '.--helpical-btn-back', function(e) {
    e.preventDefault();
    helpicalScroll();
    $('.helpical-3').fadeOut('slow', function() {
        $('.helpical-loading').fadeIn();
    });
    getTickets();
});

$(document).on('select2:select', '.--helpical-ticket-departement', function() {
    let selected = $('.--helpical-ticket-departement').find(':selected');
    let categories = JSON.parse(selected.data('categories').replaceAll("'", '"'));
    let cate = $('.--helpical-ticket-category');
    cate.html('');
    categories.forEach(element => {
        let newOption = new Option(element.title, element.id, false, false);
        cate.append(newOption);
    });
    let newOption = new Option($('.--helpical-ticket-category').data('others'), 1, true, true);
    cate.append(newOption);
});

$('.--helpical-ticket-departement').trigger('select2:select');

$(document).on('click', '.--helpical-newTicket-important-icon', function() {
    $('.--helpical-newTicket-important-icon').removeClass('active');
    $(this).addClass('active');
    $('.--helpical-importance-radio').removeAttr('checked');
    $('.--helpical-importance-radio[value=' + $(this).data('value') + ']').attr('checked', true);
});

$(document).on('click', '.--helpical-send-ticket', function() {
    helpicalScroll();
    $('.helpical-2').fadeOut('slow', function() {
        $('.helpical-loading').fadeIn('slow');
    })
    sendTicket();
});

$(document).on('click', '.--helpical-reply-ticket', function() {
    helpicalScroll();
    $('.helpical-3').fadeOut('slow', function() {
        $('.helpical-loading').fadeIn('slow');
    })
    replyTicket();
});

$(document).on('click', '.--helpical-btn-new-ticket', function() {
    helpicalScroll();
    $('.helpical-1').fadeOut('slow', function() {
        $('.helpical-2').fadeIn('slow');
    });
});

$(document).on('click', '.--helpical-btn-arshive', function() {
    helpicalScroll();
    $('.helpical-2').fadeOut('slow', function() {
        $('.helpical-1').fadeIn();
    });
});

$(document).on('mouseover', '.--helpical-tooltip-owner', function() {
    var tooltip = $(this).attr('data-tooltip');
    var tooltipContainer = `<div class="--helpical-tooltip-text"><p class="--helpical-m-0 --helpical-tooltip-text-icon">${tooltip}</p></div>`;
    $(this).append(tooltipContainer);
});

$(document).on('click', '.--helpical-tooltip-owner', function() {
    let tooltip = $(this).find('.--helpical-tooltip-text');
    if (tooltip.length > 0)
        tooltip.remove();
    else {
        var tooltipText = $(this).attr('data-tooltip');
        var tooltipContainer = `<div class="--helpical-tooltip-text"><p class="--helpical-m-0 --helpical-tooltip-text-icon">${tooltipText}</p></div>`;
        $(this).append(tooltipContainer);
    }
});

$(document).on('mouseout', '.--helpical-tooltip-owner', function() {
    $(this).find('.--helpical-tooltip-text').remove();
});

$(document).on('change', '.--helpical-ticket-attachments-input', function() {
    let files = this.files;
    let count = files.length;
    let flag = true;
    for (let i = 0; i < count; i++) {
        let format = files[i].name.substr(files[i].name.lastIndexOf('.') + 1, files[i].name.length);
        if (helpical.attachmentsFormat.indexOf(files[i].type) == -1 && format != 'zip' && format != 'rar') {
            flag = false;
        } else if (parseInt(helpical.attachmentsSize) < parseInt(files[i].size))
            flag = false;
    }
    if (!flag) {
        $(this).val(null);
        $('.--helpical-attachments-note').addClass('--helpical-text-danger');
        $('.--helpical-attachments-note').removeClass('--helpical-text-muted');
        setTimeout(() => {
            $('.--helpical-attachments-note').addClass('--helpical-text-muted');
            $('.--helpical-attachments-note').removeClass('--helpical-text-danger');
        }, 2000);
    } else {
        if (count > 1) {
            $(this).parent().children(".--helpical-ticket-attachments-name").text(count + ' selected files');
        } else {
            $(this).parent().children(".--helpical-ticket-attachments-name").text(files[0].name);
        }
        $(this).parent(".--helpical-overflow-hidden").css("border", "1px solid var(--primary)");
    }
});

$(document).on('change', '.--helpical-reply-attachments-input', function() {
    let files = this.files;
    let count = files.length;
    let flag = true;
    for (let i = 0; i < count; i++) {
        let format = files[i].name.substr(files[i].name.lastIndexOf('.') + 1, files[i].name.length);
        if (helpical.attachmentsFormat.indexOf(files[i].type) == -1 && format != 'zip' && format != 'rar') {
            flag = false;
        } else if (parseInt(helpical.attachmentsSize) < parseInt(files[i].size))
            flag = false;
    }
    if (!flag) {
        $(this).val(null);
        $('.--helpical-attachments-note').addClass('--helpical-text-danger');
        $('.--helpical-attachments-note').removeClass('--helpical-text-muted');
        setTimeout(() => {
            $('.--helpical-attachments-note').addClass('--helpical-text-muted');
            $('.--helpical-attachments-note').removeClass('--helpical-text-danger');
        }, 2000);
    } else {
        if (count > 1) {
            $(this).parent().children(".--helpical-ticket-attachments-name").text(count + ' selected files');
        } else {
            $(this).parent().children(".--helpical-ticket-attachments-name").text(files[0].name);
        }
        $(this).parent(".--helpical-overflow-hidden").css("border", "1px solid var(--primary)");
    }
});

$(document).on('click', '.--helpical-ticket-attachments-btn', function() {
    $('.--helpical-ticket-attachments-input').trigger("click");
});

$(document).on('click', '.--helpical-reply-attachments-btn', function() {
    $('.--helpical-reply-attachments-input').trigger("click");
});

$(document).on('click', '.--helpical-ticket-send-cancel', function() {
    $(".--helpical-ticket-send").slideUp("slow", () => {
        $(".--helpical-Ticket-status").slideDown("slow")
    });
});

$(document).on('click', '.--helpical-ticket-send-open', function() {
    $(".--helpical-Ticket-status").slideUp("slow", () => {
        $(".--helpical-ticket-send").slideDown("slow");
    })
});

$(document).on('click', '.--helpical-refresh', function() {
    helpicalScroll();
    $('.helpical-1').fadeOut('slow', function() {
        $('.helpical-loading').fadeIn();
    });
    getTickets();
});

$(document).on('click', '.--helpical-close-ticket', function() {
    if (confirm('آیا از بستن این تیکت اطمینان دارید؟')) {
        helpicalScroll();
        $('.helpical-3').fadeOut('slow', function() {
            $('.helpical-loading').fadeIn('slow');
        });
        closeTicket();
    }
});

$(document).on('click', '.--helpical-satisfaction', function() {
    $('.--helpical-satisfaction-container').css('opacity', '0.4');
    sendSatisfaction($(this).data('value'), $(this));
});

function errorTemp(message) {
    $('.--helpical-problem').remove();
    return `
    <div class="--helpical-alert --helpical-alert-danger --helpical-problem" role="alert">
        ${message}
    </div>
    `;
}

function helpicalScroll() {
    $('html, body').animate({
        scrollTop: $('.--helpical').offset().top - 10
    }, 700);
}

function getTickets() {
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: helpical.url + '/getTickets',
        headers: {
            'X-WP-Nonce': helpical.wpnonce
        },
        data: {
            nonce: helpical.helpicalnonce,
        },
        success: function(response) {
            helpicalScroll();
            $('.helpical-loading').fadeOut('slow', function() {
                if (response['status'] == 'error' || response['status'] == 'failed') {
                    $('.--helpical').append(errorTemp(response['msg']));
                } else {
                    ids = response['ids'];
                    $('.helpical-1').html(response['data']);
                    $('.helpical-1').fadeIn('slow');
                }
            });
        },
        error: function(error) {
            helpicalScroll();
            $('.helpical-loading').fadeOut('slow', function() {
                $('.--helpical').append(errorTemp('Java Script Error'));
            });
        }
    });
}

function getTicket() {
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: helpical.url + '/getTicket',
        headers: {
            'X-WP-Nonce': helpical.wpnonce
        },
        data: {
            nonce: helpical.helpicalnonce,
            id: ticket
        },
        success: function(response) {
            helpicalScroll();
            $('.helpical-loading').fadeOut('slow', function() {
                if (response['status'] == 'error' || response['status'] == 'failed') {
                    $('.--helpical').append(errorTemp(response['msg']));
                } else {
                    $('.helpical-3').html(response['data']);
                    $('.helpical-3').fadeIn('slow');
                }
            });
        },
        error: function(error) {
            helpicalScroll();
            $('.helpical-loading').fadeOut('slow', function() {
                $('.--helpical').append(errorTemp('Java Script Error'));
            });
        }
    });
}

function sendTicket() {
    let formdata = new FormData();
    let attachments = $('.--helpical-ticket-attachments-input')[0].files;
    formdata.append('nonce', helpical.helpicalnonce);
    formdata.append('ticket_cat', $('.--helpical-ticket-category').find(':selected').val());
    formdata.append('target_department_id', $('.--helpical-ticket-departement').find(':selected').val());
    formdata.append('importance', $('.--helpical-importance-radio[checked]').val());
    formdata.append('subject', $('.--helpical-ticket-title-input').val());
    formdata.append('message', $('.--helpical-ticket-message-input').val());
    for (let i = 0; i < attachments.length; i++)
        formdata.append("attachments[]", attachments[i]);
    $.ajax({
        type: 'POST',
        url: helpical.url + '/newTicket',
        data: formdata,
        processData: false,
        contentType: false,
        headers: {
            'X-WP-Nonce': helpical.wpnonce
        },
        success: function(response) {
            if (response['status'] == 'success') {
                ticket = response['data']['ticket_id'];
                $('.--helpical-ticket-title-input').val('');
                $('.--helpical-ticket-message-input').val('');
                getTicket();
            } else {
                $('.--helpical-form-new-ticket-message').html(errorTemp(response['msg']));
                helpicalScroll();
                $('.helpical-loading').fadeOut('slow', function() {
                    $('.helpical-2').fadeIn('slow');
                });
            }
        },
        error: function(error) {
            helpicalScroll();
            $('.helpical-loading').fadeOut('slow', function() {
                $('.--helpical').append(errorTemp('Java Script Error'));
            });
        }
    });
}

function closeTicket() {
    $.ajax({
        type: 'PUT',
        dataType: 'json',
        url: helpical.url + '/closeTicket',
        headers: {
            'X-WP-Nonce': helpical.wpnonce
        },
        data: {
            nonce: helpical.helpicalnonce,
            ticket_id: ticket
        },
        success: function(response) {
            if (response['status'] == 'error' || response['status'] == 'failed') {
                $('.--helpical-form-close-ticket-message').html(errorTemp(response['msg']));
                helpicalScroll();
                $('.helpical-loading').fadeOut('slow', function() {
                    $('.helpical-3').fadeIn('slow');
                });
            } else {
                getTicket();
            }
        },
        error: function(error) {
            helpicalScroll();
            $('.helpical-loading').fadeOut('slow', function() {
                $('.--helpical').append(errorTemp('Java Script Error'));
            });
        }
    });
}

function replyTicket() {
    let formdata = new FormData();
    let attachments = $('.--helpical-reply-attachments-input')[0].files;
    formdata.append('nonce', helpical.helpicalnonce);
    formdata.append('ticket_id', ticket);
    formdata.append('message', $('.--helpical-reply-message-input').val());
    for (let i = 0; i < attachments.length; i++)
        formdata.append("attachments[]", attachments[i]);

    $.ajax({
        type: 'POST',
        url: helpical.url + '/replyTicket',
        processData: false,
        contentType: false,
        data: formdata,
        headers: {
            'X-WP-Nonce': helpical.wpnonce
        },
        success: function(response) {
            if (response['status'] == 'success') {
                getTicket();
            } else {
                $('.--helpical-reply-ticket-message').html(errorTemp(response['msg']));
                helpicalScroll();
                $('.helpical-loading').fadeOut('slow', function() {
                    $('.helpical-3').fadeIn('slow');
                });
            }
        },
        error: function(error) {
            helpicalScroll();
            $('.helpical-loading').fadeOut('slow', function() {
                $('.--helpical').append(errorTemp('Java Script Error'));
            });
        }
    });
}

function sendSatisfaction(satisfaction, context) {
    $.ajax({
        type: 'PUT',
        dataType: 'json',
        url: helpical.url + '/satisfactionTicket',
        headers: {
            'X-WP-Nonce': helpical.wpnonce
        },
        data: {
            nonce: helpical.helpicalnonce,
            ticket_id: ticket,
            satisfaction: satisfaction
        },
        success: function(response) {
            if (response['status'] == 'error' || response['status'] == 'failed') {
                $('.--helpical-form-close-ticket-message').html(errorTemp(response['msg']));
                $('.--helpical-satisfaction-container').css('opacity', '1');
            } else {
                $('.--helpical-satisfaction').removeClass('active');
                context.addClass('active');
                $('.--helpical-satisfaction-text').text(response['data']['msg']);
                $('.--helpical-satisfaction-container').css('opacity', '1');
            }
        },
        error: function(error) {
            $('.--helpical').append(errorTemp('Java Script Error'));
            $('.helpical-3').fadeOut('slow');
        }
    });
}