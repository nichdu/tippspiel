/**
 * Skriptdatei zu Tippspiel
 * Copyright (c) 2012 Tjark Saul
 * All rights reserved
 */

$(document).ready(function() {
    // Macht aus der Login-Form eine Ajax-Form
    $('#loginlink').click(function()
    {
        var options = {
            success:    function(data) {
                $('#message').html(data.message).show();
                if (data.error == 0)
                {
                    $('#message').css('color', 'green');
                    window.location.reload();
                }
            }
        };
        $('#loginForm').ajaxSubmit(options).error(connectError());
        return false;
    });

    // Macht aus der Registrierungsform eine Ajax-Form
    $('#registerLink').click(function()
    {
        var options = {
            success:    function(data) {
                $('#message').html(data.message).show();
                if (data.error == 0)
                {
                    $('#message').css('color', 'green');
                }
            }
        };
        $('#registerForm').ajaxSubmit(options).error(connectError());
        return false;
    });

    // Validiert die E-Mail-Adresse bei Eingabe
    $('#register_email_input').bind('textchange', function(event, previousText) {
        if (checkEmailAddress($(this).val())) {
            $('#register_email_info').html('&#x2714;').css('color', 'green');
        } else {
            $('#register_email_info').html('&#x2716;').css('color', 'red');
        }
    });

    // Validiert den Benutzernamen bei Eingabe
    $('#register_username_input').bind('textchange', function(event, previousText) {
        var value = $(this).val();
        var regex = /^[A-Za-z]\w+$/;
        if (parseInt(value.length) >= 3 && regex.test(value)) {
            $('#register_username_info').html('&#x2714;').css('color', 'green');
        } else {
            $('#register_username_info').html('&#x2716;').css('color', 'red');
        }
    });

    // Validiert das Passwort bei Eingabe
    $('#register_pwd_input').bind('textchange', function(event, previousText) {
        var value = $(this).val();
        var regex = /^\S.*\S$/;
        if (parseInt(value.length) >= 8 && regex.test(value)) {
            $('#register_pwd_info').html('&#x2714;').css('color', 'green');
        } else {
            $('#register_pwd_info').html('&#x2716;').css('color', 'red');
        }
    });

    // Validiert die Passwortwiederholung bei Eingabe
    $('#register_wdh_input').bind('textchange', function(event, previousText) {
        var value = $(this).val();
        var pwd = $('#register_pwd_input').val();
        if (value == pwd) {
            $('#register_wdh_info').html('&#x2714;').css('color', 'green');
        } else {
            $('#register_wdh_info').html('&#x2716;').css('color', 'red');
        }
    });
});

function connectError()
{
    alert('Bei der Verbindung ist ein Fehler aufgetreten. Möglicherweise sind Sie nicht mit dem Internet ' +
        'verbunden. Bitte versuchen Sie es später noch einmal.');
}

function checkEmailAddress(value)
{
    return /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(value);
}