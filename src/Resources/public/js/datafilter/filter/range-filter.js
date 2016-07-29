define(
    ['jquery', 'underscore', 'oro/datafilter/number-filter', 'oro/app'],
    function ($, _, NumberFilter, app) {
        'use strict';

        /**
         * Range filter
         *
         * @author Romain Monceau <romain@akeneo.com>
         *
         * @export  oro/datafilter/range-filter
         * @class   oro.datafilter.RangeFilter
         * @extends oro.datafilter.NumberFilter
         */
        return NumberFilter.extend({
            /**
             * @inheritDoc
             */
            initialize: function() {
                NumberFilter.prototype.initialize.apply(this, arguments);

                this.on('disable', this._onDisable, this);
            },

            _onDisable: function() {
                this.$('.choicefilter button.dropdown-toggle').first().html(_.__('Action') + '<span class="caret"></span>');
            },

            /**
             * @inheritDoc
             */
            _renderCriteria: function (el) {
                $(el).append(this.popupCriteriaTemplate({
                    name:    this.name,
                    choices: this.choices
                }));

                return this;
            },

            /**
             * @inheritDoc
             */
            _writeDOMValue: function (value) {
                this._setInputValue(this.criteriaValueSelectors.value, value.value);
                this._setInputValue(this.criteriaValueSelectors.type, value.type);
                this._setInputValue(this.criteriaValueSelectors.max, value.max);

                return this;
            },

            /**
             * @inheritDoc
             */
            _readDOMValue: function () {
                return {
                    value: this._getInputValue(this.criteriaValueSelectors.value),
                    type: this._getInputValue(this.criteriaValueSelectors.type),
                    max: this._getInputValue(this.criteriaValueSelectors.max)
                };
            },

            /**
             * @inheritDoc
             */
            _getCriteriaHint: function () {
                var value = (arguments.length > 0) ? this._getDisplayValue(arguments[0]) : this._getDisplayValue();
                if (value.type === 'empty') {
                    return this._getChoiceOption(value.type).label;
                }
                if (!value.value) {
                    return this.placeholder;
                } else {
                    var operator = _.find(this.choices, function(choice) {
                        return choice.value == value.type;
                    });
                    operator = operator ? operator.label : '';

                    return operator + ' "' + value.value + ' ' + _.__(value.max) + '"';
                }
            },

            /**
             * @inheritDoc
             */
            popupCriteriaTemplate: _.template(
                '<div class="rangefilter choicefilter">' +
                    '<div class="input-prepend input-append">' +
                        '<div class="btn-group">' +
                            '<button class="btn dropdown-toggle" data-toggle="dropdown">' +
                                '<%= _.__("Action") %>' +
                                '<span class="caret"></span>' +
                            '</button>' +

                            '<ul class="dropdown-menu">' +
                                '<% _.each(choices, function (choice) { %>' +
                                '<li><a class="choice_value" href="#" data-value="<%= choice.value %>"><%= choice.label %></a></li>' +
                                '<% }); %>' +
                            '</ul>' +
                            '<input class="name_input" type="hidden" name="range_type" value=""/>' +
                        '</div>' +

                        '<input type="text" name="value" value="">' +

                        '<input type="text" name="range_max" value="">' +

                    '</div>' +
                    '<button class="btn btn-primary filter-update" type="button"><%= _.__("Update") %></button>' +
                '</div>'
            ),

            /**
             * Selectors for filter criteria elements
             *
             * @property {Object}
             */
            criteriaValueSelectors: {
                max: 'input[name="range_max"]',
                type: 'input[name="range_type"]',
                value: 'input[name="value"]'
            },

            /**
             * Empty value object
             *
             * @property {Object}
             */
            emptyValue: {
                max: '',
                type: '',
                value: ''
            },

            /**
             * @inheritDoc
             */
            _triggerUpdate: function(newValue, oldValue) {
                if (!app.isEqualsLoosely(newValue, oldValue)) {
                    this.trigger('update');
                }
            },

            /**
             * @inheritDoc
             *
             * Synchronize choice selector with new value
             */
            _onValueUpdated: function(newValue, oldValue) {
                var menu = this.$('.choicefilter .dropdown-menu');
                menu.find('li a').each(function() {
                    var item = $(this);
                    if (item.data('value') == oldValue.type && item.parent().hasClass('active')) {
                        item.parent().removeClass('active');
                    } else if (item.data('value') == newValue.type && !item.parent().hasClass('active')) {
                        item.parent().addClass('active');
                        menu.parent().find('button').html(item.html() + '<span class="caret"></span>');
                    }
                });
                if (newValue.type === 'empty') {
                    this.$(this.criteriaValueSelectors.value).hide();
                    this.$(this.criteriaValueSelectors.max).hide();
                } else {
                    this.$(this.criteriaValueSelectors.value).show();
                    this.$(this.criteriaValueSelectors.max).show();
                }

                NumberFilter.prototype._onValueUpdated.apply(this, arguments);
            },

            /**
             * @inheritDoc
             */
            setValue: function(value) {
                if (this._isNewValueUpdated(value)) {
                    var oldValue = this.value;
                    this.value = app.deepClone(value);
                    this._updateDOMValue();
                    this._onValueUpdated(this.value, oldValue);
                }

                return this;
            },

            /**
             * @inheritDoc
             */
            _onClickChoiceValue: function(e) {
                NumberFilter.prototype._onClickChoiceValue.apply(this, arguments);
                var parentDiv = $(e.currentTarget).parent().parent().parent().parent();
                if ($(e.currentTarget).attr('data-value') === 'empty') {
                    parentDiv.find(this.criteriaValueSelectors.value).hide();
                    parentDiv.find(this.criteriaValueSelectors.max).hide();
                } else {
                    parentDiv.find(this.criteriaValueSelectors.value).show();
                    parentDiv.find(this.criteriaValueSelectors.max).show();
                }
            },

            /**
             * @inheritDoc
             */
            reset: function() {
                this.setValue(this.emptyValue);
                this.trigger('update');

                return this;
            }
        });
    }
);
