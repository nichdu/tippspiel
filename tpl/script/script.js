/**
 * Skriptdatei zu Tippspiel
 * Copyright (c) 2012 Tjark Saul
 * All rights reserved
 */

var emailCorrect = false,
    userCorrect = false,
    passwordCorrect = false,
    passwordsMatch = false;

$(document).ready(function() {
    // Macht aus der Tippabgabe-Form eine Ajax-Form
    $('#tippSubmit').click(function(){
        var options = {
            success: function(data) {
                $('#message').html(data.message).show();
                if (data.error == 0)
                {
                    $('#message').css('color', 'green');
                }
            }
        };
        $('#tippabgabeForm').ajaxSubmit(options).error(connectError);
        return false;
    });

    // Tippabgabe-Form per Enter abschickbar
    $('#tippabgabeForm :input').keyup(function(event) {
        if (event.which == 13)
        {
            $('#tippSubmit').click();
        }
    });

    // Macht aus der Login-Form eine Ajax-Form
    $('#loginLink').click(function()
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
        $('#loginForm').ajaxSubmit(options).error(connectError);
        return false;
    });

    // Login-Form per Enter abschickbar
    $('#loginForm :input').keyup(function(event)
    {
        if (event.which == 13)
        {
            $('#loginLink').click();
        }
    });

    // Macht aus der Registrierungsform eine Ajax-Form
    $('#registerLink').click(function()
    {
        var options = {
            beforeSubmit: function() {
                var value = $('#register_wdh_input').val();
                var pwd = $('#register_pwd_input').val();
                passwordsMatch = value == pwd;
                if (!userCorrect) {
                    $('#message').html('Der eingegebene Benutzername ist nicht gültig.').show();
                    return false;
                }
                if (!emailCorrect) {
                    $('#message').html('Die eingegebene E-Mail-Adresse ist nicht gültig.').show();
                    return false;
                }
                if (!passwordCorrect) {
                    $('#message').html('Das eingegebene Passwort ist nicht gültig.').show();
                    return false;
                }
                if (!passwordsMatch) {
                    $('#message').html('Die eingegebenen Passwörter stimmen nicht überein.').show();
                    return false;
                }
            },
            success: function(data) {
                $('#message').html(data.message).show();
                if (data.error == 0)
                {
                    $('#message').css('color', 'green');
                }
            }
        };
        $('#registerForm').ajaxSubmit(options).error(connectError);
        return false;
    });

    // Registrier-Form per Enter abschickbar
    $('#registerForm :input').keyup(function(event)
    {
        if (event.keyCode == 13)
        {
            $('#registerLink').click();
        }
    });

    // Validiert die E-Mail-Adresse bei Eingabe
    $('#register_email_input').bind('textchange', function() {
        emailCorrect = checkEmailAddress($(this).val());
        if (checkEmailAddress($(this).val())) {
            $('#register_email_info').html('&#x2714;').css('color', 'green');
        } else {
            $('#register_email_info').html('&#x2716;').css('color', 'red');
        }
    });

    // Validiert den Benutzernamen bei Eingabe
    $('#register_username_input').bind('textchange', function() {
        var value = $(this).val();
        var regex = /^[A-Za-z]\w+$/;
        if (parseInt(value.length) >= 3 && regex.test(value)) {
            userCorrect = true;
            $('#register_username_info').html('&#x2714;').css('color', 'green');
        } else {
            userCorrect = false;
            $('#register_username_info').html('&#x2716;').css('color', 'red');
        }
    });

    // Validiert das Passwort bei Eingabe
    $('#register_pwd_input').bind('textchange', function() {
        var value = $(this).val();
        var regex = /^\S.*\S$/;
        if (parseInt(value.length) >= 8 && regex.test(value)) {
            passwordCorrect = true;
            $('#register_pwd_info').html('&#x2714;').css('color', 'green');
        } else {
            passwordCorrect = false;
            $('#register_pwd_info').html('&#x2716;').css('color', 'red');
        }
    });

    // Validiert die Passwortwiederholung bei Eingabe
    $('#register_wdh_input').bind('textchange', function() {
        var value = $(this).val();
        var pwd = $('#register_pwd_input').val();
        passwordsMatch = (value == pwd);
        if (value == pwd) {
            $('#register_wdh_info').html('&#x2714;').css('color', 'green');
        } else {
            $('#register_wdh_info').html('&#x2716;').css('color', 'red');
        }
    });

    $('#spieltagAuswahl').change(function()
    {
        window.location.href = '/tippabgabe/' + $('#spieltagAuswahl').val();
    });

    $('#spieltagAuswahlErgebnisse').change(function()
    {
        window.location.href = '/ergebnisse/' + $('#spieltagAuswahlErgebnisse').val();
    });
});

function connectError()
{
    alert('Bei der Verbindung ist ein Fehler aufgetreten. Möglicherweise sind Sie nicht mit dem Internet ' +
        'verbunden oder der Server konnte Ihre Anfrage nicht korrekt verarbeiten. Bitte versuchen Sie es ' +
        'später noch einmal. Sollte dieser Fehler über längere Zeit bestehen, informieren Sie bitte einen ' +
        'Administrator.');
}

function checkEmailAddress(value)
{
    return /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(value);
}