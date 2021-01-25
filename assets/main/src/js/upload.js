/* global $, site, autosize, google, baseUrl */
$(function() {
    var commonDefaults = {
        dictDefaultMessage: null,
        dictFallbackMessage: null,
        dictFallbackText: null,
        dictInvalidFileType: null,
        dictFileTooBig: null,
        dictResponseError: null,
        dictCancelUpload: null,
        dictCancelUploadConfirmation: null,
        dictRemoveFile: null,
        dictMaxFilesExceeded: null,
        targetZone: '.file-upload',
        addRemoveLinks: true,
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        parallelUploads: 5,
        maxFiles: 5,
        acceptedFiles: 'image/*',
        previewsContainer: null
    };

    var upload = {
        common: function(config) {
            $.extend(true, commonDefaults, config || {});
        },
        handleErrorSummary: function() {
            var mainErrorBlock = $('#report-create-form > .error__sum');
            $('.field-reportform-pictures').removeClass('has-error').slideUp();
            if ($('.has-error').length > 0) {
                mainErrorBlock.slideDown();
            } else {
                mainErrorBlock.slideUp();
            }
        },
        init: function(config) {
            var $zone = $(config.targetZone);

            config = $.extend(true, {}, commonDefaults, config || {});

            if (!$zone.length) {
                return false;
            }

            var uploadTo = $zone && $zone.data('upload-url');
            var deleteUrl = $zone && $zone.data('delete-url');
            var inputName = $zone && $zone.data('input-name') + '[]';
            // var $errorBlock = $(config.errorBlock);

            if (uploadTo) {
                var settings = {
                    url: uploadTo,
                    removedfile: function(file) {
                        var name = file.name;
                        $.ajax({
                            type: 'POST',
                            url: deleteUrl,
                            data: {
                                fileName: name
                            },
                            dataType: 'html'
                        });

                        var _ref = file.previewElement;
                        return _ref !== null ? _ref.parentNode.removeChild(file.previewElement) : 0;
                    },
                    init: function() {
                        var _this = this;
                        _this.on('complete', function(file) {
                            var previewContainer = $zone;

                            if (config.previewsContainer) {
                                previewContainer = $(config.previewsContainer);
                            }

                            if (file.status === 'success') {
                                previewContainer.find('.dz-preview:last').append('<input type="hidden" name="' + inputName + '" value="' + file.name + '">');
                            }
                        });
                        /*
                         _this.on('error', function(file, errorMessage, xhr) {
                         if ($errorBlock.length) {
                         $errorBlock.append('<div class="error-block">' + errorMessage + '</div>');
                         }
                         });
                         */
                        _this.on('addedfile', function(file) {
                            if (this.files.length) {
                                var i;
                                var len;
                                for (i = 0, len = this.files.length; i < len - 1; i++) {
                                    if (this.files[i].name === file.name && this.files[i].size === file.size && this.files[i].lastModifiedDate.toString() === file.lastModifiedDate.toString()) {
                                        this.removeFile(file);
                                    }
                                }
                            }
                            upload.handleErrorSummary();
                        });
                        _this.on('removedfile', function() {
                        });
                        _this.on('maxfilesexceeded', function(file) {
                            _this.removeFile(file);
                        });
                        _this.on('maxfilesreached', function() {

                        });
                    }
                };
                settings = $.extend(true, {}, settings, config || {});
                $zone.dropzone(settings);
            }

            return true;
        }
    };

    $(function() {
        upload.common({
            dictRemoveFile: '',
            dictMaxFilesExceeded: 'Elérted a képfeltöltés limitet.'
        });
        upload.init({
            targetZone: '.file-upload--report'
        });
        upload.init({
            targetZone: '.file-upload--comment',
            previewsContainer: '.file-upload--previews',
            clickable: '.file-upload--comment, .file-upload--previews',
            errorBlock: '.comment__body .help-block'
        });
    });
});
