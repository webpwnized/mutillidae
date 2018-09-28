/**
 * Hover balloon on elements without css and images.
 *
 * Copyright (c) 2011 Hayato Takenaka
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * @author: Hayato Takenaka (http://urin.take-uma.net)
 * @version: 0.3.0 - 2012/02/25
**/
;(function($) {
	//-----------------------------------------------------------------------------
	// Private
	//-----------------------------------------------------------------------------
	// Helper for meta programming
	var Meta = {};
	Meta.pos  = $.extend(["top", "bottom", "left", "right"], {camel: ["Top", "Bottom", "Left", "Right"]});
	Meta.size = $.extend(["height", "width"], {camel: ["Height", "Width"]});
	Meta.getRelativeNames = function(position) {
		var idx = {
			pos: {
				o: position,                                          // origin
				f: (position % 2 == 0) ? position + 1 : position - 1, // faced
				p1: (position % 2 == 0) ? position : position - 1,
				p2: (position % 2 == 0) ? position + 1 : position,
				c1: (position < 2) ? 2 : 0,
				c2: (position < 2) ? 3 : 1
			},
			size: {
				p: (position < 2) ? 0 : 1, // parallel
				c: (position < 2) ? 1 : 0  // cross
			}
		};
		var names = {};
		for(var m1 in idx) {
			if(!names[m1]) names[m1] = {};
			for(var m2 in idx[m1]) {
				names[m1][m2] = Meta[m1][idx[m1][m2]];
				if(!names.camel) names.camel = {};
				if(!names.camel[m1]) names.camel[m1] = {};
				names.camel[m1][m2] = Meta[m1].camel[idx[m1][m2]];
			}
		}
		names.isTopLeft = (names.pos.o == names.pos.p1);
		return names;
	};

	// Helper class to handle position and size as numerical pixels.
	function NumericalBoxElement() { this.initialize.apply(this, arguments); }
	(function() {
		// Method factories
		var Methods = {
			setBorder: function(pos, isVertical) {
				return function(value) {
					this.$.css("border-" + pos.toLowerCase() + "-width", value + "px");
					this["border" + pos] = value;
					return (this.isActive) ? digitalize(this, isVertical) : this;
				};
			},
			setPosition: function(pos, isVertical) {
				return function(value) {
					this.$.css(pos.toLowerCase(), value + "px");
					this[pos.toLowerCase()] = value;
					return (this.isActive) ? digitalize(this, isVertical) : this;
				};
			}
		};

		NumericalBoxElement.prototype = {
			initialize: function($element) {
				this.$ = $element;
				$.extend(true, this, this.$.offset(), {center: {}, inner: {center: {}}});
				for(var i = 0; i < Meta.pos.length; i++) {
					this["border" + Meta.pos.camel[i]] = parseInt(this.$.css("border-" + Meta.pos[i] + "-width")) || 0;
				}
				this.active();
			},
			active: function() { this.isActive = true; digitalize(this); return this; },
			inactive: function() { this.isActive = false; return this; }
		};
		for(var i = 0; i < Meta.pos.length; i++) {
			NumericalBoxElement.prototype["setBorder" + Meta.pos.camel[i]] = Methods.setBorder(Meta.pos.camel[i], (i < 2));
			if(i % 2 == 0)
				NumericalBoxElement.prototype["set" + Meta.pos.camel[i]] = Methods.setPosition(Meta.pos.camel[i], (i < 2));
		}

		function digitalize(box, isVertical) {
			if(isVertical == undefined) { digitalize(box, true); return digitalize(box, false); }
			var m = Meta.getRelativeNames((isVertical) ? 0 : 2);
			box[m.size.p] = box.$["outer" + m.camel.size.p]();
			box[m.pos.f] = box[m.pos.o] + box[m.size.p];
			box.center[m.pos.o] = box[m.pos.o] + box[m.size.p] / 2;
			box.inner[m.pos.o] = box[m.pos.o] + box["border" + m.camel.pos.o];
			box.inner[m.size.p] = box.$["inner" + m.camel.size.p]();
			box.inner[m.pos.f] = box.inner[m.pos.o] + box.inner[m.size.p];
			box.inner.center[m.pos.o] = box.inner[m.pos.f] + box.inner[m.size.p] / 2;
			return box;
		}
	})();

	// Adjust position of balloon body
	function makeupBalloon($target, $balloon, options) {
		$balloon.stop(true, true);
		var outerTip, innerTip,
			initTipStyle = {position: "absolute", height: "0", width: "0", border: "solid 0 transparent"},
			target = new NumericalBoxElement($target),
			balloon = new NumericalBoxElement($balloon);
		balloon.setTop(-options.offsetY
			+ ((options.position && options.position.indexOf("top") >= 0) ? target.top - balloon.height
			: ((options.position && options.position.indexOf("bottom") >= 0) ? target.bottom
			: target.center.top - balloon.height / 2)));
		balloon.setLeft(options.offsetX
			+ ((options.position && options.position.indexOf("left") >= 0) ? target.left - balloon.width
			: ((options.position && options.position.indexOf("right") >= 0) ? target.right
			: target.center.left - balloon.width / 2)));
		if(options.tipSize > 0) {
			// Add hidden balloon tips into balloon body.
			if($balloon.data("outerTip")) { $balloon.data("outerTip").remove(); $balloon.removeData("outerTip"); }
			if($balloon.data("innerTip")) { $balloon.data("innerTip").remove(); $balloon.removeData("innerTip"); }
			outerTip = new NumericalBoxElement($("<div>").css(initTipStyle).appendTo($balloon));
			innerTip = new NumericalBoxElement($("<div>").css(initTipStyle).appendTo($balloon));
			// Make tip triangle, adjust position of tips.
			var m;
			for(var i = 0; i < Meta.pos.length; i++) {
				m = Meta.getRelativeNames(i);
				if(balloon.center[m.pos.c1] >= target[m.pos.c1] &&
					balloon.center[m.pos.c1] <= target[m.pos.c2]) {
					if(i % 2 == 0) {
						if(balloon[m.pos.o] >= target[m.pos.o] && balloon[m.pos.f] >= target[m.pos.f]) break;
					} else {
						if(balloon[m.pos.o] <= target[m.pos.o] && balloon[m.pos.f] <= target[m.pos.f]) break;
					}
				}
				m = null;
			}
			if(m) {
				balloon["set" + m.camel.pos.p1]
					(balloon[m.pos.p1] + ((m.isTopLeft) ? 1 : -1) * (options.tipSize - balloon["border" + m.camel.pos.o]));
				makeTip(balloon, outerTip, m, options.tipSize, $balloon.css("border-" + m.pos.o + "-color"));
				makeTip(balloon, innerTip, m, options.tipSize - 2 * balloon["border" + m.camel.pos.o], $balloon.css("background-color"));
				$balloon.data("outerTip", outerTip.$).data("innerTip", innerTip.$);
			} else {
				$.each([outerTip.$, innerTip.$], function() { this.remove(); });
			}
		}
		// Make up balloon tip.
		function makeTip(balloon, tip, m, tipSize, color) {
			var len = Math.round(tipSize / 1.7320508);
			tip.inactive()
				["setBorder" + m.camel.pos.f](tipSize)
				["setBorder" + m.camel.pos.c1](len)
				["setBorder" + m.camel.pos.c2](len)
				["set" + m.camel.pos.p1]((m.isTopLeft) ? -tipSize : balloon.inner[m.size.p])
				["set" + m.camel.pos.c1](balloon.inner[m.size.c] / 2 - len)
				.active()
				.$.css("border-" + m.pos.f + "-color", color);
		}
	}

	// True if the event comes from the target or balloon.
	function isValidTargetEvent($target, e) {
		var b = $target.data("balloon") && $target.data("balloon").get(0);
		return !(b && (b == e.relatedTarget || $.contains(b, e.relatedTarget)));
	}

	//-----------------------------------------------------------------------------
	// Public
	//-----------------------------------------------------------------------------
	$.fn.balloon = function(options) {
		options = $.extend(true, {}, $.balloon.defaults, options);
		return this.one("mouseenter", function(e) {
			var $target = $(this), t = this;
			var $balloon = $target.unbind("mouseenter", arguments.callee)
				.showBalloon(options).mouseenter(function(e) {
					isValidTargetEvent($target, e) && $target.showBalloon();
				}).data("balloon");
			if($balloon) {
				$balloon.mouseleave(function(e) {
					if(t == e.relatedTarget || $.contains(t, e.relatedTarget)) return;
					$target.hideBalloon();
				}).mouseenter(function(e) { $target.showBalloon(); });
			}
		}).mouseleave(function(e) {
			var $target = $(this);
			isValidTargetEvent($target, e) && $target.hideBalloon();
		});
	};

	$.fn.showBalloon = function(options) {
		var $target, $balloon, offTimer;
		if(!$.balloon.defaults.css) $.balloon.defaults.css = {};
		if(options || !this.data("options"))
			this.data("options", $.extend(true, {}, $.balloon.defaults, options));
		options = this.data("options");
		return this.each(function() {
			$target = $(this);
			(offTimer = $target.data("offTimer")) && clearTimeout(offTimer);
			var contents = $.isFunction(options.contents)
				? options.contents()
				: (options.contents || $target.attr("title"));
			var isNew = !($balloon = $target.data("balloon"));
			if(isNew) $balloon = $("<div>").append(contents);
			if(!options.url && (!$balloon || $balloon.html() == "")) return;
			if(!isNew && contents && contents != $balloon.html()) $balloon.empty().append(contents);
			$target.removeAttr("title");
			if(options.url) {
				$balloon.load($.isFunction(options.url) ? options.url(this) : options.url, function(res, sts, xhr) {
					if(options.ajaxComplete) options.ajaxComplete(res, sts, xhr);
					makeupBalloon($target, $balloon, options);
				});
			}
			if(isNew) {
				$balloon
					.addClass(options.classname)
					.css(options.css)
					.css({visibility: "hidden", position: "absolute"})
					.appendTo("body");
				$target.data("balloon", $balloon);
				makeupBalloon($target, $balloon, options);
				$balloon.hide().css("visibility", "visible");
			} else {
				makeupBalloon($target, $balloon, options);
			}
			$target.data("onTimer", setTimeout(function() {
				if(options.showAnimation) {
					options.showAnimation.apply($balloon.stop(true, true), [options.showDuration]);
				} else {
					$balloon.show(options.showDuration, function() {
						if(this.style.removeAttribute) { this.style.removeAttribute("filter"); }
					});
				}
			}, options.delay));
		});
	};

	$.fn.hideBalloon = function() {
		var options = this.data("options"), onTimer, offTimer;
		return this.each(function() {
			var $target = $(this);
			(onTimer = $target.data("onTimer")) && clearTimeout(onTimer);
			(offTimer = $target.data("offTimer")) && clearTimeout(offTimer);
			$target.data("offTimer", setTimeout(function() {
				var $balloon = $target.data("balloon");
				if(options.hideAnimation) {
					$balloon && options.hideAnimation.apply($balloon.stop(true, true), [options.hideDuration]);
				} else {
					$balloon && $balloon.stop(true, true).hide(options.hideDuration);
				}
			},
			options.minLifetime));
		});
	};

	$.balloon = {
		defaults: {
			contents: null, url: null, ajaxComplete: null, classname: null,
			position: "top", offsetX: 0, offsetY: 0, tipSize: 12,
			delay: 0, minLifetime: 200,
			showDuration: 100, showAnimation: null,
			hideDuration:  80, hideAnimation: function(d) { this.fadeOut(d); },
			css: {
				minWidth       : "20px",
				padding        : "5px",
				borderRadius   : "6px",
				border         : "solid 1px #777",
				boxShadow      : "4px 4px 4px #555",
				//color          : "#666",
				backgroundColor: "#efefef",
				//opacity        : ($.support.opacity) ? "0.85" : null,
				zIndex         : "32767",
				textAlign      : "left",
				/* Defaults altered and added by Jeremy Druin */
				opacity        : ($.support.opacity) ? "1.0" : null,
				maxWidth		: "500px",
				fontWeight:"bold",
				color:"#000000"
			}
		}
	};
})(jQuery);
