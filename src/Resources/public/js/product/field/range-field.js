'use strict';

/**
 * Range field.
 *
 * @author Damien Carcel <damien.carcel@akeneo.com>
 */
define (
    [
        'pim/field',
        'underscore',
        'text!pimextendedattributetype/template/product/field/range'
    ], function (
        Field,
        _,
        fieldTemplate
    ) {
        return Field.extend({
            fieldTemplate: _.template(fieldTemplate),
            events: {
                'change .field-input:first .fromData, .field-input:first .toData': 'updateModel'
            },
            renderInput: function (context) {
                return this.fieldTemplate(context);
            },
            setFocus: function () {
                this.$('.min:first').focus();
            },
            updateModel: function () {
                var fromData = this.$('.field-input:first .min').val();
                var toData   = this.$('.field-input:first .max').val();

                this.setCurrentValue({
                    min: '' !== fromData ? fromData : null,
                    max: '' !== toData ? toData : null
                });
            }
        });
    }
);
