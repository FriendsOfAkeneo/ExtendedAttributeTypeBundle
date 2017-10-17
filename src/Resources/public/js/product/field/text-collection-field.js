'use strict';
/**
 * Text collection field
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
        'pim-extended-attribute-type/templates/product/field/text-collection'
    ],
    function (Field,
              _,
              $,
              stringTemplate) {
        return Field.extend({
            fieldTemplate: _.template(stringTemplate),

            renderInput(context) {
                return this.fieldTemplate(context);
            },

            postRender() {
                const $fieldInput = this.$('.field-input:first');
                const $tableBody = $fieldInput.find('tbody');
                const self = this;

                $fieldInput.find('.AknTextCollection-addButton').click(function () {
                    this.addRow();
                }.bind(this));

                $tableBody
                    .on('change', '.AknTextCollection-item', this.updateModel.bind(this))
                    .on('click', '.AknTextCollection-deleteButton', function () {
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
                            const originals = tr.children();
                            const helper = tr.clone();
                            helper.children().each(function (index) {
                                $(this).width(originals.eq(index).outerWidth());
                            });
                            return helper;
                        },
                        forcePlaceholderSize: true
                    });
            },

            addRow() {
                const newValue = this.$el.find('.AknTextCollection-newItem').val();
                let values = [];
                if (null !== this.getCurrentValue().data) {
                    values = this.getCurrentValue().data;
                }
                values.push($.trim(newValue));
                this.setCurrentValue(values);
                this.render();
                this.setFocus();
            },

            updateModel() {
                let values = [];
                this.$('.field-input:first .AknTextCollection-items tbody tr').each(function () {
                    const $row = $(this);
                    const text = $row.find('.AknTextCollection-item').val();
                    if ('' !== $.trim(text)) {
                        values.push(text);
                    }
                });
                this.setCurrentValue(values);
            },
            setFocus: function () {
                this.$('.AknTextCollection-newItem').focus();
            }
        });
    }
);
