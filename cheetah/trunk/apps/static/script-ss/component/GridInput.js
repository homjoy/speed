fml.define('component/GridInput', ['jquery', 'plugin/store'], function (require, exports) {
    "use strict";
    var $ = require('jquery'), store = require('plugin/store');

    var DEFAULT = {
        size: 6,
        maskInput: false, //掩盖输入内容
        maskText: '*',
        value: null,
        onInput: function () {
        }
    };
    var KEY = {
        BACKSPACE: 8,
        TAB: 9,
        ENTER: 13,
        SPACE: 32,
        LEFT: 37,
        UP: 38,
        RIGHT: 39,
        DOWN: 40,
        NUMPAD_ENTER: 108
    };

    /**
     *
     * @param element
     * @param options
     * @constructor
     */
    function GridInput(element, options) {
        this.$element = $(element);
        this.options = $.extend({}, DEFAULT, options, true);
        this.lastValue = this.options.value || this.$element.val() || this.$element.text() || '';


        this.render();
        this.bindEvent();
    }

    GridInput.prototype = {
        render: function () {
            var values = this.lastValue.split('');
            var showValue, inputHtml, width = (100 / this.options.size),
                $gridInput = $('<div class="grid-input"></div>');

            for (var i = 0; i < this.options.size; i++) {
                showValue = this.getShowValue(values[i]);
                inputHtml = '<input type="text" class="grid-input-item" value="' + showValue + '" maxlength="1" autocomplete="off" style="width:' + width + '%">';
                $gridInput.append(inputHtml);
            }

            this.$element.hide();
            this.$element.after($gridInput);
            this.$gridInput = $gridInput;
        },
        getShowValue: function (value) {
            return value ? (this.options.maskInput ? this.options.maskText : value ) : '';
        },
        getValue: function () {
            var that = this;
            return this.$gridInput.find('.grid-input-item').map(function () {
                return that.options.maskInput ? $(this).data('value') : $(this).val();
            }).get().join('');
        },
        syncToInput: function () {
            var gridInputValue = this.getValue();

            //值未改变.
            if (gridInputValue == this.lastValue) {
                return false;
            }
            this.$element.val(gridInputValue);
            this.$element.trigger('input');
            this.lastValue = gridInputValue;

            //触发input事件
            $.isFunction(this.options.onInput) && this.options.onInput.call(this, gridInputValue);
        },
        bindEvent: function () {
            var that = this;

            this.$gridInput.on('click', '.grid-input-item', function (e) {
                that.focusToLastPosition($(this));
            }).on('keydown', '.grid-input-item', function (e) {
                var input = $(this), value = input.val();
                switch (e.keyCode) {
                    case KEY.BACKSPACE:
                        !value && input.prev() && that.focusToLastPosition(input.prev());
                        break;
                    case KEY.UP:
                    case KEY.LEFT:
                        input.prev() && that.focusToLastPosition(input.prev());
                        break;
                    case KEY.RIGHT:
                    case KEY.DOWN:
                    case KEY.TAB:
                        input.next() && that.focusToLastPosition(input.next());
                        //屏蔽默认.
                        (e.keyCode == KEY.TAB) && e.preventDefault();
                        break;
                    default:
                        input.val('');//清空内容以便更新值.
                }
            }).on('keyup', '.grid-input-item', function (e) {
                var input = $(this), value = input.val();
                input.data('value', value);
                if (value && that.options.maskInput) {
                    input.val(that.options.maskText);
                }

                if (value && input.next()) {
                    input.next().focus();
                }
                that.syncToInput();
                return true;
            });
        },
        focusToLastPosition: function (input) {
            var value = input.val();
            input.focus();
            if (!value) {
                return;
            }
            //清空内容重设，确保光标在最后
            input.val('');
            input.val(value);
        },
        focus: function () {
            var emptyInput = this.$gridInput.find('.grid-input-item').filter(function () {
                return !$(this).val();
            });
            if (emptyInput.length) {
                this.focusToLastPosition($(emptyInput[0]));
            }else{
                this.focusToLastPosition(this.$gridInput.find('.grid-input-item').last());
            }
        },
        destroy: function () {
            this.syncToInput();
            this.$gridInput.remove();
            this.$element.show();
        }
    };

    //别名
    GridInput.prototype.val = GridInput.prototype.getValue;


    return GridInput;
});