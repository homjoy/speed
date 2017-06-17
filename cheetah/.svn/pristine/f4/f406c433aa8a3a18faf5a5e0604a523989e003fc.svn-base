fml.define('component/TreeSelect', ['jquery', 'plugin/zTree'], function (require, exports) {
    var $ = require('jquery');


    return (function (factory) {
        if (typeof define === "function" && define.amd) {
            // AMD模式
            define(["jquery"], factory);
        } else {
            // 全局模式
            factory($);
        }
    }(function ($) {
        "use strict";

        /**
         *
         * @param element
         * @param options
         * @constructor
         */
        function TreeSelect(element, options) {
            //绑定插件的输入框
            this.$elelment = $(element);
            this.options = options;
            if (!$.fn.zTree) {
                throw new Error("zTree V3 is required");
            }

            //匹配搜索的节点
            this.matchedNodes = [];
            this.selectedValues = [];

            if (!options.data) {
                throw new Error("必须提供数据.");
            }

            /**
             * 解析已存在的值.
             */
            var value = this.$elelment.val();


            //如果是多选，开启并标记已存在的数据.
            if (this.options.multiple) {
                this.options.zTreeSettings.check = {
                    enable: true,
                    chkboxType: this.options.cascading ? {"Y": "ps", "N": "ps"} : {"Y": "", "N": ""}
                };
                //处理多值.
                this.existValue = value ? value.split(this.options.delimiter) : [];
            } else {
                this.existValue = value;
            }

            this.init();
        }

        // 默认配置
        TreeSelect.DEFAULT = {
            "data": null, //数据
            "delimiter": ',', //值的分隔符.
            "valueField": 'id', //取值的字段.
            "placeholder": '请选择', //无值时候的提示.
            "multiple": false, //是否多选，预备以后可以支持多选.
            "cascading": false, //是否级联选择.
            "zTreeSettings": {
                view: {
                    showLine: false,
                    fontCss: function (treeId, treeNode) {
                        return (!!treeNode.highlight) ? {
                            color: "#A60000",
                            "font-weight": "bold"
                        } : {
                            color: "#333",
                            "font-weight": "normal"
                        };
                    }
                },
                callback: {
                    onClick: $.noop,
                    onCheck: $.noop
                }
            }
        };
        /**
         * 生成树形ID
         * @returns {string}
         */
        function genZTreeId() {
            return 'zTree' + (new Date().getTime()) + (Math.random().toString().replace('.', ''));
        }

        function inArray(element, values) {
            for (var i = 0, len = values.length; i < len; i++) {
                if (element == values[i]) {
                    return true;
                }
            }

            return false;
        }

        /**
         * 标记为选中.
         * @param ids
         * @param values
         * @param field
         */
        function markedNode(ids, values, field) {
            field = field || 'id';
            //标记为选中.
            for (var i = 0, len = values.length; i < len; i++) {
                //确保数据类型一致.

                if (inArray(values[i][field], ids)) {
                    values[i].checked = true;
                }

                if (values[i].children && values[i].children.length) {
                    markedNode(ids, values[i].children);
                }
            }
        }

        TreeSelect.prototype = {
            version: '1.0',
            init: function () {
                //绑定处理方法.
                this.options.zTreeSettings.callback.onClick = this.onZTreeClick.bind(this);
                if (this.options.multiple) {
                    this.options.zTreeSettings.callback.onCheck = this.onZTreeChecked.bind(this);
                }
                this.render();
                this.bindEvent();
            },

            render: function () {
                var self = this;
                //隐藏输入框.
                self.$elelment.hide();

                self.$container = $('<div class="tree-select-container">' +
                    '<div class="tree-select-label"><span class="label-inner">' + self.options.placeholder + '</span></div>' +
                    '<div class="tree-select-dropdown">' +
                    '<span class="tree-search-box"><input type="text" class="tree-select-input" placeholder="搜索.."/></span>' +
                    '<div class="tree-select-dropdown-wrap"><ul class="ztree tree-select-ztree" id="' + genZTreeId() + '"></ul></div>' +
                    '</div></div>');

                //已选中的label
                self.$label = self.$container.find('.tree-select-label');

                //搜索输入框.
                self.$searchBox = self.$container.find('.tree-select-input');
                //下拉列表
                self.$dropdown = self.$container.find('.tree-select-dropdown');

                //追加到指定元素之后.
                self.$container.insertAfter(self.$elelment);

                self.zTreeObj = $.fn.zTree.init(self.$container.find('.tree-select-ztree'), self.options.zTreeSettings, self.options.data);


                if (!self.options.multiple) {
                    //获取选中的节点.
                    var node = self.zTreeObj.getNodeByParam(self.options.valueField, self.existValue, null);
                    if (node) {
                        //单独选中.
                        self.zTreeObj.selectNode(node, false);
                        //展开.
                        self.expandPath(node, true);
                        self.$label.find('.label-inner').text(node.name);
                        self.$label.attr('title', node.name);
                    }
                } else {
                    //标记为选中.
                    markedNode(self.existValue, self.options.data);
                    var nodes = self.zTreeObj.getCheckedNodes();
                    if (nodes && nodes.length) {
                        var nodeName = [];
                        $.each(nodes, function (index, node) {
                            //展开对应的节点
                            self.expandPath(node, true);
                            nodeName.push(node.name);
                        });
                        //显示已存在的值.
                        self.$label.find('.label-inner').text(nodeName.join(','));
                        self.$label.attr('title', nodeName.join(','));
                    }
                }
            },

            bindEvent: function () {
                var that = this;


                var typingTimer;
                /**
                 * 处理文本变更:http://stackoverflow.com/questions/4220126/run-javascript-function-when-user-finishes-typing-instead-of-on-key-up
                 */
                var doneTypingInterval = 50;  //time in ms, 5 second for example
                var lastValue = "";


                that.$searchBox.on('textChange', that.searchNode.bind(that))
                    .on('keyup', function () {
                        var input = $(this);
                        clearTimeout(typingTimer);
                        var value = $.trim(input.val() || '');
                        if (lastValue === value) return;
                        lastValue = value;
                        typingTimer = setTimeout(function () {
                            //触发文字变更事件.
                            input.trigger('textChange');
                        }, doneTypingInterval);
                    }).on('keydown', function () {
                        clearTimeout(typingTimer);
                    });
                that.$label.on('click', function () {
                    that.toggle(!that.$container.hasClass('active'));
                });

                $("body").on('click', function (e) {
                    var target = $(e.target);
                    //阻止
                    if (target.parents('.tree-select-container').size() > 0) {
                        return false;
                    } else {
                        that.toggle(false);
                    }
                });
            },
            toggle: function (status) {
                //隐藏其他的.
                $('.tree-select-dropdown').hide();
                $('.tree-select-container.active').removeClass('active');
                if (status) {
                    this.$dropdown.show();
                    this.$container.addClass('active');
                } else {
                    this.$dropdown.hide();
                    this.$container.removeClass('active');
                }
            },

            onZTreeClick: function (event, treeId, treeNode, clickFlag) {
                var that = this;
                //如果是多选，忽略单击.
                if (that.options.multiple) {
                    //单击的同时切换选中，然后不进行级联选中，触发回调.
                    that.zTreeObj.checkNode(treeNode, !treeNode.checked, false, true);
                    return false;
                }


                //如果是之前选中的节点，则表示取消.
                //此处使用内置的tid 进行比较，防止id 字段不存在的情况比较失败.
                if (that.lastSelectedNode && that.lastSelectedNode.tId == treeNode.tId) {
                    that.zTreeObj.cancelSelectedNode(treeNode);
                    //清空最后一次选择的节点.
                    that.lastSelectedNode = null;
                    that.$label.find('.label-inner').text(that.options.placeholder);
                    that.$label.attr('title', that.options.placeholder);
                    that.$elelment.val('').trigger('change');
                } else {
                    that.lastSelectedNode = treeNode;
                    //修改输入框的值
                    that.$label.find('.label-inner').text(treeNode.name);
                    that.$label.attr('title', treeNode.name);
                    //设置选中的值
                    that.$elelment.val(treeNode[that.options.valueField]).trigger('change', [treeNode]);
                }

                //如果是单选，点击则表示选中，直接隐藏下拉.
                that.toggle(false);
            },
            onZTreeChecked: function (e, treeId, treeNode) {
                var that = this;
                var nodes = that.zTreeObj.getCheckedNodes(true);
                var values = [];
                var names = [];
                //var params = [];

                //全部取消了.
                if (!nodes.length) {
                    that.$label.find('.label-inner').text(that.options.placeholder);
                    that.$label.attr('title', that.options.placeholder);

                    that.$elelment.val('').trigger('change', [[]]);
                    return;
                }

                for (var i = 0, l = nodes.length; i < l; i++) {
                    values.push(nodes[i][that.options.valueField]);
                    names.push(nodes[i].name);
                    //params.push({
                    //    id: nodes[i].id,
                    //    name: nodes[i].name
                    //})
                }
                //显示到label 上.
                that.$label.find('.label-inner').text(names.join(','));
                that.$label.attr('title', names.join(','));
                that.$elelment.val(values.join(that.options.delimiter)).trigger('change', [nodes]);
            },

            toggleNodes: function (show) {
                var nodes = this.zTreeObj.transformToArray(this.zTreeObj.getNodes());
                if (show) {
                    this.zTreeObj.showNodes(nodes);
                } else {
                    this.zTreeObj.hideNodes(nodes);
                }
            },

            /**
             * 搜索
             * @param e
             */
            searchNode: function (e) {
                var that = this;
                //重置节点列表
                that.resetNodeList();
                var value = $.trim(that.$searchBox.val() || '');
                //搜索条件为空.
                if (value === "") {
                    return;
                }

                /*
                 * 隐藏所有节点，只显示匹配的
                 */
                that.toggleNodes(false);
                /*
                 * 获取匹配的节点
                 */
                that.matchedNodes = that.zTreeObj.getNodesByParamFuzzy("name", value);

                /**
                 * 依次展开和高亮匹配搜索条件的节点
                 */
                for (var i = 0, l = that.matchedNodes.length; i < l; i++) {
                    that.matchedNodes[i].highlight = true;
                    that.zTreeObj.updateNode(that.matchedNodes[i]);
                    that.expandPath(that.matchedNodes[i], true);
                }

                //防止被隐藏.
                that.toggle(true);
            },

            /**
             * 展开指定节点及父节点.
             * @param node
             * @param expand
             */
            expandPath: function (node, expand) {
                var that = this;
                var pathNodes = [node];
                var parent, current = node;
                while (parent = current.getParentNode()) {
                    pathNodes.unshift(parent);
                    current = parent;
                }

                that.zTreeObj.showNodes(pathNodes);
                pathNodes.forEach(function (node, i) {
                    that.zTreeObj.expandNode(node, expand, null, null, false);
                });
            },
            /**
             * 重置所有节点.
             */
            resetNodeList: function () {
                var that = this;
                //显示所有节点
                that.toggleNodes(true);
                //默认不展开
                that.zTreeObj.expandAll(false);
                for (var i = 0, l = that.matchedNodes.length; i < l; i++) {
                    that.matchedNodes[i].highlight = false;
                    that.zTreeObj.updateNode(that.matchedNodes[i]);
                }
            },

            destroy: function () {
                var instance = this.$elelment.data('TreeSelect');
                if (instance) {
                    this.$container.remove();
                    this.$elelment.show();
                    this.$elelment.removeData('TreeSelect');
                }
            }
        };


        $.fn.TreeSelect = function (options) {
            options = $.extend(true, {}, TreeSelect.DEFAULT, options);

            return this.each(function () {
                var $this = $(this),
                    data = $this.data('TreeSelect');

                if (!data) {
                    data = new TreeSelect(this, options);
                    $this.data('TreeSelect', data);
                }
                if (typeof options == 'string') {
                    data[options].call($this);
                }
            });
        };


        // ADM
        return TreeSelect;
    }));
});