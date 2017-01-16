'use strict';
/**
 * URL collection field
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define(
    [
        'pim/field',
        'underscore',
        'jquery',
        'text!pim-extended-attribute-type/templates/product/field/url-collection'
    ],
    function (Field,
              _,
              $,
              fieldTemplate) {
        return Field.extend({
            fieldTemplate: _.template(fieldTemplate),
            renderInput: function (context) {
                return this.fieldTemplate(context);
            },
            postRender: function () {
                var $fieldInput = this.$('.field-input:first');
                var $tableBody = $fieldInput.find('tbody');
                var self = this;

                $fieldInput.find('.pim-extended-attribute-string-collection-add').click(function () {
                    this.addRow();
                }.bind(this));

                $tableBody
                    .on('change', '.pim-extended-attribute-string-collection-field', this.updateModel.bind(this))
                    .on('click', 'button', function () {
                        $(this).closest('tr').remove();
                        self.updateModel();

                        return false;
                    })
                    .sortable({
                        axis: 'y',
                        cursor: 'move',
                        handle: '.icon-reorder',
                        update: this.updateModel.bind(this),
                        start: function (e, ui) {
                            ui.placeholder.height(ui.helper.outerHeight());
                        },
                        tolerance: 'pointer',
                        helper: function (e, tr) {
                            var originals = tr.children();
                            var helper = tr.clone();
                            helper.children().each(function (index) {
                                $(this).width(originals.eq(index).outerWidth());
                            });
                            return helper;
                        },
                        forcePlaceholderSize: true
                    });
            },
            addRow: function () {
                var newValue = this.$el.find('.pim-extended-attribute-string-collection-new-value').val();
                var values = [];
                if (null !== this.getCurrentValue().data) {
                    values = JSON.parse(this.getCurrentValue().data);
                }
                values.push($.trim(newValue));
                this.setCurrentValue(JSON.stringify(values));
                this.render();
            },
            updateModel: function () {
                var values = [];
                this.$('.field-input:first .pim-extended-attribute-string-collection-values tbody tr').each(function () {
                    var $row = $(this);
                    var text = $row.find('.pim-extended-attribute-string-collection-value').val();
                    if ('' !== $.trim(text)) {
                        values.push(text);
                    }
                });
                this.setCurrentValue(JSON.stringify(values));
            },
            setFocus: function () {
                this.$('.pim-extended-attribute-string-collection-new-value').focus();
            }
        });
    }
);
