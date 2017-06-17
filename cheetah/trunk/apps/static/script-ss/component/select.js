fml.define('component/select', ['jquery'], function (require, exports) {
    "use strict";
    var $ = require('jquery');


    return (function ($) {
        "use strict";

        // 默认配置
        var defaults = {
            "width": "200",
            "cssPrefix": "queen-",
            "dataAttr": "data-item",
            "placeholder": "请选择"
        };

        function Select(element, options) {
            this.element = element;
            this.$element = $(element);
            this.settings = $.extend({}, defaults, options);
            this.isSync = false; //是否和select 同步
            this.init();
        }

        Select.prototype = {
            version: '1.0',
            init: function () {
                this.render();
                return this;
            },

            render: function () {
                var self = this;
                //隐藏原始的select
                self.$element.hide();


                //构造模拟的下拉
                self.$container = $('<div>').addClass(this.settings.cssPrefix + 'select-container');
                self.$selected = $('<div>')
                    .addClass(this.settings.cssPrefix + 'selected-content')
                    .addClass('no-selected')
                    .append($('<span>').text(self.settings.placeholder))
                    .append($("<i>").addClass(this.settings.cssPrefix + 'caret'));


                self.$container.append(self.$selected);
                self.$dropdown = $("<div>").addClass(this.settings.cssPrefix + "select-dropdown")
                    .append('<ul class="dropdown-items"></ul>').hide();
                self.$container.append(self.$dropdown);
                self.$element.before(self.$container);
                //同步数据到html
                self.sync();
                //重新绑定时间
            },
            sync: function () {
                var self = this;

                //删除当前下拉数据
                //self.$container.find(".dropdown-items").html('');

                //重新获取原始select 数据
                self.menuItems = this.__menuData();
                var li = [];
                $.each(self.menuItems.items, function (index, item) {
                    li.push('<li><a href="javascript:void(0);" data-i="' + index + '">' + item.text + '</a></li>');
                });

                //生成新的下拉html
                self.$container.find(".dropdown-items").html(li.join(''));

                if(self.menuItems.selected){
                    self.$selected.removeClass('no-selected').removeClass('active');
                    self.$selected.find('span').text(self.menuItems.selected.text);
                }

                //触发各种事件.
                /**
                 * 点击展开
                 */
                self.$selected.off('click').on('click', function () {
                    self.toggle('show');
                });

                /**
                 * 隐藏下拉
                 */
                $('body').on('click', function (event) {
                    var target = $(event.target);
                    if (!target.closest('.' + self.settings.cssPrefix + 'select-container').length) {
                        self.toggle('hide');
                    }
                });

                /**
                 * 切换
                 */
                self.$container.find('.queen-selected-content').off('click').on("click",function(e){
                    //e.preventDefault();
                    var current = $(this);
                    //切换
                    self.toggle(current.hasClass('active') ? 'hide': 'show');
                });

                /**
                 * 选中选项
                 */
                self.$dropdown.off('click').on('click', 'a', function (e) {
                    var option = $(e.target);
                    var index = option.attr('data-i');
                    var item = self.menuItems.items[index];

                    //修改原始select 的值
                    self.$element.val(item.value);
                    //除法change 事件
                    self.$element.trigger('change');
                    //
                    self.menuItems.selected = {"text": item.text, "value": item.value};

                    self.$selected.removeClass('no-selected').removeClass('active');
                    self.$selected.find('span').text(self.menuItems.selected.text);

                    self.$dropdown.hide();
                });

                //标记为已同步.
                self.isSync = true;
                return self;
            },
            selectItem:function(value){

            },
            toggle:function(state){
                //隐藏其他的
                $(".queen-select-dropdown").hide();
                $(".queen-selected-content.active").removeClass("active");

                //切换.
                if(state == 'show'){
                    this.$dropdown.show();
                    this.$selected.addClass('active');
                }else{
                    this.$dropdown.hide();
                    this.$selected.removeClass('active');
                }
            },
            __menuData: function () {
                var self = this;
                var data = {
                    selected: null,
                    items: []
                };

                //获取所有options的value、name
                self.$element.find('option').each(function () {
                    var option = $(this);
                    var selected = option.prop('selected');
                    var item = {
                        'el': option,
                        'elClass': option.attr('class') || '',
                        'value': option.val(),
                        'text': option.text(),
                        'selected': selected
                    };

                    if (selected) {
                        data.selected = {
                            'value': item.value,
                            'text': item.text
                        };
                    }
                    data.items.push(item);
                });
                //构造生成模拟的下拉的数据
                return data;
            },
            destroy: function () {
                this.$container.remove();
                this.$element.show();
                this.$element.data('queen-select',null);
            }
        };

        $.fn.select = function (options) {
            options = $.extend(true, {}, defaults, options);

            return this.each(function () {
                var self = $(this);
                if (undefined == self.data('queen-select')) {
                    self.data('queen-select', new Select(this, options));
                }
            })
        };

        // ADM
        return Select;
    })($);
});