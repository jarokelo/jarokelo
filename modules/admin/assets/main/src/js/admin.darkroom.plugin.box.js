(function() {
    'use strict';

    var Box = Darkroom.Transformation.extend({
        applyTransformation: function(canvas, image, next) {
            // Snapshot the image delimited by the box zone
            var snapshot = new Image();
            snapshot.onload = function() {
                // Validate image
                if (height < 1 || width < 1)
                    return;

                var imgInstance = new fabric.Image(this, {
                    // options to make the image static
                    selectable: false,
                    evented: false,
                    lockMovementX: true,
                    lockMovementY: true,
                    lockRotation: true,
                    lockScalingX: true,
                    lockScalingY: true,
                    lockUniScaling: true,
                    hasControls: false,
                    hasBorders: false
                });

                var width = this.width;
                var height = this.height;

                // Update canvas size
                canvas.setWidth(width);
                canvas.setHeight(height);

                // Add image
                image.remove();
                canvas.add(imgInstance);

                next(imgInstance);
            };

            var viewport = Darkroom.Utils.computeImageViewPort(image);
            var imageWidth = viewport.width;
            var imageHeight = viewport.height;

            var left = this.options.left * imageWidth;
            var top = this.options.top * imageHeight;
            var width = Math.min(this.options.width * imageWidth, imageWidth - left);
            var height = Math.min(this.options.height * imageHeight, imageHeight - top);

            var square = new fabric.Rect({
                width: width,
                height: height,
                left: left,
                top: top,
                fill: '#000'
            });

            canvas.add(square);
            canvas.renderAll();

            snapshot.src = canvas.toDataURL();
        }
    });

    var BoxZone = fabric.util.createClass(fabric.Rect, {
        _render: function(ctx) {
            this.callSuper('_render', ctx);

            var canvas = ctx.canvas;
            var dashWidth = 7;

            // Set original scale
            var flipX = this.flipX ? -1 : 1;
            var flipY = this.flipY ? -1 : 1;
            var scaleX = flipX / this.scaleX;
            var scaleY = flipY / this.scaleY;

            ctx.scale(scaleX, scaleY);

            // Overlay rendering
            //ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
            //this._renderOverlay(ctx);

            // Set dashed borders
            if (ctx.setLineDash !== undefined)
                ctx.setLineDash([dashWidth, dashWidth]);
            else if (ctx.mozDash !== undefined)
                ctx.mozDash = [dashWidth, dashWidth];

            // First lines rendering with black
            ctx.strokeStyle = 'rgba(0, 0, 0, 0.2)';
            this._renderBorders(ctx);
            //this._renderGrid(ctx);

            // Re render lines in white
            ctx.lineDashOffset = dashWidth;
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.4)';
            this._renderBorders(ctx);
            //this._renderGrid(ctx);

            // Reset scale
            ctx.scale(1/scaleX, 1/scaleY);
        },

        _renderOverlay: function(ctx) {
            var canvas = ctx.canvas;
            var borderOffset = 0;

            //
            //    x0    x1        x2      x3
            // y0 +------------------------+
            //    |\\\\\\\\\\\\\\\\\\\\\\\\|
            //    |\\\\\\\\\\\\\\\\\\\\\\\\|
            // y1 +------+---------+-------+
            //    |\\\\\\|         |\\\\\\\|
            //    |\\\\\\|    0    |\\\\\\\|
            //    |\\\\\\|         |\\\\\\\|
            // y2 +------+---------+-------+
            //    |\\\\\\\\\\\\\\\\\\\\\\\\|
            //    |\\\\\\\\\\\\\\\\\\\\\\\\|
            // y3 +------------------------+
            //

            var x0 = Math.ceil(-this.getWidth() / 2 - this.getLeft());
            var x1 = Math.ceil(-this.getWidth() / 2);
            var x2 = Math.ceil(this.getWidth() / 2);
            var x3 = Math.ceil(this.getWidth() / 2 + (canvas.width - this.getWidth() - this.getLeft()));

            var y0 = Math.ceil(-this.getHeight() / 2 - this.getTop());
            var y1 = Math.ceil(-this.getHeight() / 2);
            var y2 = Math.ceil(this.getHeight() / 2);
            var y3 = Math.ceil(this.getHeight() / 2 + (canvas.height - this.getHeight() - this.getTop()));

            // Upper rect
            ctx.fillRect(x0, y0, x3 - x0, y1 - y0 + borderOffset);

            // Left rect
            ctx.fillRect(x0, y1, x1 - x0, y2 - y1 + borderOffset);

            // Right rect
            ctx.fillRect(x2, y1, x3 - x2, y2 - y1 + borderOffset);

            // Down rect
            ctx.fillRect(x0, y2, x3 - x0, y3 - y2);
        },

        _renderBorders: function(ctx) {
            ctx.beginPath();
            ctx.moveTo(-this.getWidth()/2, -this.getHeight()/2); // upper left
            ctx.lineTo(this.getWidth()/2, -this.getHeight()/2); // upper right
            ctx.lineTo(this.getWidth()/2, this.getHeight()/2); // down right
            ctx.lineTo(-this.getWidth()/2, this.getHeight()/2); // down left
            ctx.lineTo(-this.getWidth()/2, -this.getHeight()/2); // upper left
            ctx.stroke();
        },

        _renderGrid: function(ctx) {
            // Vertical lines
            ctx.beginPath();
            ctx.moveTo(-this.getWidth()/2 + 1/3 * this.getWidth(), -this.getHeight()/2);
            ctx.lineTo(-this.getWidth()/2 + 1/3 * this.getWidth(), this.getHeight()/2);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(-this.getWidth()/2 + 2/3 * this.getWidth(), -this.getHeight()/2);
            ctx.lineTo(-this.getWidth()/2 + 2/3 * this.getWidth(), this.getHeight()/2);
            ctx.stroke();
            // Horizontal lines
            ctx.beginPath();
            ctx.moveTo(-this.getWidth()/2, -this.getHeight()/2 + 1/3 * this.getHeight());
            ctx.lineTo(this.getWidth()/2, -this.getHeight()/2 + 1/3 * this.getHeight());
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(-this.getWidth()/2, -this.getHeight()/2 + 2/3 * this.getHeight());
            ctx.lineTo(this.getWidth()/2, -this.getHeight()/2 + 2/3 * this.getHeight());
            ctx.stroke();
        }
    });

    Darkroom.plugins['box'] = Darkroom.Plugin.extend({
        // Init point
        startX: null,
        startY: null,

        // Keybox
        isKeyBoxing: false,
        isKeyLeft: false,
        isKeyUp: false,

        defaults: {
            // min box dimension
            minHeight: 1,
            minWidth: 1,
            // ensure box ratio
            ratio: null,
            // quick box feature (set a key code to enable it)
            quickBoxKey: false
        },

        initialize: function InitDarkroomBoxPlugin() {
            var buttonGroup = this.darkroom.toolbar.createButtonGroup();

            this.boxButton = buttonGroup.createButton({
                image: 'box'
            });
            this.okButton = buttonGroup.createButton({
                image: 'done',
                type: 'success',
                hide: true
            });
            this.cancelButton = buttonGroup.createButton({
                image: 'close',
                type: 'danger',
                hide: true
            });

            // Buttons click
            this.boxButton.addEventListener('click', this.toggleBox.bind(this));
            this.okButton.addEventListener('click', this.boxCurrentZone.bind(this));
            this.cancelButton.addEventListener('click', this.releaseFocus.bind(this));

            // Canvas events
            this.darkroom.canvas.on('mouse:down', this.onMouseDown.bind(this));
            this.darkroom.canvas.on('mouse:move', this.onMouseMove.bind(this));
            this.darkroom.canvas.on('mouse:up', this.onMouseUp.bind(this));
            this.darkroom.canvas.on('object:moving', this.onObjectMoving.bind(this));
            this.darkroom.canvas.on('object:scaling', this.onObjectScaling.bind(this));

            fabric.util.addListener(fabric.document, 'keydown', this.onKeyDown.bind(this));
            fabric.util.addListener(fabric.document, 'keyup', this.onKeyUp.bind(this));

            this.darkroom.addEventListener('core:transformation', this.releaseFocus.bind(this));
        },

        // Avoid box zone to go beyond the canvas edges
        onObjectMoving: function(event) {
            if (!this.hasFocus()) {
                return;
            }

            var currentObject = event.target;
            if (currentObject !== this.boxZone)
                return;

            var canvas = this.darkroom.canvas;
            var x = currentObject.getLeft(), y = currentObject.getTop();
            var w = currentObject.getWidth(), h = currentObject.getHeight();
            var maxX = canvas.getWidth() - w;
            var maxY = canvas.getHeight() - h;

            if (x < 0)
                currentObject.set('left', 0);
            if (y < 0)
                currentObject.set('top', 0);
            if (x > maxX)
                currentObject.set('left', maxX);
            if (y > maxY)
                currentObject.set('top', maxY);

            this.darkroom.dispatchEvent('box:update');
        },

        // Prevent box zone from going beyond the canvas edges (like mouseMove)
        onObjectScaling: function(event) {
            if (!this.hasFocus()) {
                return;
            }

            var preventScaling = false;
            var currentObject = event.target;
            if (currentObject !== this.boxZone)
                return;

            var canvas = this.darkroom.canvas;
            var pointer = canvas.getPointer(event.e);
            var x = pointer.x;
            var y = pointer.y;

            var minX = currentObject.getLeft();
            var minY = currentObject.getTop();
            var maxX = currentObject.getLeft() + currentObject.getWidth();
            var maxY = currentObject.getTop() + currentObject.getHeight();

            if (null !== this.options.ratio) {
                if (minX < 0 || maxX > canvas.getWidth() || minY < 0 || maxY > canvas.getHeight()) {
                    preventScaling = true;
                }
            }

            if (minX < 0 || maxX > canvas.getWidth() || preventScaling) {
                var lastScaleX = this.lastScaleX || 1;
                currentObject.setScaleX(lastScaleX);
            }
            if (minX < 0) {
                currentObject.setLeft(0);
            }

            if (minY < 0 || maxY > canvas.getHeight() || preventScaling) {
                var lastScaleY = this.lastScaleY || 1;
                currentObject.setScaleY(lastScaleY);
            }
            if (minY < 0) {
                currentObject.setTop(0);
            }

            if (currentObject.getWidth() < this.options.minWidth) {
                currentObject.scaleToWidth(this.options.minWidth);
            }
            if (currentObject.getHeight() < this.options.minHeight) {
                currentObject.scaleToHeight(this.options.minHeight);
            }

            this.lastScaleX = currentObject.getScaleX();
            this.lastScaleY = currentObject.getScaleY();

            this.darkroom.dispatchEvent('box:update');
        },

        // Init box zone
        onMouseDown: function(event) {
            if (!this.hasFocus()) {
                return;
            }

            var canvas = this.darkroom.canvas;

            // recalculate offset, in case canvas was manipulated since last `calcOffset`
            canvas.calcOffset();
            var pointer = canvas.getPointer(event.e);
            var x = pointer.x;
            var y = pointer.y;
            var point = new fabric.Point(x, y);

            // Check if user want to scale or drag the box zone.
            var activeObject = canvas.getActiveObject();
            if (activeObject === this.boxZone || this.boxZone.containsPoint(point)) {
                return;
            }

            canvas.discardActiveObject();
            this.boxZone.setWidth(0);
            this.boxZone.setHeight(0);
            this.boxZone.setScaleX(1);
            this.boxZone.setScaleY(1);

            this.startX = x;
            this.startY = y;
        },

        // Extend box zone
        onMouseMove: function(event) {
            // Quick box feature
            if (this.isKeyBoxing)
                return this.onMouseMoveKeyBox(event);

            if (null === this.startX || null === this.startY) {
                return;
            }

            var canvas = this.darkroom.canvas;
            var pointer = canvas.getPointer(event.e);
            var x = pointer.x;
            var y = pointer.y;

            this._renderBoxZone(this.startX, this.startY, x, y);
        },

        onMouseMoveKeyBox: function(event) {
            var canvas = this.darkroom.canvas;
            var zone = this.boxZone;

            var pointer = canvas.getPointer(event.e);
            var x = pointer.x;
            var y = pointer.y;

            if (!zone.left || !zone.top) {
                zone.setTop(y);
                zone.setLeft(x);
            }

            this.isKeyLeft =  x < zone.left + zone.width / 2 ;
            this.isKeyUp = y < zone.top + zone.height / 2 ;

            this._renderBoxZone(
                Math.min(zone.left, x),
                Math.min(zone.top, y),
                Math.max(zone.left+zone.width, x),
                Math.max(zone.top+zone.height, y)
            );
        },

        // Finish box zone
        onMouseUp: function(event) {
            if (null === this.startX || null === this.startY) {
                return;
            }

            var canvas = this.darkroom.canvas;
            this.boxZone.setCoords();
            canvas.setActiveObject(this.boxZone);
            canvas.calcOffset();

            this.startX = null;
            this.startY = null;
        },

        onKeyDown: function(event) {
            if (false === this.options.quickBoxKey || event.keyCode !== this.options.quickBoxKey || this.isKeyBoxing)
                return;

            // Active quick box flow
            this.isKeyBoxing = true ;
            this.darkroom.canvas.discardActiveObject();
            this.boxZone.setWidth(0);
            this.boxZone.setHeight(0);
            this.boxZone.setScaleX(1);
            this.boxZone.setScaleY(1);
            this.boxZone.setTop(0);
            this.boxZone.setLeft(0);
        },

        onKeyUp: function(event) {
            if (false === this.options.quickBoxKey || event.keyCode !== this.options.quickBoxKey || !this.isKeyBoxing)
                return;

            // Unactive quick box flow
            this.isKeyBoxing = false;
            this.startX = 1;
            this.startY = 1;
            this.onMouseUp();
        },

        selectZone: function(x, y, width, height, forceDimension) {
            if (!this.hasFocus())
                this.requireFocus();

            if (!forceDimension) {
                this._renderBoxZone(x, y, x+width, y+height);
            } else {
                this.boxZone.set({
                    'left': x,
                    'top': y,
                    'width': width,
                    'height': height
                });
            }

            var canvas = this.darkroom.canvas;
            canvas.bringToFront(this.boxZone);
            this.boxZone.setCoords();
            canvas.setActiveObject(this.boxZone);
            canvas.calcOffset();

            this.darkroom.dispatchEvent('box:update');
        },

        toggleBox: function() {
            if (!this.hasFocus())
                this.requireFocus();
            else
                this.releaseFocus();
        },

        boxCurrentZone: function() {
            if (!this.hasFocus())
                return;

            // Avoid boxing empty zone
            if (this.boxZone.width < 1 && this.boxZone.height < 1)
                return;

            var image = this.darkroom.image;

            // Compute box zone dimensions
            var top = this.boxZone.getTop() - image.getTop();
            var left = this.boxZone.getLeft() - image.getLeft();
            var width = this.boxZone.getWidth();
            var height = this.boxZone.getHeight();

            // Adjust dimensions to image only
            if (top < 0) {
                height += top;
                top = 0;
            }

            if (left < 0) {
                width += left;
                left = 0;
            }

            // Apply box transformation.
            // Make sure to use relative dimension since the box will be applied
            // on the source image.
            this.darkroom.applyTransformation(new Box({
                top: top / image.getHeight(),
                left: left / image.getWidth(),
                width: width / image.getWidth(),
                height: height / image.getHeight(),
            }));
        },

        // Test wether box zone is set
        hasFocus: function() {
            return this.boxZone !== undefined;
        },

        // Create the box zone
        requireFocus: function() {
            this.boxZone = new BoxZone({
                fill: 'black',
                hasBorders: false,
                originX: 'left',
                originY: 'top',
                //stroke: '#444',
                //strokeDashArray: [5, 5],
                //borderColor: '#444',
                cornerColor: '#444',
                cornerSize: 8,
                transparentCorners: false,
                lockRotation: true,
                hasRotatingPoint: false,
            });

            if (null !== this.options.ratio) {
                this.boxZone.set('lockUniScaling', true);
            }

            this.darkroom.canvas.add(this.boxZone);
            this.darkroom.canvas.defaultCursor = 'crosshair';

            this.boxButton.active(true);
            this.okButton.hide(false);
            this.cancelButton.hide(false);
        },

        // Remove the box zone
        releaseFocus: function() {
            if (undefined === this.boxZone)
                return;

            this.boxZone.remove();
            this.boxZone = undefined;

            this.boxButton.active(false);
            this.okButton.hide(true);
            this.cancelButton.hide(true);

            this.darkroom.canvas.defaultCursor = 'default';

            this.darkroom.dispatchEvent('box:update');
        },

        _renderBoxZone: function(fromX, fromY, toX, toY) {
            var canvas = this.darkroom.canvas;

            var isRight = (toX > fromX);
            var isLeft = !isRight;
            var isDown = (toY > fromY);
            var isUp = !isDown;

            var minWidth = Math.min(+this.options.minWidth, canvas.getWidth());
            var minHeight = Math.min(+this.options.minHeight, canvas.getHeight());

            // Define corner coordinates
            var leftX = Math.min(fromX, toX);
            var rightX = Math.max(fromX, toX);
            var topY = Math.min(fromY, toY);
            var bottomY = Math.max(fromY, toY);

            // Replace current point into the canvas
            leftX = Math.max(0, leftX);
            rightX = Math.min(canvas.getWidth(), rightX);
            topY = Math.max(0, topY)
            bottomY = Math.min(canvas.getHeight(), bottomY);

            // Recalibrate coordinates according to given options
            if (rightX - leftX < minWidth) {
                if (isRight)
                    rightX = leftX + minWidth;
                else
                    leftX = rightX - minWidth;
            }
            if (bottomY - topY < minHeight) {
                if (isDown)
                    bottomY = topY + minHeight;
                else
                    topY = bottomY - minHeight;
            }

            // Truncate truncate according to canvas dimensions
            if (leftX < 0) {
                // Translate to the left
                rightX += Math.abs(leftX);
                leftX = 0
            }
            if (rightX > canvas.getWidth()) {
                // Translate to the right
                leftX -= (rightX - canvas.getWidth());
                rightX = canvas.getWidth();
            }
            if (topY < 0) {
                // Translate to the bottom
                bottomY += Math.abs(topY);
                topY = 0
            }
            if (bottomY > canvas.getHeight()) {
                // Translate to the right
                topY -= (bottomY - canvas.getHeight());
                bottomY = canvas.getHeight();
            }

            var width = rightX - leftX;
            var height = bottomY - topY;
            var currentRatio = width / height;

            if (this.options.ratio && +this.options.ratio !== currentRatio) {
                var ratio = +this.options.ratio;

                if(this.isKeyBoxing) {
                    isLeft = this.isKeyLeft;
                    isUp = this.isKeyUp;
                }

                if (currentRatio < ratio) {
                    var newWidth = height * ratio;
                    if (isLeft) {
                        leftX -= (newWidth - width);
                    }
                    width = newWidth;
                } else if (currentRatio > ratio) {
                    var newHeight = height / (ratio * height/width);
                    if (isUp) {
                        topY -= (newHeight - height);
                    }
                    height = newHeight;
                }

                if (leftX < 0) {
                    leftX = 0;
                    //TODO
                }
                if (topY < 0) {
                    topY = 0;
                    //TODO
                }
                if (leftX + width > canvas.getWidth()) {
                    var newWidth = canvas.getWidth() - leftX;
                    height = newWidth * height / width;
                    width = newWidth;
                    if (isUp) {
                        topY = fromY - height;
                    }
                }
                if (topY + height > canvas.getHeight()) {
                    var newHeight = canvas.getHeight() - topY;
                    width = width * newHeight / height;
                    height = newHeight;
                    if (isLeft) {
                        leftX = fromX - width;
                    }
                }
            }

            // Apply coordinates
            this.boxZone.left = leftX;
            this.boxZone.top = topY;
            this.boxZone.width = width;
            this.boxZone.height = height;

            this.darkroom.canvas.bringToFront(this.boxZone);

            this.darkroom.dispatchEvent('box:update');
        }
    });

})();

