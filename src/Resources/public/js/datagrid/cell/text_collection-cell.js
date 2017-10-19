/* global define */
define(
    [
        'underscore',
        'oro/datagrid/string-cell',
        'pim-extended-attribute-type/templates/datagrid/cell/text-collection'
    ],
    function (_, StringCell, template) {
        'use strict';

        /**
         * String column cell. Added missing behaviour.
         *
         * @export  oro/datagrid/string-cell
         * @class   oro.datagrid.StringCell
         * @extends Backgrid.StringCell
         */
        return StringCell.extend({
            template: _.template(template),

            /**
             * Render an image.
             */
            render() {
                const collection = this.formatter.fromRaw(this.model.get(this.column.get('name')));
                if (collection) {
                    this.$el.empty().html(this.getTemplate({collection: collection}));
                }

                return this;
            },

            /**
             * Returns the template used to show the image.
             *
             * This function can be overridden to alter the way the image is shown.
             *
             * @returns {string}
             */
            getTemplate(params) {
                return this.template(params);
            }
        });
    }
);
