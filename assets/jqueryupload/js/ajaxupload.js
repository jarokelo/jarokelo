(function($){
    'use strict';

    // Detect file input support, based on
    // http://viljamis.com/blog/2012/file-upload-support-on-mobile/
    $.support.fileInput = !(new RegExp(
        // Handle devices which give false positives for the feature detection:
        '(Android (1\\.[0156]|2\\.[01]))' +
            '|(Windows Phone (OS 7|8\\.0))|(XBLWP)|(ZuneWP)|(WPDesktop)' +
            '|(w(eb)?OSBrowser)|(webOS)' +
            '|(Kindle/(1\\.0|2\\.[05]|3\\.0))'
    ).test(window.navigator.userAgent) ||
        // Feature detection for all other devices:
        $('<input type="file">').prop('disabled'));

    // The FileReader API is not actually used, but works as feature detection,
    // as some Safari versions (5?) support XHR file uploads via the FormData API,
    // but not non-multipart XHR file uploads.
    // window.XMLHttpRequestUpload is not available on IE10, so we check for
    // window.ProgressEvent instead to detect XHR2 file upload capability:
    $.support.xhrFileUpload = !!(window.ProgressEvent && window.FileReader);
    $.support.xhrFormDataFileUpload = !!window.FormData;

    // Detect support for Blob slicing (required for chunked uploads):
    $.support.blobSlice = window.Blob && (Blob.prototype.slice ||
        Blob.prototype.webkitSlice || Blob.prototype.mozSlice);

    // Helper function to create drag handlers for dragover/dragenter/dragleave:
    function getDragHandler(type) {
        var isDragOver = type === 'dragover';
        return function (e) {
            e.dataTransfer = e.originalEvent && e.originalEvent.dataTransfer;
            var dataTransfer = e.dataTransfer;
            if (dataTransfer && $.inArray('Files', dataTransfer.types) !== -1/* &&
                    this._trigger(
                        type,
                        $.Event(type, {delegatedEvent: e})
                    ) !== false*/
            ) {
                e.preventDefault();
                if (isDragOver) {
                    dataTransfer.dropEffect = 'copy';
                }
            }
        };
    }

    $.fn.simplefileupload = function(_options) {
        var self = this;

        /*self.fileupload({
            dataType: 'json',
            formData: {
                'ajax-file-upload': true
            }
        });*/

        var $label = self.find('.filename');
        var $fakeInput = self.find(_options.input);
        var $hiddenInput = self.find(_options.hiddenInput);
        var $form = self.closest('form');

        self.on('dragover', getDragHandler('dragover'));
        self.on('dragenter', getDragHandler('dragenter'));
        self.on('dragleave', getDragHandler('dragleave'));
        self.on('drop', function(e) {
            e.dataTransfer = e.originalEvent && e.originalEvent.dataTransfer;
            var dataTransfer = e.dataTransfer;
            if (dataTransfer && dataTransfer.files && dataTransfer.files.length) {
                e.preventDefault();
                var names = [];
                for (var i = 0, l = dataTransfer.files.length; i<l; i++) {
                    names.push(dataTransfer.files[i].name);
                }
                var val = names.join(',');
                $fakeInput.val(val);
                if ($fakeInput.length) {
                    $fakeInput.get(0).files = dataTransfer.files;
                }
                $label.text(val);
                $hiddenInput.val(val);
                oldInput = resetInput();
                $form.yiiActiveForm('validateAttribute', _options.attributeId);
            }
        });

        // clone and reset the file input.
        function resetInput() {
            var $input = $(_options.fileInput);
            var $inputClone = $input.clone();
            $('<form></form>').append($inputClone)[0].reset();
            return $input.after($inputClone).detach();
        }

        var oldInput;

        self.on('change', _options.fileInput, function(e) {
            var $input = $(e.target);
            $label.text($input.val().replace(/^C:\\fakepath\\/, ""));
            $fakeInput.val($input.val());
            if ($fakeInput.length) {
                $fakeInput.get(0).files = $input.get(0).files;
            }
            $hiddenInput.val($input.val());
            oldInput = resetInput();
            $form.yiiActiveForm('validateAttribute', _options.attributeId);
        });

        $form.on('beforeValidateAttribute', function(e, attribute) {
            if (attribute.input !== _options.input) {
                return;
            }
        });

        $form.on('afterValidateAttribute', function(e, attribute, messages) {
            if (attribute.input !== _options.input) {
                return;
            }
            if (messages.length > 0) {
                return;
            }
            // upload here
            oldInput.fileupload();
            oldInput.fileupload('add', {
                fileInput: oldInput
            });
        });

        /*self.find('input[type="file"]').fileupload({
            dataType: 'json',
            formData: {
                'ajax-file-upload': true
            },
            replaceFileInput: false,
            forceIframeTransport: true
        });*/

        return this;
    };

    $.fn.ajaxupload = function(_options) {
        var self = this,
            defaultOptions = {
                uploadUrl: '',
                chunkSize : 10000000,
                debugIframe : false,
                previewResize: false,
                previewWidth: 80,
                previewHeight: 80,
                maximum: false,
                uploadedSelector: false,
                removeFailed: false,
                clearOnUpload: false,
                imageResize: false,
                imageMaxWidth: 1920,
                imageMaxHeight: 1080,
                imageCrop: false,
                previewCrop: true,
                formData: {}
            },
            progressContainer,
            errorContainer,
            progressbar,
            progressNum;

        var options = $.extend({}, defaultOptions, _options);

        if (options.progressbar) {
            progressbar = $(options.progressbar);
        }
        if (options.progress) {
            progressNum = $(options.progress);
        }
        if (options.progressContainer) {
            progressContainer = $(options.progressContainer);
        }
        if (options.errorContainer) {
            errorContainer = $(options.errorContainer);
        }

        self.fileupload({
            url: options.uploadUrl,
            dataType: 'json',
            formData: options.formData,
            maxChunkSize: options.chunkSize,
            forceIframeTransport: options.debugIframe,
            previewMaxWidth: options.previewWidth,
            previewMaxHeight: options.previewHeight,
            previewCrop: options.previewCrop,
            disableImageResize: !options.imageResize,
            imageMaxWidth: options.imageMaxWidth,
            imageMaxHeight: options.imageMaxHeight,
            imageCrop: options.imageCrop,
            start: function(e) {
                if (progressContainer) {
                    progressContainer.stop().fadeIn(400);
                }
                $(e.target).trigger('ajaxuploadstart');
            },
            stop: function(e) {
                if (progressContainer) {
                    progressContainer.stop().fadeOut(400, function() {
                        progressbar && progressbar.css(options.progressbarAllProperty ? options.progressbarAllProperty : 'width',0);
                    });
                }
                $(e.target).trigger('ajaxuploadstop');
            },
            add: function (e, data) {
                if (errorContainer) {
                    errorContainer.empty();
                }

                if (options.clearOnUpload && options.uploadedSelector !== false) {
                    var $uploaded = $(options.uploadedSelector);
                    if (options.templateSelectors['cancel']) {
                        $uploaded.find(options.templateSelectors['cancel']).click();
                    }
                    if (options.templateSelectors['delete']) {
                        $uploaded.find(options.templateSelectors['delete']).click();
                    }
                    $uploaded.remove();
                }

                if (options.uploadedSelector !== false
                    && (options.maximum !== false && $(options.uploadedSelector).length >= options.maximum)
                ) {
                    return false
                }
                if (options.maximum !== false && options.uploadedSelector !== false && $(options.uploadedSelector).length >= options.maximum ) {
                    return false;
                }
                data.context = $();
                $.each(data.files, function(index, file) {

                    var picture = $($.parseHTML($.trim(options.fileTemplate))).appendTo('#'+options.divId);
                    if (options.templateSelectors['filename']) {
                        picture.find(options.templateSelectors['filename']).text(file.name);
                    }
                    if (options.templateSelectors['progressbar']) {
                        picture.find(options.templateSelectors['progressbar']).css(options.progressbarProperty ? options.progressbarProperty : 'width',0);
                    }
                    if (options.templateSelectors['retry']) {
                        picture.find(options.templateSelectors['retry']).hide();
                    }
                    if (options.templateSelectors['delete']) {
                        picture.find(options.templateSelectors['delete']).hide();
                    }
                    if (options.templateSelectors['cancel']) {
                        picture.find(options.templateSelectors['cancel']).show().on('click.ajaxupload', function(e2) {
                            e2.preventDefault();
                            data.abort();

                            $(e.target).trigger('ajaxuploadcancel', [data.files[index]]);

                            if (cleanupUrl = $(this).data('cleanupUrl')) {
                                $.post(cleanupUrl, options.formData);
                            }

                            data.context.remove();
                        });
                    }

                    data.context = data.context.add(
                        picture
                    );
                });

                if (data.autoUpload || (data.autoUpload !== false && $(this).fileupload('option', 'autoUpload'))) {
                    var that = this;

                    var processFunc = false;
                    if (options.previewResize || options.imageResize) {
                        processFunc = function() {
                            return $(that).fileupload('process', data);
                        };
                    }

                    data.process(processFunc).done(function () {
                        data.submit();
                        if (options.templateSelectors['preview']) {
                            data.context.find(options.templateSelectors['preview']).each(function (index, elem) {
                                $(elem).prepend($(data.files[index].preview).addClass('origpreview').addClass('img-responsive'));
                            });
                        }
                    });
                }
            },
            chunkdone: function(e, data) {
                //chunks and multiFileUploads cannot be used together, so don't bother
                if (!data.result[options.inputName][0]) {
                    return;
                }
                if (options.templateSelectors['cancel']) {
                    var cancelButtons = data.context.find(options.templateSelectors['cancel']);
                    if (data.result[options.inputName][0].deleteUrl) {
                        cancelButtons.each(function(index, elem) {
                            $(elem).data('cleanupUrl', data.result[options.inputName][0].deleteUrl);
                        });
                    }
                }
                if (data.result[options.inputName][0].error) {
                    var errorMsg = data.result[options.inputName][0].error;
                    if (errorContainer) {
                        errorContainer.append(document.createTextNode(errorMsg));
                        errorContainer.append('<br/>');
                    }
                    if (!options.removeFailed && options.templateSelectors['error']) {
                        data.context.find(options.templateSelectors['error']).text(errorMsg);
                    }
                    data.context.data('error-set', true);
                    $(e.target).trigger('ajaxuploadfailed', [data.files[0], errorMsg, data.result[options.inputName][0]]);
                    return false;
                }
            },
            chunksend: function(e, data) {
                if (data.context.data('error-set')) {
                    return false;
                }
                return true;
            },
            fail: function(e, data) {
                data.context.each(function(index) {
                    var $this = $(this),
                        errorMsg = options.strings['upload-failed'],
                        errorSet = data.context.data('error-set');

                    if (!errorSet) {
                        $(e.target).trigger('ajaxuploadfailed', [data.files[index], errorMsg]);
                    }

                    data.context.data('error-set', null);

                    if (errorContainer && !errorSet) {
                        errorContainer.append(document.createTextNode(errorMsg));
                        errorContainer.append('<br/>');
                    }

                    if (options.removeFailed) {
                        $this.remove();
                        return;
                    }
                    if (options.templateSelectors['error'] && !errorSet) {
                        $this.find(options.templateSelectors['error']).text(errorMsg);
                    }
                    if (options.templateSelectors['retry']) {
                        $(this).find(options.templateSelectors['retry'])
                            .show()
                            .off('click.ajaxupload')
                            .on('click.ajaxupload', function(e2) {
                                e2.preventDefault();

                                $(e.target).trigger('ajaxuploadretry', [data.files[index]]);

                                if (options.templateSelectors['error']) {
                                    $this.find(options.templateSelectors['error']).text('');
                                }

                                $(this).hide();
                                data.submit();
                            });
                    }
                });
            },
            progress: function(e, data) {
                if (data.context) {
                    var progress = Math.floor(data.loaded / data.total * 100);
                    if (options.templateSelectors['progress']) {
                        data.context.find(options.templateSelectors['progress']).text(progress);
                    }
                    if (options.templateSelectors['progressbar']) {
                        data.context.find(options.templateSelectors['progressbar']).css(options.progressbarProperty ? options.progressbarProperty : 'width',progress+'%');
                    }
                }
            },
            progressall: function(e, data) {
                if (progressbar || progressNum) {
                    var progress = Math.floor(data.loaded / data.total * 100);
                    if (progress <=100 && progress >=0) {
                        progressbar && progressbar.css(options.progressbarAllProperty ? options.progressbarAllProperty : 'width',progress+'%');
                        progressNum && progressNum.text(progress);

                    }
                }
            },
            done: function (e, data) {
                data.context.each(function(index) {
                    var $this = $(this);
                    if (!data.result[options.inputName][index]) {
                        $this.remove();
                        $(e.target).trigger('ajaxuploadfailed', [data.files[index], '', {}]);
                        return;
                    }

                    var file = data.result[options.inputName][index],
                        field = $($.parseHTML('<input type="hidden"/>')).attr('name',options.inputName).val(file.name);

                    if (options.templateSelectors['delete']) {
                        $this.find(options.templateSelectors['delete'])
                            .show()
                            .off('click.ajaxupload')
                            .on('click.ajaxupload',function(e2) {
                                e2.preventDefault();

                                $(e.target).trigger('ajaxuploaddelete', [data.files[index]]);

                                if (file.deleteUrl) {
                                    $.post(file.deleteUrl, options.formData).done(function() {
                                        $this.remove();
                                    }).fail(function() {
                                        console.log("Delete failed!");
                                    });
                                } else {
                                    $this.remove();
                                }
                            });
                    }
                    if (options.templateSelectors['cancel']) {
                        $this.find(options.templateSelectors['cancel']).remove();
                    }

                    if (file.error) {

                        $(e.target).trigger('ajaxuploadfailed', [data.files[index], file.error, file]);

                        if (errorContainer) {
                            errorContainer.append(document.createTextNode(file.error));
                            errorContainer.append('<br/>');
                        }

                        if (options.removeFailed) {
                            $this.remove();
                            return;
                        }
                        if (options.templateSelectors['error']) {
                            $this.find(options.templateSelectors['error']).text(file.error);
                        }
                    } else {
                        $this.append(field);
                        $(e.target).trigger('ajaxuploadsucceeded', [data.files[index], file]);
                    }
                    if (file.thumbnailUrl) {
                        if (options.templateSelectors['preview']) {
                            $this.find(options.templateSelectors['preview'])
                                .find('.origpreview').remove().end()
                                .prepend($('<img src="'+file.thumbnailUrl+'" class="img-responsive"/>'));
                        }
                    }

                    if (options.templateSelectors['filename']) {
                        $this.find(options.templateSelectors['filename']).text(file.name);
                    }
                });
            }
        });

        return this;
    };
}(jQuery));
