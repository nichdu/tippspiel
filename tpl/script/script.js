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
                $('#message').html(data).show();
                if (data == 'Erfolgreich eingeloggt.')
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
                $('#message').html(data).show();
                if (data.substr(0, 3) == 'Erf')
                {
                    $('#message').css('color', 'green');
                }
            }
        };
        $('#registerForm').ajaxSubmit(options).error(connectError());
        return false;
    });
});

function connectError()
{
    alert('Bei der Verbindung ist ein Fehler aufgetreten. Möglicherweise sind Sie nicht mit dem Internet ' +
        'verbunden. Bitte versuchen Sie es später noch einmal.');
}