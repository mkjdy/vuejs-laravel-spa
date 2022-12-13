import { extend } from "vee-validate";
import {
    required,
    email,
    max,
    min,
    confirmed,
    alpha_spaces,
    alpha_num,
    numeric,
    size,
    regex,
    digits,
    double
} from "vee-validate/dist/rules";

extend("size", {
    ...size,
    message: "Avatar size should be less than 3 MB!"
});

extend("required", {
    ...required,
    message: "This field is required"
});

extend("max", {
    ...max,
    message: "This field must be {length} characters or less"
});

extend("min", {
    ...min,
    message: "This field must be {length} characters or more"
});

extend("email", {
    ...email,
    message: "This field must be a valid email"
});

extend("confirmed", {
    ...confirmed,
    message: "The value for {_field_} field does not match"
});

extend("password", {
    params: ["target"],
    validate(value, { target }) {
        return value === target;
    },
    message: "Password confirmation does not match"
});

extend("alpha_spaces", {
    ...alpha_spaces,
    message: "Suffix may only contain alphabetic characters"
});

extend("numeric", {
    ...numeric,
    message: "This field must only contain numerical values"
});

extend('length', {
    validate(value, { char_length }) {
        return value.length >= char_length;
    },
    params: ['char_length'],
    message: 'This field must have at least {char_length} characters'
})

extend('alpha_num', {
    ...alpha_num,
    message: "This field may only contain alpha-numeric characters"
});

//setInteractionMode('eager')

extend('digits', {
...digits,
message: '{_field_} needs to be {length} digits. ({_value_})',
})

/*extend('required', {
...required,
message: '{_field_} can not be empty',
})

extend('max', {
...max,
message: '{_field_} may not be greater than {length} characters',
})*/

extend('regex', {
...regex,
message: '{_field_} {_value_} does not match {regex}',
})

/*extend('email', {
...email,
message: 'Email must be valid',
})*/

extend('double', {
    ...double,
    // message: "This field only contain decimal"
    message: (fieldName, placeholders) => {
        return `This field must be a valid decimal` + (placeholders.decimals > 0 ? ` with ${placeholders.decimals} decimal places` : '')
    }
});

extend('minmax', {
    validate(value, { min, max }) {
        return Number(value) >= Number(min) && Number(value) <= Number(max)
    },
    params: ['min', 'max'],
    message: '{_field_} must be at least {min} and {max} at most'
})