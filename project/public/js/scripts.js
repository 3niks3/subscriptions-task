function processEmailErrors(failed, message, error_not_show = false)
{
    switch(true)
    {
        case(failed && !error_not_show):
            email_valid = false;
            $('p#email-error').text(message).show();
            $('button#subscription-submit').prop('disabled',true);
            break;
        case(!failed):
            email_valid = true;
            $('p#email-error').text('').hide();
            break;
    }
}

function processTermsErrors(failed, message, error_not_show = false)
{
    console.log(message);
    switch(true)
    {
        case(failed && !error_not_show):
            terms_valid = false;
            $('p#terms-error').text(message).show();
            $('button#subscription-submit').prop('disabled',true);
            break;
        case(!failed):
            terms_valid = true;
            $('p#terms-error').text('').hide();
            break;
    }
}