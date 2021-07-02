
class DataValidator {

    validateEmail(value, rules)
    {
        let email_regex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

        //check if field has value
        if(($.inArray( "required", rules ) >= 0) && (!value || ( $.type( value ) &&  value.trim().length <= 0 ))){
            return {'failed': true, 'message': 'Email address is required'};
        }

        //check email pattern
        if(($.inArray( "email", rules ) >= 0) && !(email_regex.test( value )) ){
            return {'failed': true, 'message': 'Please provide a valid e-mail address'};
        }

        //check against Colombian email address
        if(($.inArray( "specEmailCheck", rules ) >= 0) && (value.split('.').pop() == 'co')){
            return {'failed': true, 'message': 'We are not accepting subscriptions from Colombia emails'};
        }

        //success
        return {'failed': false, 'message': ''};

    }

    validateTerms(value, rules)
    {
        //check if field has value
        if( ($.inArray( "required", rules ) >= 0) && !value){
            return {'failed': true, 'message': 'You must accept the terms and conditions'};
        }

        //success
        return {'failed': false, 'message': ''};
    }

}

window.dataValidator = new DataValidator();