fml.define('component/upload', ['jquery'], function (require, exports) {
    "use strict";
    var jQuery = require('jquery');


    (function ($, window, document, undefined) {

        /**
         * XHR 对象
         * @param options
         * @constructor
         */
        function XHR(options) {
            this.options = options;
        }

        XHR.prototype = {
            send: function (data) {
                var self = this,
                    options = self.options,
                    xhr = new XMLHttpRequest(),
                    _timer,
                    url = options.url;

                //如果不加cache
                if (!options.cache) {
                    url += (~url.indexOf('?') ? '&' : '?') + (new Date().getTime()) + Math.random();
                }

                //处理事件
                xhr.onreadystatechange = function () {
                    //请求未完成
                    if (xhr.readyState != 4) {
                        return;
                    }

                    //清空事件处理及超时检测
                    xhr.onreadystatechange = null;
                    _timer && window.clearTimeout(_timer);

                    //检测状态码
                    if (xhr.status !== 200) {
                        return options.error && options.error("上传错误.", options.identify, xhr);
                    }

                    //解析响应
                    try {
                        var json = $.parseJSON(xhr.response);
                        return options.success && options.success(json, options.identify);
                    } catch (err) {
                        return options.error && options.error(err, options.identify, xhr);
                    }
                };

                //处理上传进度
                if (options.progress) {
                    xhr.upload.addEventListener('progress', function (e) {
                        //百分比保留两位小数
                        options.progress((e.loaded / e.total * 100).toFixed(2), options.identify, xhr);
                    }, false);
                }

                //异步发送请求
                xhr.open('POST', url, true);
                //处理额外的HTTP 头.
                if (options.headers) {
                    for (var name in options.headers) {
                        xhr.setRequestHeader(name, options.headers[name]);
                    }
                }

                if (options.timeout) {
                    _timer = window.setTimeout(function () {
                        //停止上传
                        if (xhr) {
                            xhr.abort();
                            xhr.onreadystatechange = null;
                        }

                        _timer && window.clearTimeout(_timer);

                        options.error && options.error("上传超时.", options.identify, xhr);
                    }, options.timeout);
                }

                //发送数据
                xhr.send(data);
            }
        };


        // 默认配置
        var defaults = {
            name: null, //上传文件的字段名字
            action: "", //上传路径
            draggable: false, //是否允许拖拽上传
            crossDomain: false, //是否是跨域请求
            backUri: null, //配合crossDomain使用
            multiple: false, //是否允许多文件上传
            headers: {}, //需要添加的HTTP 头
            data: {}, //需要额外提交的数据
            timeout: 0,//超时时间
            allowTypes: null, //允许的文件类型
            maxFileCount: 0, //允许同时上传的最大文件数
            maxFileSize: 0, //允许上传的最大文件大小 0为不限制
            existFiles: [], //初始化的文件列表：必须是{"identify":"文件标识","name":"文件名","type":"文件类型jpg"}的对象数组
            showProgressBar: true, //是否显示进度条
            message: {
                textChooseFile: "选择文件",
                textContinueUpload: "继续上传",
                allowTypeTips: "仅支持JPG,PDF格式.",
                draggableTips: "或将文件拖拽至此区域",
                statusText: {
                    uploading: '上传中...',
                    success: '上传成功!',
                    error: '上传失败!',
                    timeout: '上传超时'
                },
                "error": {
                    "tooMuch": "每次最多上传{count}个文件,请重新选择",
                    "tooLarge": "文件大小不符合.",
                    "typeNotAllowed": "文件类型不允许."
                }
            },
            onSuccess: $.noop,
            onError: $.noop,
            onProgress: $.noop,
            onFileRemove: $.noop
        };

        /**
         * 构造函数
         * @param element
         * @param options
         * @constructor
         */
        function QueenUpload(element, options) {
            this.$element = $(element);
            this.options = $.extend(true, {}, defaults, options);
            //必须保证上传字段的名称.
            this.options.name = this.options.name || this.$element.attr('name') || 'file';
            //是否能拖拽上传.
            this.useDragDrop = ('draggable' in document.createElement('span')) && this.options.draggable;
            //是否支持HTML5的 input 的 files 属性
            var isSupportFiles = document.createElement("input").files === null || "files" in document.createElement("input");

            // 是否支持HTML5的 FormData 对象
            this.supportH5 = isSupportFiles && !!window.FormData && window.XMLHttpRequest !== undefined;

            //如果是跨域的，不使用AJAX 的方式.
            if (this.options.crossDomain) {
                this.supportH5 = false;
            }

            this.files = {};
            this._init();
        }

        /**
         *
         */
        QueenUpload.prototype = {
            version: '1.0',
            uniqueId: function (prefix) {
                var uid = ((new Date().getTime()) + '' + Math.random() ).replace('.', '');
                return prefix ? prefix + uid : uid;
            },
            _init: function () {
                var upload = this;
                //生成控件指定的html 结构.
                upload._render();

                //伪装 Input输入框.
                upload._wrapInput();

                //绑定具体的实现
                //优先使用H5的特性.
                if (upload.supportH5) {
                    upload._bindHTML5();
                    this.useDragDrop && upload._bindDnD();
                } else {
                    //禁用多文件上传.
                    upload.options.multiple = false;
                    upload._bindIFrame();
                }


                /**
                 * 处理删除附件.
                 */
                upload.$container.on('click', '.queen-remove-file', function (e) {
                    e.preventDefault();
                    var item = $(this).closest('.queen-file-item');
                    var identify = item.attr('data-identify');
                    if (!item.length || !identify) {
                        return;
                    }

                    //从附件列表里面删除.
                    item.remove();
                    upload.removeFile(identify);
                });
            },
            _bindHTML5: function () {
                var upload = this;
                //绑定输入框的文件选择
                upload.$container.on("change", 'input[type=file]', function (e) {
                    var self = $(this),
                        files = this.files,
                        name = self.attr('name');
                    upload._uploadFiles(files);
                    //清空内容
                    self.val('');
                });
            },
            _bindDnD: function () {
                var upload = this;
                //屏蔽浏览器默认的拖拽行为
                $(document).bind("dragenter", function () {
                    return false;
                }).bind("dragover", function () {
                    return false;
                });
                upload.$container[0].addEventListener('drop', function (e) {
                    e.preventDefault(); //取消默认浏览器拖拽效果
                    var files = (e.dataTransfer && e.dataTransfer.files) || []; //获取文件对象
                    upload._uploadFiles(files);
                }, false);
            },
            _uploadFiles: function (files) {
                var upload = this, i = 0, len = 0;
                if (!files.length) {
                    return;
                }

                if (upload.options.maxFileCount && files.length > upload.options.maxFileCount) {
                    return upload._error(upload.options.message.error.tooMuch.replace('{count}', upload.options.maxFileCount));
                }


                //更新上传文件列表.
                var formData, xhr, file;
                for (i = 0, len = files.length; i < len; i++) {
                    formData = upload._getFormData();
                    formData.append(upload.options.name, files[i]);

                    //显示到文件列表
                    file = upload._getFileInfo(files[i]);
                    upload._appendFile(file);

                    //检测文件类型，并且类型不在允许范围内
                    if (upload.options.allowTypes && $.inArray(file.type, upload.options.allowTypes) === -1) {
                        //通知错误
                        upload._error(upload.options.message.error.typeNotAllowed);
                        //更新标记
                        upload._updateStatus(file.identify, 'error', upload.options.message.error.typeNotAllowed);
                        //不进行上传操作
                        continue;
                    }

                    //检测文件大小，文件大小超过限制.
                    if (upload.options.maxFileSize && file.size > upload.options.maxFileSize) {
                        //通知错误
                        upload._error(upload.options.message.error.tooLarge);
                        //更新标记
                        upload._updateStatus(file.identify, 'error', upload.options.message.error.tooLarge);
                        //不进行上传操作
                        continue;
                    }

                    //上传
                    xhr = new XHR({
                        identify: file.identify,
                        url: upload.options.action,
                        headers: upload.options.headers,
                        timeout: upload.options.timeout,
                        success: upload._success.bind(upload),
                        error: upload._error.bind(upload),
                        progress: upload._progress.bind(upload)
                    });
                    xhr.send(formData);
                }
            },
            _bindIFrame: function () {
                var upload = this;
                this.$container.on("change", 'input[type=file]', function (e) {
                    //生成 iframe 的唯一id
                    var uniqueId = upload.uniqueId('queen-iframe-');
                    var iframe = $('<iframe id="' + uniqueId + '" name="' + uniqueId + '"></iframe>').hide().appendTo('body'),
                        form = null,
                        _timer = null,
                        _callbackName = 'callback_' + uniqueId,
                        crossDomainData = '', /**/file;

                    //获取选择的文件信息
                    file = upload._getFileInfo($(this).val());
                    //显示到文件列表
                    upload._appendFile(file);


                    var done = function () {
                        if (_timer) {
                            window.clearTimeout(_timer);
                        }
                        iframe.remove();
                        form && form.remove();
                    };

                    if (upload.options.timeout) {
                        _timer = window.setTimeout(function () {
                            upload._error("上传超时.", file.identify);
                        }, upload.options.timeout);
                    }

                    //如果是跨域上传.
                    if (upload.options.crossDomain) {
                        window[_callbackName] = function (ret) {
                            try {
                                var json = $.parseJSON(ret);
                                upload._success(json, file.identify);
                            } catch (error) {
                                upload._error(error, file.identify);
                            }
                        };

                        crossDomainData = "__callback=" + _callbackName + "&backuri=" + upload.options.backUri + "&backname=data"
                        if (upload.options.url.indexOf('?') > 0) {
                            crossDomainData = '&' + crossDomainData
                        } else {
                            crossDomainData = '?' + crossDomainData
                        }
                    } else {
                        //处理返回值
                        iframe.bind('load', function () {
                            var content = this.contentWindow.window.document.body;
                            var ret = content && (content.innerText || content.textContent || content.innerHTML);

                            try {
                                var json = $.parseJSON(ret);
                                upload._success(json, file.identify);
                                done();
                            } catch (error) {
                                upload._error(error, file.identify);
                            }
                        });
                    }


                    //处理额外的数据.
                    var html = '';
                    if (upload.options.data) {
                        for (var name in upload.options.data) {
                            html += '<input type="hidden" name="' + name + '" value="' + upload.options.data[name] + '" />'
                        }
                    }

                    form = $('<form method="post" enctype="multipart/form-data"></form>')
                        .attr("action", upload.options.action + crossDomainData)
                        .attr("target", uniqueId)
                        .append(html)
                        .append(this) //包括文件输入框
                        .css({position: 'relative'})
                        .hide()
                        .appendTo("body")
                        .submit();

                    //文件输入框没有了，需要再次模拟.
                    upload._wrapInput();
                });
            },
            _getFormData: function () {
                var upload = this;
                var form = new FormData();

                //整合额外的数据.
                if (upload.options.data) {
                    for (var name in upload.options.data) {
                        form.append(name, upload.options.data[name]);
                    }
                }
                return form;
            },
            //确保控件HTML 结构.
            _render: function () {
                var upload = this;
                this.$element.hide();
                //是否是文件输入框.
                //var isFileInput = upload.$element.prop('tagName') === 'INPUT' && upload.$element.attr('type') === 'file';
                //if (isFileInput) {
                //处理文件输入框.
                //upload.originElement = upload.$element;
                //} else {
                //    upload.$element.wrapInner('<span></span>');
                //}


                //直接绑定的上传按钮
                upload.$container = $('<div class="queen-upload-container">' +
                    '<div class="queen-upload-control">' +
                    '<a class="btn-queen-upload" href="javascript:void(0)"><span>' + upload.options.message.textChooseFile + '<span></a>' +
                    '</div>' +
                    '<div class="queen-uploaded-files"></div>' +
                    '</div>');
                upload.$uploadBtn = upload.$container.find('.btn-queen-upload');
                upload.$uploadBtn.css({
                    position: 'relative',
                    overflow: 'hidden'
                });

                //伪装 Input输入框.
                upload._wrapInput();

                if (upload.options.message.allowTypeTips) {
                    upload.$uploadBtn.after('<span class="queen-upload-tips">' +
                        (upload.useDragDrop ? upload.options.message.draggableTips : upload.options.message.allowTypeTips)
                        + '</span>');
                }

                //是否支持拖拽上传.
                upload.$container.toggleClass('queen-draggable', upload.useDragDrop);
                upload.$container.addClass('queen-upload-empty');

                //初始化文件列表
                if (upload.options.existFiles && upload.options.existFiles.length) {
                    upload.addExistFiles(upload.options.existFiles);
                }

                //插入到绑定的控件之后.
                upload.$element.after(upload.$container);
            },

            //处理模拟的文件输入框.
            _wrapInput: function () {
                var self = this;
                if (self.$uploadBtn.find('input[type=file]').length > 0) {
                    return;
                }
                var mockInput = $('<input type="file" style="font-size:100px !important;" name="' + self.options.name + '" ' + (self.options.multiple ? 'multiple' : '') + ' />')
                    .css({
                        'position': 'absolute',
                        'left': 0,
                        'top': 0,
                        'cursor': 'pointer',
                        'opacity': 0,
                        'width': self.$uploadBtn.outerWidth()
                    });

                //放一个模拟输入框.
                self.$uploadBtn.append(mockInput);
            },
            _success: function (json, identify, xhr) {
                var upload = this;
                upload.files[identify].result = json;
                var file = upload.files[identify];
                //处理返回值
                upload._updateStatus(identify, 'success');

                //回调自定义的事件
                if ($.isFunction(upload.options.onSuccess)) {
                    if (upload.options.onSuccess.call(upload, json, file) === false) {
                        //如果返回false 表示出错.
                        upload._updateStatus(identify, 'error');
                    }
                }
            },
            _error: function (message, identify) {
                var upload = this,
                    file = upload.files[identify] || null;

                //更新状态
                this._updateStatus(identify, 'error', message);

                //处理自定义的progress方法
                if ($.isFunction(upload.options.onError)) {
                    upload.options.onError.call(upload, message, file);
                }
            },
            //进度控制
            _progress: function (percent, identify) {
                var upload = this,
                    file = upload.files[identify] || null;
                if (!identify || !file) {
                    return;
                }

                //如果显示了进度条，则更新进度条.
                if (upload.options.showProgressBar) {
                    var item = this.$container.find('[data-identify=' + identify + ']');
                    item.find('.queen-upload-progress').removeClass('')
                    item.find('.queen-upload-progress-inner').css({'width': percent + '%'});
                }

                //处理自定义的progress方法
                if ($.isFunction(upload.options.onProgress)) {
                    upload.options.onProgress.call(upload, percent, file);
                }
            },
            //更新上传状态
            _updateStatus: function (identify, status, message) {
                var item = this.$container.find('[data-identify=' + identify + ']');
                if (!identify || !item.length) {
                    return;
                }
                if (!message) {
                    message = this.options.message.statusText[status] || 'error.';
                }
                item.removeClass('uploading success error').addClass(status)
                    .find('.queen-message')
                    .text(message);
            },
            //获取上传的文件列表
            //_getFiles: function (input) {
            //    var upload = this;
            //    var files = [];
            //
            //    if (input.files) {
            //        $.each(input.files, function (index, value) {
            //            files.push(upload._getFileInfo(value));
            //        });
            //    } else {
            //        files.push(this._getFileInfo(input));
            //    }
            //    return files;
            //},
            _getFileInfo: function (value) {
                if (value instanceof window.File) {
                    return {
                        identify: this.uniqueId('queen-file-'),
                        name: value.name,
                        mime: value.type,
                        type: value.name.split('.').pop(),
                        size: value.size
                    }
                } else if (typeof value == 'string') {
                    //不支持this.files
                    var file = {
                        identify: this.uniqueId('queen-file-')
                    };
                    //转换文件类型
                    file.name = (value + '').split(/\\|\//g).pop();
                    file.mime = null;
                    file.type = file.name.split('.').pop();
                    file.size = 0;
                    return file;
                }
            },
            _appendFile: function (file, status) {
                var upload = this,
                    item, itemTpl;
                //只要有文件上传.
                upload.$container.removeClass('queen-upload-empty');
                //按钮切换为继续上传
                upload.$container.find('.btn-queen-upload > span').text(upload.options.message.textContinueUpload);
                //保存到文件列表
                upload.files[file.identify] = file;
                status = status || 'uploading';


                itemTpl = '<div class="queen-file-item">' +
                    '<i class="queen-item-column queen-attachment"></i>' +
                    '<span class="queen-item-column queen-filename"></span>';

                //如果显示进度条
                if (upload.options.showProgressBar) {
                    itemTpl += '<span class="queen-item-column queen-upload-progress">';
                    if (status == 'success') {
                        itemTpl += '<span class="queen-upload-progress-inner" style="width:100%;"></span>';
                    } else {
                        itemTpl += '<span class="queen-upload-progress-inner"></span>';
                    }
                    itemTpl += '</span>';
                }

                itemTpl += '<a href="javascript:void(0)" class="queen-item-column queen-remove-file"><i></i></a>' +
                    '<span class="queen-item-column queen-message"></span></div>';

                item = $(itemTpl).attr('data-identify', file.identify);

                //设置文件名.
                item.find('.queen-filename').text(file.name);
                //放入列表
                upload.$container.find('.queen-uploaded-files').append(item);
                //初始状态
                upload._updateStatus(file.identify, status);
            },
            addExistFiles: function (files) {
                var upload = this;
                if (!files) {
                    return;
                }
                //单个对象.
                if ($.isPlainObject(files)) {
                    files = [files];
                }

                $.each(files, function (index, file) {
                    //没有提供标识或者文件名
                    if (!file.identify || !file.name) {
                        return;
                    }
                    upload._appendFile(file, 'success');
                });
            },
            removeFile: function (identify) {
                if (!identify) {
                    return;
                }
                var upload = this;
                var file = upload.files[identify];

                if ($.isFunction(upload.options.onFileRemove)) {
                    upload.options.onFileRemove.call(upload, file);
                }
                //删除对应的文件.
                delete upload.files[identify];
                //如果最后文件列表为空，追加样式.
                if ($.isEmptyObject(upload.files)) {
                    upload.$container.addClass('queen-upload-empty');
                    upload.$container.find('.btn-queen-upload > span').text(upload.options.message.textChooseFile);
                }
            },
            destroy: function () {
                var upload = this;
                upload.$container.remove();
                upload.$element.show();
                //还原原来的元素.
                upload.$element.removeData('QueenUpload');
            }
        };

        /**
         * 提供插件方法.
         * @param options
         * @returns {*}
         * @constructor
         */
        $.fn.QueenUpload = function (options) {
            //提取参数.
            var args = Array.prototype.slice.call(arguments, 1);
            return this.each(function () {
                var instance = $(this).data("QueenUpload");
                if ('object' === typeof(options) && undefined === instance) {
                    instance = new QueenUpload(this, options);
                    $(this).data("QueenUpload", instance);
                    return this;
                }

                if ($.type(options) === 'string' && options.charAt(0) !== '_' && instance[options]) {
                    instance[options].apply(instance, args);
                }

                return this;
            })
        };

    })(jQuery, window, document);
});