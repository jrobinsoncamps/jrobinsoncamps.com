/* Gallery (slide-on-click, auto-slide-left) */
jQuery.fn.gallSlide = function(_options){
	// defaults options	
	var _options = jQuery.extend({
		duration: 700,
		autoSlide: 5000
	},_options);

	return this.each(function(){
		var _hold = $(this);
		var _speed = _options.duration;
		var _timer = _options.autoSlide;
		var _wrap = _hold.find('ul');
		var _el = _hold.find('ul > li');
		var _next = _hold.find('a.link-next');
		var _prev = _hold.find('a.link-prev');
		var _count = _el.index(_el.filter(':last'));
		var _w = _el.outerWidth();
		var _wrapHolderW = Math.ceil(_wrap.parent().width()/_w);
		var _t;
		var _active = 0;
		function scrollEl(){
			_wrap.eq(0).animate({
				marginLeft: -(_w * _active) + "px"
			}, {queue:false, duration: _speed});
		}
		function runTimer(){
			_t = setInterval(function(){
				_active++;
				if (_active > (_count - _wrapHolderW + 1)) _active = 0;
				scrollEl();
			}, _timer);
		}
		runTimer();
		_next.click(function(){
			_active++;
			if(_t) clearTimeout(_t);
			if (_active > (_count - _wrapHolderW + 1)) _active = 0;
			scrollEl();
			runTimer();
			return false;
		});
		_prev.click(function(){
			_active--;
			if(_t) clearTimeout(_t);
			if (_active < 0) _active = _count - _wrapHolderW + 1;
			scrollEl();
			runTimer();
			return false;
		});
	});
}

$(document).ready(function(){
	$('div#gallery').gallSlide({
		duration: 700,
		autoSlide: 5000
	});
});
