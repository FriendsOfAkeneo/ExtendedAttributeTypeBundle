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
        'text!pim-extended-attribute-type/template/product/field/text-collection',
        'text!pim-extended-attribute-type/template/product/field/text-collection-row'
    ],
    function (Field,
              _,
              $,
              fieldTemplate, rowTemplate) {
        return Field.extend({
            fieldTemplate: _.template(fieldTemplate),
            rowTemplate: _.template(rowTemplate),
            rowPrototype: null,
            rawData: null,
            renderInput: function (context) {
                this.rowPrototype = this.rowTemplate(context);
                this.rawData = context.value.data;
                return this.fieldTemplate(context);
            },
            postRender: function () {
                var $fieldInput = this.$('.field-input:first');
                var $tableBody = $fieldInput.find('tbody');
                var self = this;

                if (null !== this.rawData) {
                    this.rawData.split(';').forEach(function (value) {
                        var $row = $(this.rowPrototype);
                        var $field = $row.find('.pim-extended-attribute-text-collection-field').first();
                        $field.val(value);
                        $tableBody.append($row);
                    }.bind(this));
                }

                $fieldInput.find(".pim-extended-attribute-text-collection-add").click(function () {
                    var $newRow = $(this.rowPrototype);
                    $tableBody.append($newRow);
                    this.updateRawData();
                }.bind(this));

                $tableBody
                    .on("change", ".pim-extended-attribute-text-collection-field", this.updateRawData.bind(this))
                    .on('click', 'button', function () {
                        $(this).closest("tr").remove();
                        self.updateRawData();

                        return false;
                    })
                    .sortable({
                        axis: "y",
                        cursor: "move",
                        handle: ".icon-reorder",
                        update: this.updateRawData.bind(this),
                        start: function (e, ui) {
                            ui.placeholder.height(ui.helper.outerHeight());
                        },
                        tolerance: "pointer",
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
            updateRawData: function () {
                var value = [];
                this.$('.field-input:first .pim-extended-attribute-text-collection-values tbody tr').each(function () {
                    var $row = $(this);
                    var row = [
                        $row.find(".pim-extended-attribute-text-collection-value").val()
                    ];
                    value.push(row.join(':'));
                });
                this.rawData = value.join(';');
                this.updateModel();
            },
            updateModel: function () {
                var data = this.rawData;
                data = '' === data ? this.attribute.empty_value : data;
                this.setCurrentValue(data);
            },
            setFocus: function () {
                this.$('.field-input:first textarea').focus();
            }
        });
    }
);
