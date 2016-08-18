'use strict';

/**
 * Range field.
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
define (
    [
        'pim/field',
        'underscore',
        'text!pimextendedattributetype/templates/product/field/range'
    ], function (
        Field,
        _,
        fieldTemplate
    ) {
        return Field.extend({
            fieldTemplate: _.template(fieldTemplate),
            events: {
                'change .field-input:first .min, .field-input:first .max': 'updateModel'
            },
            renderInput: function (context) {
                return this.fieldTemplate(context);
            },
            setFocus: function () {
                this.$('.min:first').focus();
            },
            updateModel: function () {
                var min = this.$('.field-input:first .min').val();
                var max = this.$('.field-input:first .max').val();

                this.setCurrentValue({
                    min: '' !== min ? min : null,
                    max: '' !== max ? max : null
                });
            }
        });
    }
);
